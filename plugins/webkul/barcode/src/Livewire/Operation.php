<?php

namespace Webkul\Barcode\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Livewire\Component;
use Throwable;
use Webkul\Inventory\Enums\CreateBackorder;
use Webkul\Inventory\Enums\LocationType;
use Webkul\Inventory\Enums\OperationState;
use Webkul\Inventory\Enums\ProductTracking;
use Webkul\Inventory\Facades\Inventory;
use Webkul\Inventory\Models\Location;
use Webkul\Inventory\Models\Lot;
use Webkul\Inventory\Models\MoveLine;
use Webkul\Inventory\Models\Operation as InventoryOperation;
use Webkul\Inventory\Models\OperationType;
use Webkul\Inventory\Models\Package;
use Webkul\Inventory\Models\Packaging;
use Webkul\Inventory\Models\ProductQuantity;
use Webkul\Product\Models\Product;

class Operation extends Component
{
    public OperationType $operationType;

    public InventoryOperation $operation;

    public string $barcode = '';

    public ?array $scanResult = null;

    public ?string $notice = null;

    public ?int $selectedMoveLineId = null;

    public array $countedMoveLineQuantities = [];

    public array $countedMoveLineIds = [];

    public ?int $editingMoveLineId = null;

    public int|string|null $editingMoveLineQuantityId = null;

    public int|string|null $editingMoveLineDestinationLocationId = null;

    public int|string|null $editingMoveLineResultPackageId = null;

    public ?string $editingMoveLineLotName = null;

    public array $editingMoveLineQuantityOptions = [];

    public array $editingMoveLineDestinationLocationOptions = [];

    public array $editingMoveLineResultPackageOptions = [];

    public array $moveLineSourceLocationOptions = [];

    public function mount(OperationType $operationType, InventoryOperation $operation): void
    {
        abort_unless((int) $operation->operation_type_id === (int) $operationType->id, 404);

        $this->operationType = $operationType;
        $this->operation = $operation;

        if (request()->filled('scan')) {
            $this->scanResult = $this->resolveScan($this->operation, (string) request('scan'));
            $this->notice = $this->scanResult['message'];
            $this->dispatchNativeFeedback($this->notice, (bool) ($this->scanResult['matched'] ?? false));

            if (isset($this->scanResult['moveLine']['id'])) {
                $moveLineId = (int) $this->scanResult['moveLine']['id'];

                $this->selectedMoveLineId = $moveLineId;
                $this->dispatch('barcode-move-line-located', moveLineId: $moveLineId, scannedAt: now()->getTimestampMs());
            }
        }
    }

    public function scan(): void
    {
        $this->scanResult = $this->resolveScan($this->operation, $this->barcode);
        $this->notice = $this->scanResult['message'];
        $this->dispatchNativeFeedback($this->notice, (bool) ($this->scanResult['matched'] ?? false));
        $this->barcode = '';

        if (isset($this->scanResult['moveLine']['id'])) {
            $moveLineId = (int) $this->scanResult['moveLine']['id'];
            $demandQuantity = (float) ($this->scanResult['moveLine']['demand'] ?? 0);

            $this->selectedMoveLineId = $moveLineId;
            $this->countedMoveLineQuantities[$moveLineId] = $demandQuantity;
            $this->countedMoveLineIds[$moveLineId] = true;
            $this->dispatch('barcode-move-line-located', moveLineId: $moveLineId, scannedAt: now()->getTimestampMs());
        }

        $this->operation->refresh();
    }

    public function updateMoveLineQuantity(int $moveLineId): void
    {
        $moveLine = MoveLine::query()->findOrFail($moveLineId);
        $quantity = min((float) $moveLine->qty, max(0, (float) ($this->countedMoveLineQuantities[$moveLineId] ?? 0)));

        $this->countedMoveLineQuantities[$moveLineId] = $quantity;
        $this->countedMoveLineIds[$moveLineId] = true;
        $this->notice = __('barcode::app.scan.move-updated');
    }

    public function adjustMoveLineQuantity(int $moveLineId, float $amount): void
    {
        $moveLine = MoveLine::query()->findOrFail($moveLineId);

        abort_unless((int) $moveLine->operation_id === (int) $this->operation->id, 404);

        $quantity = min((float) $moveLine->qty, max(0, (float) ($this->countedMoveLineQuantities[$moveLineId] ?? 0) + $amount));

        $this->countedMoveLineQuantities[$moveLineId] = $quantity;
        $this->countedMoveLineIds[$moveLineId] = true;
        $this->selectedMoveLineId = $quantity > 0 ? $moveLineId : ($this->selectedMoveLineId === $moveLineId ? null : $this->selectedMoveLineId);
    }

