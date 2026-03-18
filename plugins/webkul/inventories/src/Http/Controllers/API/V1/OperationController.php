<?php

namespace Webkul\Inventory\Http\Controllers\API\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use Webkul\Inventory\Enums\MoveState;
use Webkul\Inventory\Enums\OperationState;
use Webkul\Inventory\Enums\OperationType as OperationTypeEnum;
use Webkul\Inventory\Enums\ProcureMethod;
use Webkul\Inventory\Facades\Inventory;
use Webkul\Inventory\Http\Requests\OperationRequest;
use Webkul\Inventory\Http\Resources\V1\OperationResource;
use Webkul\Inventory\Models\Operation;
use Webkul\Inventory\Models\OperationType;
use Webkul\Inventory\Models\Product;
use Webkul\Support\Models\UOM;

class OperationController extends Controller
{
    protected array $allowedIncludes = [
        'user',
        'owner',
        'operationType',
        'sourceLocation',
        'destinationLocation',
        'backOrderOf',
        'return',
        'partner',
        'company',
        'creator',
        'moves',
        'moves.product',
        'moves.uom',
        'moves.sourceLocation',
        'moves.destinationLocation',
        'moves.finalLocation',
        'moves.operationType',
        'moveLines',
    ];

    protected function listOperations()
    {
        $operations = QueryBuilder::for($this->modelClass()::query())
            ->whereHas('operationType', fn ($query) => $query->where('type', $this->operationType()))
            ->allowedFilters([
                AllowedFilter::exact('id'),
                AllowedFilter::partial('name'),
                AllowedFilter::exact('state'),
                AllowedFilter::exact('move_type'),
                AllowedFilter::exact('partner_id'),
                AllowedFilter::exact('user_id'),
                AllowedFilter::exact('company_id'),
                AllowedFilter::exact('operation_type_id'),
            ])
            ->allowedSorts([
                'id',
                'name',
                'state',
                'scheduled_at',
                'deadline',
                'created_at',
                'updated_at',
            ])
            ->allowedIncludes($this->allowedIncludes)
            ->paginate();

        return $this->resourceClass()::collection($operations);
    }

    protected function findOperationForShow(string $id): Operation
    {
        $operation = QueryBuilder::for($this->modelClass()::query()->where('id', $id))
            ->allowedIncludes($this->allowedIncludes)
            ->firstOrFail();

        $this->ensureOperationTypeMatches($operation);

        return $operation;
    }

    protected function showOperation(string $id)
    {
        return new ($this->resourceClass())($this->findOperationForShow($id));
    }

    protected function findOperationById(string $id): Operation
    {
        $operation = $this->modelClass()::query()->findOrFail($id);
        $this->ensureOperationTypeMatches($operation);

        return $operation;
    }

    protected function deleteOperationById(string $id)
    {
        $operation = $this->findOperationById($id);
        $operation->delete();

        return response()->json([
            'message' => $this->deletedMessage(),
        ]);
    }

    protected function checkAvailabilityById(string $id): Operation
    {
        $operation = $this->findOperationById($id);
        $operation = Inventory::checkTransferAvailability($operation);

        return $operation->refresh()->load($this->allowedIncludes);
    }

    protected function todoById(string $id): Operation
    {
        $operation = $this->findOperationById($id);
        $operation = Inventory::todoTransfer($operation);

        return $operation->refresh()->load($this->allowedIncludes);
    }

    protected function validateById(string $id): Operation
    {
        $operation = $this->findOperationById($id);
        $operation = Inventory::validateTransfer($operation);

        return $operation->refresh()->load($this->allowedIncludes);
    }

    protected function cancelById(string $id): Operation
    {
        $operation = $this->findOperationById($id);
        $operation = Inventory::cancelTransfer($operation);

        return $operation->refresh()->load($this->allowedIncludes);
    }

    protected function returnById(string $id): Operation
    {
        $operation = $this->findOperationById($id);
        $newOperation = Inventory::returnTransfer($operation);

        return $newOperation->refresh()->load($this->allowedIncludes);
    }

