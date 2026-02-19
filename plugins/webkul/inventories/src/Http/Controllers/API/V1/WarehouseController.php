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
use Webkul\Inventory\Http\Requests\WarehouseRequest;
use Webkul\Inventory\Http\Resources\V1\WarehouseResource;
use Webkul\Inventory\Models\Warehouse;

#[Group('Inventory API Management')]
#[Subgroup('Warehouses', 'Manage warehouse configurations')]
#[Authenticated]
class WarehouseController extends Controller
{
    protected array $allowedIncludes = [
        'company',
        'partnerAddress',
        'creator',
        'locations',
        'supplierWarehouses',
        'routes',
    ];

    #[Endpoint('List warehouses', 'Retrieve a paginated list of warehouses with filtering and sorting')]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> company, partnerAddress, creator, locations, supplierWarehouses, routes', required: false, example: 'company,partnerAddress')]
    #[QueryParam('filter[id]', 'string', 'Comma-separated list of IDs to filter by', required: false)]
    #[QueryParam('filter[name]', 'string', 'Filter by warehouse name', required: false, example: 'Main')]
    #[QueryParam('filter[code]', 'string', 'Filter by warehouse code', required: false, example: 'WH')]
    #[QueryParam('filter[company_id]', 'string', 'Filter by company IDs', required: false)]
    #[QueryParam('filter[partner_address_id]', 'string', 'Filter by partner address IDs', required: false)]
    #[QueryParam('sort', 'string', 'Sort field', required: false, example: '-created_at')]
    #[ResponseFromApiResource(WarehouseResource::class, Warehouse::class, collection: true, paginate: 10)]
    public function index()
    {
        Gate::authorize('viewAny', Warehouse::class);

        $warehouses = QueryBuilder::for(Warehouse::class)
            ->allowedFilters([
                AllowedFilter::exact('id'),
                AllowedFilter::partial('name'),
                AllowedFilter::partial('code'),
                AllowedFilter::exact('company_id'),
                AllowedFilter::exact('partner_address_id'),
            ])
            ->allowedSorts(['id', 'name', 'code', 'sort', 'reception_steps', 'delivery_steps', 'created_at', 'updated_at'])
            ->allowedIncludes($this->allowedIncludes)
            ->paginate();

        return WarehouseResource::collection($warehouses);
    }

    #[Endpoint('Create warehouse', 'Create a new warehouse')]
    #[ResponseFromApiResource(WarehouseResource::class, Warehouse::class, status: 201, additional: ['message' => 'Warehouse created successfully.'])]
    #[Response(status: 422, description: 'Validation error')]
    public function store(WarehouseRequest $request)
    {
        Gate::authorize('create', Warehouse::class);

        $data = $request->validated();
        $supplierWarehouses = $data['supplier_warehouses'] ?? null;
        unset($data['supplier_warehouses']);

        $warehouse = Warehouse::create($data);

        if ($supplierWarehouses !== null) {
            $warehouse->supplierWarehouses()->sync($supplierWarehouses);
        }

        return (new WarehouseResource($warehouse->load($this->allowedIncludes)))
            ->additional(['message' => 'Warehouse created successfully.'])
            ->response()
            ->setStatusCode(201);
    }

    #[Endpoint('Show warehouse', 'Retrieve a specific warehouse by ID')]
    #[UrlParam('id', 'integer', 'The warehouse ID', required: true, example: 1)]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> company, partnerAddress, creator, locations, supplierWarehouses, routes', required: false, example: 'company,locations')]
    #[ResponseFromApiResource(WarehouseResource::class, Warehouse::class)]
    #[Response(status: 404, description: 'Warehouse not found')]
    public function show(string $id)
    {
        $warehouse = QueryBuilder::for(Warehouse::where('id', $id))
            ->allowedIncludes($this->allowedIncludes)
            ->firstOrFail();

        Gate::authorize('view', $warehouse);

        return new WarehouseResource($warehouse);
    }

    #[Endpoint('Update warehouse', 'Update an existing warehouse')]
    #[UrlParam('id', 'integer', 'The warehouse ID', required: true, example: 1)]
    #[ResponseFromApiResource(WarehouseResource::class, Warehouse::class, additional: ['message' => 'Warehouse updated successfully.'])]
    #[Response(status: 404, description: 'Warehouse not found')]
    #[Response(status: 422, description: 'Validation error')]
    public function update(WarehouseRequest $request, string $id)
    {
        $warehouse = Warehouse::findOrFail($id);

        Gate::authorize('update', $warehouse);

        $data = $request->validated();
        $supplierWarehouses = $data['supplier_warehouses'] ?? null;
        unset($data['supplier_warehouses']);

        $warehouse->update($data);

        if ($supplierWarehouses !== null) {
            $warehouse->supplierWarehouses()->sync($supplierWarehouses);
        }

        return (new WarehouseResource($warehouse->load($this->allowedIncludes)))
            ->additional(['message' => 'Warehouse updated successfully.']);
    }

    #[Endpoint('Delete warehouse', 'Soft delete a warehouse')]
    #[UrlParam('id', 'integer', 'The warehouse ID', required: true, example: 1)]
    #[Response(status: 200, description: 'Warehouse deleted successfully', content: '{"message":"Warehouse deleted successfully."}')]
    #[Response(status: 404, description: 'Warehouse not found')]
    public function destroy(string $id)
    {
        $warehouse = Warehouse::findOrFail($id);

        Gate::authorize('delete', $warehouse);

        $warehouse->delete();

        return response()->json([
            'message' => 'Warehouse deleted successfully.',
        ]);
    }

    #[Endpoint('Restore warehouse', 'Restore a soft deleted warehouse')]
    #[UrlParam('id', 'integer', 'The warehouse ID', required: true, example: 1)]
    #[ResponseFromApiResource(WarehouseResource::class, Warehouse::class, additional: ['message' => 'Warehouse restored successfully.'])]
    #[Response(status: 404, description: 'Warehouse not found')]
    public function restore(string $id)
    {
        $warehouse = Warehouse::withTrashed()->findOrFail($id);

        Gate::authorize('restore', $warehouse);

        $warehouse->restore();

        return (new WarehouseResource($warehouse->fresh()->load($this->allowedIncludes)))
            ->additional(['message' => 'Warehouse restored successfully.']);
    }

    #[Endpoint('Force delete warehouse', 'Permanently delete a warehouse')]
    #[UrlParam('id', 'integer', 'The warehouse ID', required: true, example: 1)]
    #[Response(status: 200, description: 'Warehouse permanently deleted successfully', content: '{"message":"Warehouse permanently deleted successfully."}')]
    #[Response(status: 404, description: 'Warehouse not found')]
    public function forceDestroy(string $id)
    {
        $warehouse = Warehouse::withTrashed()->findOrFail($id);

        Gate::authorize('forceDelete', $warehouse);

        $warehouse->forceDelete();

        return response()->json([
            'message' => 'Warehouse permanently deleted successfully.',
        ]);
    }
}