    public function setMoveLineQuantity(int $moveLineId, float $quantity): void
    {
        $moveLine = MoveLine::query()->findOrFail($moveLineId);

        abort_unless((int) $moveLine->operation_id === (int) $this->operation->id, 404);

        $this->countedMoveLineQuantities[$moveLineId] = min((float) $moveLine->qty, max(0, $quantity));
        $this->countedMoveLineIds[$moveLineId] = true;
        $this->selectedMoveLineId = $this->countedMoveLineQuantities[$moveLineId] > 0
            ? $moveLineId
            : ($this->selectedMoveLineId === $moveLineId ? null : $this->selectedMoveLineId);
    }

    public function updatedCountedMoveLineQuantities($value, $key): void
    {
        $moveLineId = (int) $key;

        if ($moveLineId <= 0) {
            return;
        }

        $moveLine = MoveLine::query()->find($moveLineId);

        if (! $moveLine || (int) $moveLine->operation_id !== (int) $this->operation->id) {
            return;
        }

        $this->countedMoveLineQuantities[$moveLineId] = min((float) $moveLine->qty, max(0, (float) $value));
        $this->selectedMoveLineId = $this->countedMoveLineQuantities[$moveLineId] > 0
            ? $moveLineId
            : ($this->selectedMoveLineId === $moveLineId ? null : $this->selectedMoveLineId);
    }

    public function editMoveLine(int $moveLineId): void
    {
        $moveLine = MoveLine::query()
            ->with(['lot', 'move', 'product', 'sourceLocation', 'destinationLocation', 'resultPackage', 'uom'])
            ->findOrFail($moveLineId);

        abort_unless((int) $moveLine->operation_id === (int) $this->operation->id, 404);

        $this->editingMoveLineId = $moveLineId;
        $this->selectedMoveLineId = $moveLineId;
        $this->countedMoveLineQuantities[$moveLineId] ??= 0.0;
        $this->editingMoveLineQuantityId = $this->matchingProductQuantityId($moveLine);
        $this->editingMoveLineDestinationLocationId = $moveLine->destination_location_id;
        $this->editingMoveLineResultPackageId = $moveLine->result_package_id;
        $this->editingMoveLineLotName = $moveLine->lot_name ?? $moveLine->lot?->name;
        $this->editingMoveLineQuantityOptions = $this->moveLineQuantityOptions($moveLine);
        $this->editingMoveLineDestinationLocationOptions = $this->moveLineDestinationLocationOptions($moveLine);
        $this->editingMoveLineResultPackageOptions = $this->moveLineResultPackageOptions(
            $moveLine,
            $this->editingMoveLineDestinationLocationId,
            $this->editingMoveLineResultPackageId
        );
        $this->moveLineSourceLocationOptions = $this->moveLineSourceLocationOptions($moveLine);
    }

    public function discardMoveLineEdit(): void
    {
        $this->editingMoveLineId = null;
        $this->editingMoveLineQuantityId = null;
        $this->editingMoveLineDestinationLocationId = null;
        $this->editingMoveLineResultPackageId = null;
        $this->editingMoveLineLotName = null;
        $this->editingMoveLineQuantityOptions = [];
        $this->editingMoveLineDestinationLocationOptions = [];
        $this->editingMoveLineResultPackageOptions = [];
        $this->moveLineSourceLocationOptions = [];
    }

    public function confirmMoveLineEdit(): void
    {
        if (! $this->editingMoveLineId) {
            return;
        }

        $moveLine = MoveLine::query()->findOrFail($this->editingMoveLineId);
        $quantity = min((float) $moveLine->qty, max(0, (float) ($this->countedMoveLineQuantities[$moveLine->id] ?? 0)));

        $this->countedMoveLineQuantities[$moveLine->id] = $quantity;
        $this->countedMoveLineIds[$moveLine->id] = true;
        $this->applyEditingMoveLineSelections($moveLine);
        $this->assignEditingMoveLineLot($moveLine);
        $this->notice = __('barcode::app.scan.move-counted');
        $this->dispatchNativeFeedback($this->notice, true);

        $this->discardMoveLineEdit();
    }

