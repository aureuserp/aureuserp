<?php

namespace Webkul\Inventory\Http\Controllers\API\V1;

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
use Webkul\Inventory\Http\Requests\OperationTypeRequest;
use Webkul\Inventory\Http\Resources\V1\OperationTypeResource;
use Webkul\Inventory\Models\OperationType;

#[Group('Inventory API Management')]
#[Subgroup('Operation Types', 'Manage inventory operation type configurations')]
#[Authenticated]
class OperationTypeController extends Controller
{
    protected array $allowedIncludes = [
        'returnOperationType',
        'sourceLocation',
        'destinationLocation',
        'warehouse',
        'company',
        'creator',
    ];

    #[Endpoint('List operation types', 'Retrieve a paginated list of operation types with filtering and sorting')]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> returnOperationType, sourceLocation, destinationLocation, warehouse, company, creator', required: false, example: 'warehouse,sourceLocation')]
    #[QueryParam('filter[id]', 'string', 'Comma-separated list of IDs to filter by', required: false)]
    #[QueryParam('filter[name]', 'string', 'Filter by name', required: false, example: 'Receipt')]
    #[QueryParam('filter[type]', 'string', 'Filter by operation type values', required: false, example: 'incoming')]
    #[QueryParam('filter[warehouse_id]', 'string', 'Filter by warehouse IDs', required: false)]
    #[QueryParam('filter[company_id]', 'string', 'Filter by company IDs', required: false)]
    #[QueryParam('sort', 'string', 'Sort field', required: false, example: '-created_at')]
    #[ResponseFromApiResource(OperationTypeResource::class, OperationType::class, collection: true, paginate: 10)]
    public function index()
    {
        Gate::authorize('viewAny', OperationType::class);

        $operationTypes = QueryBuilder::for(OperationType::class)
            ->allowedFilters([
                AllowedFilter::exact('id'),
                AllowedFilter::partial('name'),
                AllowedFilter::exact('type'),
                AllowedFilter::exact('warehouse_id'),
                AllowedFilter::exact('company_id'),
            ])
            ->allowedSorts(['id', 'name', 'type', 'sort', 'created_at', 'updated_at'])
            ->allowedIncludes($this->allowedIncludes)
            ->paginate();

        return OperationTypeResource::collection($operationTypes);
    }

    #[Endpoint('Create operation type', 'Create a new operation type')]
    #[ResponseFromApiResource(OperationTypeResource::class, OperationType::class, status: 201, additional: ['message' => 'Operation type created successfully.'])]
    #[Response(status: 422, description: 'Validation error')]
    public function store(OperationTypeRequest $request)
    {
        Gate::authorize('create', OperationType::class);

        $operationType = OperationType::create($request->validated());

        return (new OperationTypeResource($operationType->load($this->allowedIncludes)))
            ->additional(['message' => 'Operation type created successfully.'])
            ->response()
            ->setStatusCode(201);
    }

    #[Endpoint('Show operation type', 'Retrieve a specific operation type by ID')]
    #[UrlParam('id', 'integer', 'The operation type ID', required: true, example: 1)]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> returnOperationType, sourceLocation, destinationLocation, warehouse, company, creator', required: false, example: 'warehouse,company')]
    #[ResponseFromApiResource(OperationTypeResource::class, OperationType::class)]
    #[Response(status: 404, description: 'Operation type not found')]
    public function show(string $id)
    {
        $operationType = QueryBuilder::for(OperationType::where('id', $id))
            ->allowedIncludes($this->allowedIncludes)
            ->firstOrFail();

        Gate::authorize('view', $operationType);

        return new OperationTypeResource($operationType);
    }

    #[Endpoint('Update operation type', 'Update an existing operation type')]
    #[UrlParam('id', 'integer', 'The operation type ID', required: true, example: 1)]
    #[ResponseFromApiResource(OperationTypeResource::class, OperationType::class, additional: ['message' => 'Operation type updated successfully.'])]
    #[Response(status: 404, description: 'Operation type not found')]
    #[Response(status: 422, description: 'Validation error')]
    public function update(OperationTypeRequest $request, string $id)
    {
        $operationType = OperationType::findOrFail($id);

        Gate::authorize('update', $operationType);

        $operationType->update($request->validated());

        return (new OperationTypeResource($operationType->load($this->allowedIncludes)))
            ->additional(['message' => 'Operation type updated successfully.']);
    }

    #[Endpoint('Delete operation type', 'Soft delete an operation type')]
    #[UrlParam('id', 'integer', 'The operation type ID', required: true, example: 1)]
    #[Response(status: 200, description: 'Operation type deleted successfully', content: '{"message":"Operation type deleted successfully."}')]
    #[Response(status: 404, description: 'Operation type not found')]
    public function destroy(string $id)
    {
        $operationType = OperationType::findOrFail($id);

        Gate::authorize('delete', $operationType);

        $operationType->delete();

        return response()->json([
            'message' => 'Operation type deleted successfully.',
        ]);
    }

    #[Endpoint('Restore operation type', 'Restore a soft deleted operation type')]
    #[UrlParam('id', 'integer', 'The operation type ID', required: true, example: 1)]
    #[ResponseFromApiResource(OperationTypeResource::class, OperationType::class, additional: ['message' => 'Operation type restored successfully.'])]
    #[Response(status: 404, description: 'Operation type not found')]
    public function restore(string $id)
    {
        $operationType = OperationType::withTrashed()->findOrFail($id);

        Gate::authorize('restore', $operationType);

        $operationType->restore();

        return (new OperationTypeResource($operationType->fresh()->load($this->allowedIncludes)))
            ->additional(['message' => 'Operation type restored successfully.']);
    }

    #[Endpoint('Force delete operation type', 'Permanently delete an operation type')]
    #[UrlParam('id', 'integer', 'The operation type ID', required: true, example: 1)]
    #[Response(status: 200, description: 'Operation type permanently deleted successfully', content: '{"message":"Operation type permanently deleted successfully."}')]
    #[Response(status: 404, description: 'Operation type not found')]
    public function forceDestroy(string $id)
    {
        $operationType = OperationType::withTrashed()->findOrFail($id);

        Gate::authorize('forceDelete', $operationType);

        $operationType->forceDelete();

        return response()->json([
            'message' => 'Operation type permanently deleted successfully.',
        ]);
    }
}