    protected function ensureCanCheckAvailability(Operation $operation): ?JsonResponse
    {
        if (! in_array($operation->state, [OperationState::CONFIRMED, OperationState::ASSIGNED], true)) {
            return $this->actionValidationError('Only confirmed or assigned operations can check availability.');
        }

        $hasEligibleMoves = $operation->moves()
            ->whereIn('state', [MoveState::CONFIRMED, MoveState::PARTIALLY_ASSIGNED])
            ->exists();

        if (! $hasEligibleMoves) {
            return $this->actionValidationError('No operation moves are eligible for availability check.');
        }

        return null;
    }

    protected function ensureCanTodo(Operation $operation): ?JsonResponse
    {
        if ($operation->state !== OperationState::DRAFT) {
            return $this->actionValidationError('Only draft operations can be set to todo.');
        }

        if (! $operation->moves()->exists()) {
            return $this->actionValidationError('Cannot set operation to todo without moves.');
        }

        return null;
    }

    protected function ensureCanValidate(Operation $operation): ?JsonResponse
    {
        if (in_array($operation->state, [OperationState::DONE, OperationState::CANCELED], true)) {
            return $this->actionValidationError('Only non-done and non-canceled operations can be validated.');
        }

        return null;
    }

    protected function ensureCanCancel(Operation $operation): ?JsonResponse
    {
        if (in_array($operation->state, [OperationState::DONE, OperationState::CANCELED], true)) {
            return $this->actionValidationError('Only non-done and non-canceled operations can be canceled.');
        }

        return null;
    }

    protected function ensureCanReturn(Operation $operation): ?JsonResponse
    {
        if ($operation->state !== OperationState::DONE) {
            return $this->actionValidationError('Only done operations can be returned.');
        }

        return null;
    }

    protected function actionValidationError(string $message): JsonResponse
    {
        return response()->json([
            'message' => $message,
        ], 422);
    }

    protected function createOperation(OperationRequest $request)
    {
        $validatedData = $request->validated();

        return DB::transaction(function () use ($validatedData) {
            [$operationData, $movesData] = $this->splitOperationData($validatedData);
            $operationData = $this->prepareOperationData($operationData);

            /** @var Operation $operation */
            $operation = $this->modelClass()::query()->create($operationData);

            if ($movesData !== null) {
                $this->syncMoves($operation, $movesData);
            }

            $operation->load($this->allowedIncludes);

            return (new ($this->resourceClass())($operation))
                ->additional(['message' => $this->createdMessage()])
                ->response()
                ->setStatusCode(201);
        });
    }

    protected function updateOperationById(OperationRequest $request, string $id)
    {
        $operation = $this->findOperationById($id);

        $validatedData = $request->validated();

        return DB::transaction(function () use ($operation, $validatedData) {
            [$operationData, $movesData] = $this->splitOperationData($validatedData);

            if (! empty($operationData)) {
                $operation->update($this->prepareOperationData($operationData, $operation));
            }

            if ($movesData !== null) {
                $this->syncMoves($operation->fresh(), $movesData);
            }

            $operation = $operation->fresh()->load($this->allowedIncludes);

            return (new ($this->resourceClass())($operation))
                ->additional(['message' => $this->updatedMessage()]);
        });
    }

    protected function prepareOperationData(array $data, ?Operation $existingOperation = null): array
    {
        $operationType = $this->resolveOperationTypeModel($data['operation_type_id'] ?? $existingOperation?->operation_type_id);
        $isCreating = $existingOperation === null;

        $preparedData = [
            ...$data,
            'operation_type_id'       => $operationType->id,
            'source_location_id'      => $data['source_location_id'] ?? $existingOperation?->source_location_id ?? $operationType->source_location_id,
            'destination_location_id' => $data['destination_location_id'] ?? $existingOperation?->destination_location_id ?? $operationType->destination_location_id,
            'company_id'              => $data['company_id'] ?? $existingOperation?->company_id ?? $this->resolveCompanyId($operationType),
            'user_id'                 => $data['user_id'] ?? $existingOperation?->user_id ?? Auth::id(),
            'state'                   => $data['state'] ?? $existingOperation?->state ?? OperationState::DRAFT,
        ];

        if ($isCreating) {
            $preparedData['creator_id'] = Auth::id();
        }

        return $preparedData;
    }