    public function executeAction(string $action, bool $cancelBackOrder = false): void
    {
        try {
            $this->operation = $this->executeOperationAction($this->operation, $action, $cancelBackOrder);
            $this->notice = __('barcode::app.actions.completed');
            $this->dispatchNativeFeedback($this->notice, true);

            if (in_array($action, ['validate', 'done'], true)) {
                $this->redirectRoute('barcode.transfers', $this->operationType, navigate: true);

                return;
            }
        } catch (Throwable $e) {
            $this->notice = $e->getMessage();
            $this->dispatchNativeFeedback($this->notice, false, 'long');
        }
    }

    public function updatedEditingMoveLineQuantityId($value): void
    {
        if (! $this->editingMoveLineId || ! $value) {
            return;
        }

        $this->applyEditingQuantitySelection((int) $value);
    }

    public function selectEditingMoveLineSourceQuantity(int $quantityId): void
    {
        if (! $this->editingMoveLineId) {
            return;
        }

        $this->editingMoveLineQuantityId = $quantityId;
        $this->applyEditingQuantitySelection($quantityId);
    }

    private function applyEditingQuantitySelection(int $quantityId): void
    {
        $moveLine = MoveLine::query()->with('uom')->find($this->editingMoveLineId);
        $productQuantity = ProductQuantity::query()->with(['lot', 'package'])->find($quantityId);

        if (! $moveLine || ! $productQuantity) {
            return;
        }

        $this->editingMoveLineLotName = $productQuantity->lot?->name;
        $this->editingMoveLineResultPackageId = $productQuantity->package_id;
        $this->editingMoveLineResultPackageOptions = $this->moveLineResultPackageOptions(
            $moveLine,
            $this->editingMoveLineDestinationLocationId,
            $this->editingMoveLineResultPackageId
        );
    }

    public function updatedEditingMoveLineDestinationLocationId(): void
    {
        if (! $this->editingMoveLineId) {
            return;
        }

        $moveLine = MoveLine::query()->find($this->editingMoveLineId);

        if (! $moveLine) {
            return;
        }

        $this->editingMoveLineResultPackageId = null;
        $this->editingMoveLineResultPackageOptions = $this->moveLineResultPackageOptions(
            $moveLine,
            $this->editingMoveLineDestinationLocationId,
            null
        );
    }

    public function render(): View
    {
        $operation = $this->loadOperation($this->operation);
        $moveLines = $this->filteredMoveLines($operation);

        foreach ($operation->moveLines as $moveLine) {
            $this->countedMoveLineQuantities[$moveLine->id] ??= 0.0;
        }

        return view('barcode::livewire.operation', [
            'actions'               => $this->availableActions($operation),
            'operation'             => $operation,
            'moveLines'             => $moveLines,
            'backorderMoveLines'    => $this->backorderMoveLines($moveLines),
            'shouldAskBackorder'    => $this->shouldAskBackorder($operation),
        ])->layout('barcode::layouts.app', [
            'title' => $operation->name,
        ]);
    }

    protected function filteredMoveLines(InventoryOperation $operation): Collection
    {
        $moveLines = $operation->moveLines
            ->filter(fn (MoveLine $moveLine): bool => (float) $moveLine->qty > 0)
            ->map(function (MoveLine $moveLine): MoveLine {
                $moveLine->product_uom_qty = $moveLine->qty;

                return $moveLine;
            });

        if ($this->barcode === '') {
            return $moveLines;
        }

        $search = mb_strtolower($this->barcode);

        return $moveLines->filter(function (MoveLine $moveLine) use ($search): bool {
            return str_contains(mb_strtolower((string) $moveLine->reference), $search)
                || str_contains(mb_strtolower((string) $moveLine->product?->name), $search)
                || str_contains(mb_strtolower((string) $moveLine->product?->reference), $search)
                || str_contains(mb_strtolower((string) $moveLine->product?->barcode), $search)
                || str_contains(mb_strtolower((string) $moveLine->lot?->name), $search)
                || str_contains(mb_strtolower((string) $moveLine->lot_name), $search);
        });
    }

    protected function backorderMoveLines(Collection $moveLines): array
    {
        $result = [];

        foreach ($moveLines as $moveLine) {
            $counted = (float) ($this->countedMoveLineQuantities[$moveLine->id] ?? 0);
            $required = (float) $moveLine->qty;

            if ($counted < $required) {
                $result[] = [
                    'id'        => $moveLine->id,
                    'name'      => $moveLine->product?->name ?? $moveLine->reference,
                    'counted'   => $counted,
                    'required'  => $required,
                    'backorder' => $required - $counted,
                    'uom'       => $moveLine->uom?->name ?? '',
                ];
            }
        }

        return $result;
    }

