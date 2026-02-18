<?php

namespace Webkul\Purchase\Http\Controllers\API\V1;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Knuckles\Scribe\Attributes\Authenticated;
use Knuckles\Scribe\Attributes\Endpoint;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\QueryParam;
use Knuckles\Scribe\Attributes\Response;
use Knuckles\Scribe\Attributes\ResponseFromApiResource;
use Knuckles\Scribe\Attributes\Subgroup;
use Knuckles\Scribe\Attributes\UrlParam;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use Webkul\Product\Models\Product;
use Webkul\Purchase\Enums\RequisitionState;
use Webkul\Purchase\Http\Requests\PurchaseAgreementRequest;
use Webkul\Purchase\Http\Resources\V1\RequisitionResource;
use Webkul\Purchase\Models\Requisition;

#[Group('Purchase API Management')]
#[Subgroup('Purchase Agreements', 'Manage purchase agreements')]
#[Authenticated]
class PurchaseAgreementController extends Controller
{
    protected array $allowedIncludes = [
        'currency',
        'partner',
        'user',
        'company',
        'creator',
        'lines',
        'lines.requisition',
        'lines.product',
        'lines.uom',
        'lines.company',
        'lines.creator',
    ];

    #[Endpoint('List purchase agreements', 'Retrieve a paginated list of purchase agreements with filtering and sorting')]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> currency, partner, user, company, creator, lines, lines.requisition, lines.product, lines.uom, lines.company, lines.creator', required: false, example: 'partner,lines')]
    #[QueryParam('filter[id]', 'string', 'Comma-separated list of IDs to filter by', required: false, example: 'No-example')]
    #[QueryParam('filter[state]', 'string', 'Filter by agreement state', required: false, example: 'draft')]
    #[QueryParam('filter[type]', 'string', 'Filter by agreement type', required: false, example: 'blanket_order')]
    #[QueryParam('filter[partner_id]', 'string', 'Filter by vendor IDs', required: false, example: 'No-example')]
    #[QueryParam('filter[trashed]', 'string', 'Filter by trashed status. </br></br><b>Available options:</b> with, only', required: false, example: 'with')]
    #[QueryParam('sort', 'string', 'Sort field', example: '-created_at')]
    #[QueryParam('page', 'int', 'Page number', example: 1)]
    #[ResponseFromApiResource(RequisitionResource::class, Requisition::class, collection: true, paginate: 10)]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function index()
    {
        Gate::authorize('viewAny', Requisition::class);

        $agreements = QueryBuilder::for(Requisition::class)
            ->allowedFilters([
                AllowedFilter::exact('id'),
                AllowedFilter::exact('state'),
                AllowedFilter::exact('type'),
                AllowedFilter::exact('partner_id'),
                AllowedFilter::exact('currency_id'),
                AllowedFilter::exact('user_id'),
                AllowedFilter::exact('company_id'),
                AllowedFilter::trashed(),
            ])
            ->allowedSorts(['id', 'name', 'type', 'state', 'starts_at', 'ends_at', 'created_at'])
            ->allowedIncludes($this->allowedIncludes)
            ->paginate();

        return RequisitionResource::collection($agreements);
    }

    #[Endpoint('Create purchase agreement', 'Create a new purchase agreement with lines')]
    #[ResponseFromApiResource(RequisitionResource::class, Requisition::class, status: 201, additional: ['message' => 'Purchase agreement created successfully.'])]
    #[Response(status: 422, description: 'Validation error', content: '{"message": "The given data was invalid.", "errors": {"partner_id": ["The partner id field is required."], "lines": ["The lines field is required."]}}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function store(PurchaseAgreementRequest $request)
    {
        Gate::authorize('create', Requisition::class);

        $data = $request->validated();

        return DB::transaction(function () use ($data) {
            $lines = $data['lines'];
            unset($data['lines']);

            $agreement = Requisition::create($data);

            $this->syncAgreementLines($agreement, $lines);

            $agreement->load($this->allowedIncludes);

            return (new RequisitionResource($agreement))
                ->additional(['message' => 'Purchase agreement created successfully.'])
                ->response()
                ->setStatusCode(201);
        });
    }