    protected function resolveOperationTypeModel(?int $operationTypeId = null): OperationType
    {
        $operationType = $operationTypeId
            ? OperationType::query()->find($operationTypeId)
            : OperationType::query()->where('type', $this->operationType())->first();

        if (! $operationType) {
            throw ValidationException::withMessages([
                'operation_type_id' => ['No operation type is configured for this resource.'],
            ]);
        }

        if ($operationType->type !== $this->operationType()) {
            throw ValidationException::withMessages([
                'operation_type_id' => ['The selected operation type does not match this resource.'],
            ]);
        }

        return $operationType;
    }

    protected function resolveCompanyId(OperationType $operationType): ?int
    {
        return match ($this->operationType()) {
            OperationTypeEnum::OUTGOING => $operationType->sourceLocation?->company_id,
            default                     => $operationType->destinationLocation?->company_id,
        };
    }

    protected function splitOperationData(array $data): array
    {
        $moves = Arr::pull($data, 'moves');

        return [$data, $moves];
    }

    protected function syncMoves(Operation $operation, array $moves): void
    {
        $existingMoveIds = $operation->moves()->pluck('id')->all();
        $retainedMoveIds = [];

        foreach ($moves as $moveData) {
            $moveId = $moveData['id'] ?? null;

            if ($moveId) {
                $move = $operation->moves()->whereKey($moveId)->first();

                if ($move) {
                    $move->update($this->prepareMoveData($operation, $moveData, true));
                    $retainedMoveIds[] = $move->id;

                    continue;
                }
            }

            $createdMove = $operation->moves()->create($this->prepareMoveData($operation, $moveData));
            $retainedMoveIds[] = $createdMove->id;
        }

        $moveIdsToDelete = array_diff($existingMoveIds, $retainedMoveIds);

        if (! empty($moveIdsToDelete)) {
            $operation->moves()->whereIn('id', $moveIdsToDelete)->delete();
        }
    }

    protected function prepareMoveData(Operation $operation, array $moveData, bool $isUpdate = false): array
    {
        $product = Product::query()->findOrFail($moveData['product_id']);
        $uom = isset($moveData['uom_id'])
            ? UOM::query()->findOrFail($moveData['uom_id'])
            : $product->uom;

        $productUomQty = (float) $moveData['product_uom_qty'];
        $productQty = $uom->computeQuantity($productUomQty, $product->uom, true, 'HALF-UP');

        $preparedData = [
            'product_id'              => $product->id,
            'product_uom_qty'         => $productUomQty,
            'product_qty'             => $productQty,
            'uom_id'                  => $uom->id,
            'final_location_id'       => $moveData['final_location_id'] ?? null,
            'description_picking'     => $moveData['description_picking'] ?? null,
            'scheduled_at'            => $moveData['scheduled_at'] ?? $operation->scheduled_at ?? now(),
            'deadline'                => $moveData['deadline'] ?? null,
            'product_packaging_id'    => $moveData['product_packaging_id'] ?? null,
            'quantity'                => $isUpdate ? ($moveData['quantity'] ?? null) : null,
            'is_picked'               => $isUpdate ? ($moveData['is_picked'] ?? false) : false,
            'company_id'              => $operation->company_id,
            'warehouse_id'            => $operation->destinationLocation?->warehouse_id,
            'state'                   => $isUpdate ? ($moveData['state'] ?? $operation->state?->value) : MoveState::DRAFT->value,
            'name'                    => $product->name,
            'procure_method'          => ProcureMethod::MAKE_TO_STOCK,
            'operation_type_id'       => $operation->operation_type_id,
            'source_location_id'      => $operation->source_location_id,
            'destination_location_id' => $operation->destination_location_id,
            'reference'               => $operation->name,
        ];

        if (! $isUpdate) {
            $preparedData['creator_id'] = Auth::id();
        }

        return $preparedData;
    }

    protected function ensureOperationTypeMatches(Operation $operation): void
    {
        if ($operation->operationType?->type !== $this->operationType()) {
            abort(404);
        }
    }

    protected function modelClass(): string
    {
        return Operation::class;
    }

    protected function resourceClass(): string
    {
        return OperationResource::class;
    }

    protected function operationType(): OperationTypeEnum
    {
        throw new \LogicException('Operation type must be defined in child controller.');
    }

    protected function createdMessage(): string
    {
        return 'Operation created successfully.';
    }

    protected function updatedMessage(): string
    {
        return 'Operation updated successfully.';
    }

    protected function deletedMessage(): string
    {
        return 'Operation deleted successfully.';
    }
}