    protected function shouldAskBackorder(InventoryOperation $operation): bool
    {
        return $operation->operationType?->create_backorder === CreateBackorder::ASK;
    }

    private function loadOperation(InventoryOperation $operation): InventoryOperation
    {
        return $operation->loadMissing([
            'operationType',
            'partner',
            'sourceLocation',
            'destinationLocation',
            'moveLines.sourceLocation',
            'moveLines.product',
            'moveLines.uom',
            'moveLines.lot',
        ]);
    }

    private function availableActions(InventoryOperation $operation): array
    {
        if (in_array($operation->state, [OperationState::DONE, OperationState::CANCELED], true)) {
            return [];
        }

        return [
            ['key' => 'validate', 'label' => __('barcode::app.actions.validate'), 'variant' => 'primary'],
            ['key' => 'cancel', 'label' => __('barcode::app.actions.cancel'), 'variant' => 'danger'],
        ];
    }

    private function executeOperationAction(InventoryOperation $operation, string $action, bool $cancelBackOrder = false): InventoryOperation
    {
        return match ($action) {
            'validate', 'done' => $this->validateOperation($operation, $cancelBackOrder),
            'cancel'           => Inventory::cancelTransfer($operation),
            default            => throw new InvalidArgumentException(__('barcode::app.actions.unsupported')),
        };
    }

    private function validateOperation(InventoryOperation $operation, bool $cancelBackOrder = false): InventoryOperation
    {
        return DB::transaction(function () use ($operation, $cancelBackOrder): InventoryOperation {
            $operation = $operation->load([
                'moveLines.product.uom',
                'moveLines.uom',
                'moveLines.move',
                'moveLines.sourceLocation',
                'moveLines.lot',
                'moveLines.package',
            ]);

            if ($this->countedMoveLineIds !== []) {
                $this->syncCountedMoveLineQuantitiesForValidation($operation);
            }

            return Inventory::doneTransfer($operation->fresh(), $cancelBackOrder);
        });
    }

    private function syncCountedMoveLineQuantitiesForValidation(InventoryOperation $operation): void
    {
        foreach ($operation->moveLines as $moveLine) {
            $quantityToValidate = min(
                (float) $moveLine->qty,
                max(0, (float) ($this->countedMoveLineQuantities[$moveLine->id] ?? 0))
            );

            if (
                (float) $moveLine->qty === $quantityToValidate
            ) {
                continue;
            }

            $moveLine->qty = $quantityToValidate;
            $moveLine->save();
        }
    }

    private function assignEditingMoveLineLot(MoveLine $moveLine): void
    {
        if (! in_array($moveLine->product?->tracking, [ProductTracking::LOT, ProductTracking::SERIAL], true)) {
            return;
        }

        $lotName = $this->editingMoveLineLotName ? trim($this->editingMoveLineLotName) : null;

        if (! $lotName) {
            $moveLine->lot_id = null;
            $moveLine->lot_name = null;
            $moveLine->save();

            return;
        }

        $lot = Lot::query()
            ->where('product_id', $moveLine->product_id)
            ->where('name', $lotName)
            ->where(function ($query) use ($moveLine) {
                $query->whereNull('company_id')
                    ->orWhere('company_id', $moveLine->company_id);
            })
            ->first();

        if (! $lot) {
            $lot = Lot::query()->create([
                'name'       => $lotName,
                'product_id' => $moveLine->product_id,
                'company_id' => $moveLine->company_id,
            ]);
        }

        $moveLine->lot_id = $lot->id;
        $moveLine->lot_name = $lot->name;
        $moveLine->save();
    }

    private function applyEditingMoveLineSelections(MoveLine $moveLine): void
    {
        if ($this->editingMoveLineQuantityId) {
            $productQuantity = ProductQuantity::query()->find($this->editingMoveLineQuantityId);

            $moveLine->package_id = $productQuantity?->package_id;
            $moveLine->source_location_id = $productQuantity?->location_id ?? $moveLine->source_location_id;
        }

        $moveLine->destination_location_id = $this->editingMoveLineDestinationLocationId ?? $moveLine->destination_location_id;
        $moveLine->result_package_id = $this->editingMoveLineResultPackageId;
        $moveLine->save();
    }