    #[Endpoint('Show purchase agreement', 'Retrieve a specific purchase agreement by its ID')]
    #[UrlParam('id', 'integer', 'The purchase agreement ID', required: true, example: 1)]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> currency, partner, user, company, creator, lines, lines.requisition, lines.product, lines.uom, lines.company, lines.creator', required: false, example: 'partner,lines')]
    #[ResponseFromApiResource(RequisitionResource::class, Requisition::class)]
    #[Response(status: 404, description: 'Purchase agreement not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function show(string $id)
    {
        $agreement = QueryBuilder::for(Requisition::where('id', $id))
            ->allowedIncludes($this->allowedIncludes)
            ->firstOrFail();

        Gate::authorize('view', $agreement);

        return new RequisitionResource($agreement);
    }

    #[Endpoint('Update purchase agreement', 'Update an existing purchase agreement and sync lines')]
    #[UrlParam('id', 'integer', 'The purchase agreement ID', required: true, example: 1)]
    #[ResponseFromApiResource(RequisitionResource::class, Requisition::class, additional: ['message' => 'Purchase agreement updated successfully.'])]
    #[Response(status: 404, description: 'Purchase agreement not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 422, description: 'Validation error', content: '{"message": "The given data was invalid.", "errors": {"partner_id": ["The partner id field must be an integer."]}}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function update(PurchaseAgreementRequest $request, string $id)
    {
        $agreement = Requisition::findOrFail($id);

        Gate::authorize('update', $agreement);

        $data = $request->validated();

        return DB::transaction(function () use ($agreement, $data) {
            $lines = $data['lines'] ?? null;
            unset($data['lines']);

            $agreement->update($data);

            if ($lines !== null) {
                $this->syncAgreementLines($agreement, $lines);
            }

            $agreement->load($this->allowedIncludes);

            return (new RequisitionResource($agreement))
                ->additional(['message' => 'Purchase agreement updated successfully.']);
        });
    }

    #[Endpoint('Confirm purchase agreement', 'Confirm a draft purchase agreement')]
    #[UrlParam('id', 'integer', 'The purchase agreement ID', required: true, example: 1)]
    #[ResponseFromApiResource(RequisitionResource::class, Requisition::class, additional: ['message' => 'Purchase agreement confirmed successfully.'])]
    #[Response(status: 422, description: 'Invalid state transition', content: '{"message": "Only draft purchase agreements can be confirmed."}')]
    #[Response(status: 404, description: 'Purchase agreement not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function confirm(string $id)
    {
        $agreement = Requisition::findOrFail($id);

        Gate::authorize('update', $agreement);

        if ($agreement->state !== RequisitionState::DRAFT) {
            return response()->json([
                'message' => 'Only draft purchase agreements can be confirmed.',
            ], 422);
        }

        $agreement->update([
            'state' => RequisitionState::CONFIRMED,
        ]);

        return (new RequisitionResource($agreement->load($this->allowedIncludes)))
            ->additional(['message' => 'Purchase agreement confirmed successfully.']);
    }

    #[Endpoint('Close purchase agreement', 'Close a confirmed purchase agreement')]
    #[UrlParam('id', 'integer', 'The purchase agreement ID', required: true, example: 1)]
    #[ResponseFromApiResource(RequisitionResource::class, Requisition::class, additional: ['message' => 'Purchase agreement closed successfully.'])]
    #[Response(status: 422, description: 'Invalid state transition', content: '{"message": "Only confirmed purchase agreements can be closed."}')]
    #[Response(status: 404, description: 'Purchase agreement not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function close(string $id)
    {
        $agreement = Requisition::findOrFail($id);

        Gate::authorize('update', $agreement);

        if ($agreement->state !== RequisitionState::CONFIRMED) {
            return response()->json([
                'message' => 'Only confirmed purchase agreements can be closed.',
            ], 422);
        }

        $agreement->update([
            'state' => RequisitionState::CLOSED,
        ]);

        return (new RequisitionResource($agreement->load($this->allowedIncludes)))
            ->additional(['message' => 'Purchase agreement closed successfully.']);
    }

