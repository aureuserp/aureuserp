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
use Webkul\Inventory\Http\Requests\LocationRequest;
use Webkul\Inventory\Http\Resources\V1\LocationResource;
use Webkul\Inventory\Models\Location;

#[Group('Inventory API Management')]
#[Subgroup('Locations', 'Manage location configurations')]
#[Authenticated]
class LocationController extends Controller
{
    protected array $allowedIncludes = [
        'parent',
        'children',
        'storageCategory',
        'warehouse',
        'company',
        'creator',
    ];

    #[Endpoint('List locations', 'Retrieve a paginated list of locations with filtering and sorting')]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> parent, children, storageCategory, warehouse, company, creator', required: false, example: 'parent,storageCategory')]
    #[QueryParam('filter[id]', 'string', 'Comma-separated list of IDs to filter by', required: false)]
    #[QueryParam('filter[name]', 'string', 'Filter by location name', required: false, example: 'Stock')]
    #[QueryParam('filter[full_name]', 'string', 'Filter by full location name', required: false, example: 'WH/Stock')]
    #[QueryParam('filter[type]', 'string', 'Filter by location types', required: false, example: 'internal')]
    #[QueryParam('filter[barcode]', 'string', 'Filter by barcode', required: false, example: 'LOC-01')]
    #[QueryParam('filter[parent_id]', 'string', 'Filter by parent location IDs', required: false)]
    #[QueryParam('filter[storage_category_id]', 'string', 'Filter by storage category IDs', required: false)]
    #[QueryParam('filter[warehouse_id]', 'string', 'Filter by warehouse IDs', required: false)]
    #[QueryParam('filter[company_id]', 'string', 'Filter by company IDs', required: false)]
    #[QueryParam('sort', 'string', 'Sort field', required: false, example: '-created_at')]
    #[ResponseFromApiResource(LocationResource::class, Location::class, collection: true, paginate: 10)]
    public function index()
    {
        Gate::authorize('viewAny', Location::class);

        $locations = QueryBuilder::for(Location::class)
            ->allowedFilters([
                AllowedFilter::exact('id'),
                AllowedFilter::partial('name'),
                AllowedFilter::partial('full_name'),
                AllowedFilter::exact('type'),
                AllowedFilter::partial('barcode'),
                AllowedFilter::exact('parent_id'),
                AllowedFilter::exact('storage_category_id'),
                AllowedFilter::exact('warehouse_id'),
                AllowedFilter::exact('company_id'),
                AllowedFilter::exact('is_scrap'),
                AllowedFilter::exact('is_replenish'),
                AllowedFilter::exact('is_dock'),
            ])
            ->allowedSorts(['id', 'name', 'full_name', 'type', 'created_at', 'updated_at'])
            ->allowedIncludes($this->allowedIncludes)
            ->paginate();

        return LocationResource::collection($locations);
    }

    #[Endpoint('Create location', 'Create a new location')]
    #[ResponseFromApiResource(LocationResource::class, Location::class, status: 201, additional: ['message' => 'Location created successfully.'])]
    #[Response(status: 422, description: 'Validation error')]
    public function store(LocationRequest $request)
    {
        Gate::authorize('create', Location::class);

        $location = Location::create($request->validated());

        return (new LocationResource($location->load($this->allowedIncludes)))
            ->additional(['message' => 'Location created successfully.'])
            ->response()
            ->setStatusCode(201);
    }

    #[Endpoint('Show location', 'Retrieve a specific location by ID')]
    #[UrlParam('id', 'integer', 'The location ID', required: true, example: 1)]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> parent, children, storageCategory, warehouse, company, creator', required: false, example: 'parent,company')]
    #[ResponseFromApiResource(LocationResource::class, Location::class)]
    #[Response(status: 404, description: 'Location not found')]
    public function show(string $id)
    {
        $location = QueryBuilder::for(Location::where('id', $id))
            ->allowedIncludes($this->allowedIncludes)
            ->firstOrFail();

        Gate::authorize('view', $location);

        return new LocationResource($location);
    }

    #[Endpoint('Update location', 'Update an existing location')]
    #[UrlParam('id', 'integer', 'The location ID', required: true, example: 1)]
    #[ResponseFromApiResource(LocationResource::class, Location::class, additional: ['message' => 'Location updated successfully.'])]
    #[Response(status: 404, description: 'Location not found')]
    #[Response(status: 422, description: 'Validation error')]
    public function update(LocationRequest $request, string $id)
    {
        $location = Location::findOrFail($id);

        Gate::authorize('update', $location);

        $location->update($request->validated());

        return (new LocationResource($location->load($this->allowedIncludes)))
            ->additional(['message' => 'Location updated successfully.']);
    }

    #[Endpoint('Delete location', 'Soft delete a location')]
    #[UrlParam('id', 'integer', 'The location ID', required: true, example: 1)]
    #[Response(status: 200, description: 'Location deleted successfully', content: '{"message":"Location deleted successfully."}')]
    #[Response(status: 404, description: 'Location not found')]
    public function destroy(string $id)
    {
        $location = Location::findOrFail($id);

        Gate::authorize('delete', $location);

        $location->delete();

        return response()->json([
            'message' => 'Location deleted successfully.',
        ]);
    }

    #[Endpoint('Restore location', 'Restore a soft deleted location')]
    #[UrlParam('id', 'integer', 'The location ID', required: true, example: 1)]
    #[ResponseFromApiResource(LocationResource::class, Location::class, additional: ['message' => 'Location restored successfully.'])]
    #[Response(status: 404, description: 'Location not found')]
    public function restore(string $id)
    {
        $location = Location::withTrashed()->findOrFail($id);

        Gate::authorize('restore', $location);

        $location->restore();

        return (new LocationResource($location->fresh()->load($this->allowedIncludes)))
            ->additional(['message' => 'Location restored successfully.']);
    }

    #[Endpoint('Force delete location', 'Permanently delete a location')]
    #[UrlParam('id', 'integer', 'The location ID', required: true, example: 1)]
    #[Response(status: 200, description: 'Location permanently deleted successfully', content: '{"message":"Location permanently deleted successfully."}')]
    #[Response(status: 404, description: 'Location not found')]
    public function forceDestroy(string $id)
    {
        $location = Location::withTrashed()->findOrFail($id);

        Gate::authorize('forceDelete', $location);

        $location->forceDelete();

        return response()->json([
            'message' => 'Location permanently deleted successfully.',
        ]);
    }
}