    private function matchingProductQuantityId(MoveLine $moveLine): ?int
    {
        $productQuantity = ProductQuantity::query()
            ->where('product_id', $moveLine->product_id)
            ->where('location_id', $moveLine->source_location_id)
            ->where('lot_id', $moveLine->lot_id ?? null)
            ->where('package_id', $moveLine->package_id ?? null)
            ->first();

        return $productQuantity?->id;
    }

    private function moveLineQuantityOptions(MoveLine $moveLine): array
    {
        if ($moveLine->sourceLocation?->type !== LocationType::INTERNAL) {
            return [];
        }

        [$quantLocationScope] = $moveLine->product->getLocationFilters();
        $sourceLocationScopeId = $moveLine->move?->source_location_id ?? $moveLine->source_location_id;

        return ProductQuantity::query()
            ->with(['location', 'lot', 'package'])
            ->where('product_id', $moveLine->product_id)
            ->whereHas('location', function ($query) use ($sourceLocationScopeId) {
                $query->where('id', $sourceLocationScopeId)
                    ->orWhere('parent_id', $sourceLocationScopeId);
            })
            ->where('quantity', '>', 0)
            ->where(fn ($query) => $quantLocationScope($query))
            ->get()
            ->mapWithKeys(function (ProductQuantity $quantity): array {
                $nameParts = array_filter([
                    $quantity->location?->full_name,
                    $quantity->lot?->name,
                    $quantity->package?->name,
                ]);

                return [$quantity->id => implode(' - ', $nameParts)];
            })
            ->toArray();
    }

    private function moveLineDestinationLocationOptions(MoveLine $moveLine): array
    {
        return Location::query()
            ->withTrashed()
            ->where(function ($query) use ($moveLine) {
                $query->where('id', $moveLine->destination_location_id)
                    ->orWhere('parent_id', $moveLine->destination_location_id);
            })
            ->get()
            ->mapWithKeys(function (Location $location): array {
                $label = $location->full_name.($location->trashed() ? ' (Deleted)' : '');

                return [$location->id => $label];
            })
            ->toArray();
    }

    private function moveLineResultPackageOptions(MoveLine $moveLine, ?int $destinationLocationId, ?int $resultPackageId): array
    {
        return Package::query()
            ->where(function ($query) use ($destinationLocationId, $resultPackageId) {
                $query->where('location_id', $destinationLocationId)
                    ->orWhere('id', $resultPackageId)
                    ->orWhereNull('location_id');
            })
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();
    }

    private function resolveScan(InventoryOperation $operation, string $barcode): array
    {
        $barcode = trim($barcode);

        if ($barcode === '') {
            return [
                'matched' => false,
                'message' => __('barcode::app.scan.empty'),
            ];
        }

        if ($operation->name === $barcode) {
            return [
                'matched' => true,
                'type'    => 'operation',
                'message' => __('barcode::app.scan.operation-matched'),
                'record'  => [
                    'id'     => $operation->id,
                    'name'   => $operation->name,
                    'state'  => $operation->state?->value,
                    'origin' => $operation->origin,
                ],
            ];
        }

        if ($product = $this->findProduct($barcode)) {
            return $this->resolveProductMoveLine($operation, $product);
        }

        if ($packaging = $this->findPackaging($barcode)) {
            return $this->resolveProductMoveLine($operation, $packaging->product, 'packaging', [
                'id'      => $packaging->id,
                'name'    => $packaging->name,
                'barcode' => $packaging->barcode,
            ]);
        }

        if ($lot = $this->findLot($barcode)) {
            $moveLine = $operation->moveLines()
                ->with(['product', 'uom', 'sourceLocation', 'lot'])
                ->where('lot_id', $lot->id)
                ->orWhere('lot_name', $lot->name)
                ->first();

            if ($moveLine) {
                return [
                    'matched'  => true,
                    'type'     => 'lot',
                    'message'  => __('barcode::app.scan.move-located'),
                    'moveLine' => $this->moveLinePayload($moveLine),
                    'record'   => [
                        'id'   => $lot->id,
                        'name' => $lot->name,
                    ],
                ];
            }
        }

        if ($package = $this->findPackage($barcode)) {
            $moveLine = $operation->moveLines()
                ->with(['product', 'uom', 'sourceLocation', 'lot'])
                ->where('package_id', $package->id)
                ->orWhere('result_package_id', $package->id)
                ->first();

            if ($moveLine) {
                return [
                    'matched'  => true,
                    'type'     => 'package',
                    'message'  => __('barcode::app.scan.package-matched'),
                    'moveLine' => $this->moveLinePayload($moveLine),
                    'record'   => [
                        'id'   => $package->id,
                        'name' => $package->name,
                    ],
                ];
            }
        }

        return [
            'matched' => false,
            'message' => __('barcode::app.scan.not-found'),
        ];
    }