    #[Endpoint('Cancel purchase agreement', 'Cancel a draft or confirmed purchase agreement')]
    #[UrlParam('id', 'integer', 'The purchase agreement ID', required: true, example: 1)]
    #[ResponseFromApiResource(RequisitionResource::class, Requisition::class, additional: ['message' => 'Purchase agreement canceled successfully.'])]
    #[Response(status: 422, description: 'Invalid state transition', content: '{"message": "Only draft or confirmed purchase agreements can be canceled."}')]
    #[Response(status: 404, description: 'Purchase agreement not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function cancel(string $id)
    {
        $agreement = Requisition::findOrFail($id);

        Gate::authorize('update', $agreement);

        if (! in_array($agreement->state, [RequisitionState::DRAFT, RequisitionState::CONFIRMED], true)) {
            return response()->json([
                'message' => 'Only draft or confirmed purchase agreements can be canceled.',
            ], 422);
        }

        $agreement->update([
            'state' => RequisitionState::CANCELED,
        ]);

        return (new RequisitionResource($agreement->load($this->allowedIncludes)))
            ->additional(['message' => 'Purchase agreement canceled successfully.']);
    }

    #[Endpoint('Delete purchase agreement', 'Soft delete a purchase agreement')]
    #[UrlParam('id', 'integer', 'The purchase agreement ID', required: true, example: 1)]
    #[Response(status: 200, description: 'Purchase agreement deleted successfully', content: '{"message": "Purchase agreement deleted successfully."}')]
    #[Response(status: 404, description: 'Purchase agreement not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function destroy(string $id)
    {
        $agreement = Requisition::findOrFail($id);

        Gate::authorize('delete', $agreement);

        $agreement->delete();

        return response()->json([
            'message' => 'Purchase agreement deleted successfully.',
        ]);
    }

    #[Endpoint('Restore purchase agreement', 'Restore a soft-deleted purchase agreement')]
    #[UrlParam('id', 'integer', 'The purchase agreement ID', required: true, example: 1)]
    #[ResponseFromApiResource(RequisitionResource::class, Requisition::class, additional: ['message' => 'Purchase agreement restored successfully.'])]
    #[Response(status: 404, description: 'Purchase agreement not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function restore(string $id)
    {
        $agreement = Requisition::withTrashed()->findOrFail($id);

        Gate::authorize('restore', $agreement);

        $agreement->restore();

        return (new RequisitionResource($agreement->load($this->allowedIncludes)))
            ->additional(['message' => 'Purchase agreement restored successfully.']);
    }

    #[Endpoint('Force delete purchase agreement', 'Permanently delete a purchase agreement')]
    #[UrlParam('id', 'integer', 'The purchase agreement ID', required: true, example: 1)]
    #[Response(status: 200, description: 'Purchase agreement permanently deleted', content: '{"message": "Purchase agreement permanently deleted."}')]
    #[Response(status: 404, description: 'Purchase agreement not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function forceDestroy(string $id)
    {
        $agreement = Requisition::withTrashed()->findOrFail($id);

        Gate::authorize('forceDelete', $agreement);

        $agreement->forceDelete();

        return response()->json([
            'message' => 'Purchase agreement permanently deleted.',
        ]);
    }

    protected function syncAgreementLines(Requisition $agreement, array $linesData): void
    {
        $submittedIds = collect($linesData)
            ->pluck('id')
            ->filter()
            ->toArray();

        $agreement->lines()
            ->whereNotIn('id', $submittedIds)
            ->delete();

        foreach ($linesData as $lineData) {
            $lineId = $lineData['id'] ?? null;
            unset($lineData['id']);

            $lineData = Arr::only($lineData, [
                'product_id',
                'qty',
                'uom_id',
                'price_unit',
            ]);

            $product = isset($lineData['product_id'])
                ? Product::find($lineData['product_id'])
                : null;

            $payload = array_merge([
                'uom_id'     => $lineData['uom_id'] ?? $product?->uom_id,
                'company_id' => $agreement->company_id,
            ], $lineData);

            if ($lineId) {
                $line = $agreement->lines()->find($lineId);

                if (! $line) {
                    continue;
                }

                $line->update($payload);
            } else {
                $agreement->lines()->create($payload);
            }
        }
    }
}