    private function resolveProductMoveLine(InventoryOperation $operation, Product $product, string $type = 'product', array $record = []): array
    {
        $moveLine = $operation->moveLines()
            ->with(['product', 'uom', 'sourceLocation', 'lot'])
            ->where('product_id', $product->id)
            ->first();

        if (! $moveLine) {
            return [
                'matched' => false,
                'type'    => $type,
                'message' => __('barcode::app.scan.product-not-on-operation'),
                'record'  => [
                    'id'        => $product->id,
                    'name'      => $product->name,
                    'reference' => $product->reference,
                    'barcode'   => $product->barcode,
                    ...$record,
                ],
            ];
        }

        return [
            'matched'  => true,
            'type'     => $type,
            'message'  => __('barcode::app.scan.move-located'),
            'moveLine' => $this->moveLinePayload($moveLine),
            'record'   => [
                'id'        => $product->id,
                'name'      => $product->name,
                'reference' => $product->reference,
                'barcode'   => $product->barcode,
                ...$record,
            ],
        ];
    }

    private function moveLinePayload(MoveLine $moveLine): array
    {
        return [
            'id'          => $moveLine->id,
            'name'        => $moveLine->reference,
            'product'     => $moveLine->product?->name,
            'reference'   => $moveLine->product?->reference,
            'barcode'     => $moveLine->product?->barcode,
            'quantity'    => (float) $moveLine->qty,
            'demand'      => (float) $moveLine->qty,
            'uom'         => $moveLine->uom?->name,
            'is_picked'   => (bool) $moveLine->is_picked,
            'is_complete' => false,
        ];
    }

    private function moveLineSourceLocationOptions(MoveLine $moveLine): array
    {
        $moveLine->loadMissing(['move', 'product.uom', 'uom', 'sourceLocation']);

        if (! $moveLine->product || ! $moveLine->sourceLocation || $moveLine->sourceLocation->type !== LocationType::INTERNAL) {
            return [];
        }

        [$quantLocationScope] = $moveLine->product->getLocationFilters();
        $sourceLocationScopeId = $moveLine->move?->source_location_id ?? $moveLine->source_location_id;

        return ProductQuantity::query()
            ->with(['location', 'lot', 'package'])
            ->where('product_id', $moveLine->product_id)
            ->whereHas('location', function ($query) use ($sourceLocationScopeId) {
                $query->where('id', $sourceLocationScopeId)
                    ->orWhere('parent_id', $sourceLocationScopeId);
            })
            ->where('quantity', '>', 0)
            ->where(fn ($query) => $quantLocationScope($query))
            ->get()
            ->map(function (ProductQuantity $quantity) use ($moveLine): array {
                return [
                    'quantity_id' => $quantity->id,
                    'location'    => $quantity->location?->full_name ?? $quantity->location?->name,
                    'lot'         => $quantity->lot?->name,
                    'package'     => $quantity->package?->name,
                    'available'   => $quantity->available_quantity,
                    'quantity'    => $quantity->quantity,
                    'uom'         => $moveLine->uom?->name,
                ];
            })
            ->all();
    }

    private function findProduct(string $barcode): ?Product
    {
        return Product::query()
            ->where('barcode', $barcode)
            ->orWhere('reference', $barcode)
            ->first();
    }

    private function findPackaging(string $barcode): ?Packaging
    {
        return Packaging::query()
            ->with('product')
            ->where('barcode', $barcode)
            ->first();
    }

    private function findLot(string $barcode): ?Lot
    {
        return Lot::query()
            ->with('product')
            ->where('name', $barcode)
            ->orWhere('reference', $barcode)
            ->first();
    }

    private function findPackage(string $barcode): ?Package
    {
        return Package::query()
            ->where('name', $barcode)
            ->first();
    }

    private function dispatchNativeFeedback(?string $message, bool $vibrate = false, string $duration = 'short'): void
    {
        if (! $message) {
            return;
        }

        $this->dispatch('barcode-native-feedback', message: $message, vibrate: $vibrate, duration: $duration);
    }
}
