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
use Webkul\Inventory\Http\Requests\RouteRequest;
use Webkul\Inventory\Http\Resources\V1\RouteResource;
use Webkul\Inventory\Models\Route;

#[Group('Inventory API Management')]
#[Subgroup('Routes', 'Manage inventory routes')]
#[Authenticated]
class RouteController extends Controller
{
    protected array $allowedIncludes = [
        'suppliedWarehouse',
        'supplierWarehouse',
        'warehouses',
        'company',
        'creator',
        'rules',
    ];

    #[Endpoint('List routes', 'Retrieve a paginated list of routes with filtering and sorting')]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> suppliedWarehouse, supplierWarehouse, warehouses, company, creator, rules', required: false, example: 'company,warehouses')]
    #[QueryParam('filter[id]', 'string', 'Comma-separated list of IDs to filter by', required: false)]
    #[QueryParam('filter[name]', 'string', 'Filter by route name', required: false, example: 'MTO')]
    #[QueryParam('filter[company_id]', 'string', 'Filter by company IDs', required: false)]
    #[QueryParam('filter[product_selectable]', 'string', 'Filter by product selectable state', required: false, example: '1')]
    #[QueryParam('filter[product_category_selectable]', 'string', 'Filter by category selectable state', required: false, example: '1')]
    #[QueryParam('filter[warehouse_selectable]', 'string', 'Filter by warehouse selectable state', required: false, example: '1')]
    #[QueryParam('filter[packaging_selectable]', 'string', 'Filter by packaging selectable state', required: false, example: '0')]
    #[QueryParam('sort', 'string', 'Sort field', required: false, example: '-created_at')]
    #[ResponseFromApiResource(RouteResource::class, Route::class, collection: true, paginate: 10)]
    public function index()
    {
        Gate::authorize('viewAny', Route::class);

        $routes = QueryBuilder::for(Route::class)
            ->allowedFilters([
                AllowedFilter::exact('id'),
                AllowedFilter::partial('name'),
                AllowedFilter::exact('company_id'),
                AllowedFilter::exact('product_selectable'),
                AllowedFilter::exact('product_category_selectable'),
                AllowedFilter::exact('warehouse_selectable'),
                AllowedFilter::exact('packaging_selectable'),
            ])
            ->allowedSorts(['id', 'name', 'sort', 'created_at', 'updated_at'])
            ->allowedIncludes($this->allowedIncludes)
            ->paginate();

        return RouteResource::collection($routes);
    }

    #[Endpoint('Create route', 'Create a new route')]
    #[ResponseFromApiResource(RouteResource::class, Route::class, status: 201, additional: ['message' => 'Route created successfully.'])]
    #[Response(status: 422, description: 'Validation error')]
    public function store(RouteRequest $request)
    {
        Gate::authorize('create', Route::class);

        $data = $request->validated();
        $warehouses = $data['warehouses'] ?? null;
        unset($data['warehouses']);

        $route = Route::create($data);

        if ($warehouses !== null) {
            $route->warehouses()->sync($warehouses);
        }

        return (new RouteResource($route->load($this->allowedIncludes)))
            ->additional(['message' => 'Route created successfully.'])
            ->response()
            ->setStatusCode(201);
    }

    #[Endpoint('Show route', 'Retrieve a specific route by ID')]
    #[UrlParam('id', 'integer', 'The route ID', required: true, example: 1)]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> suppliedWarehouse, supplierWarehouse, warehouses, company, creator, rules', required: false, example: 'company,rules')]
    #[ResponseFromApiResource(RouteResource::class, Route::class)]
    #[Response(status: 404, description: 'Route not found')]
    public function show(string $id)
    {
        $route = QueryBuilder::for(Route::where('id', $id))
            ->allowedIncludes($this->allowedIncludes)
            ->firstOrFail();

        Gate::authorize('view', $route);

        return new RouteResource($route);
    }

    #[Endpoint('Update route', 'Update an existing route')]
    #[UrlParam('id', 'integer', 'The route ID', required: true, example: 1)]
    #[ResponseFromApiResource(RouteResource::class, Route::class, additional: ['message' => 'Route updated successfully.'])]
    #[Response(status: 404, description: 'Route not found')]
    #[Response(status: 422, description: 'Validation error')]
    public function update(RouteRequest $request, string $id)
    {
        $route = Route::findOrFail($id);

        Gate::authorize('update', $route);

        $data = $request->validated();
        $warehouses = $data['warehouses'] ?? null;
        unset($data['warehouses']);

        $route->update($data);

        if ($warehouses !== null) {
            $route->warehouses()->sync($warehouses);
        }

        return (new RouteResource($route->load($this->allowedIncludes)))
            ->additional(['message' => 'Route updated successfully.']);
    }

    #[Endpoint('Delete route', 'Soft delete a route')]
    #[UrlParam('id', 'integer', 'The route ID', required: true, example: 1)]
    #[Response(status: 200, description: 'Route deleted successfully', content: '{"message":"Route deleted successfully."}')]
    #[Response(status: 404, description: 'Route not found')]
    public function destroy(string $id)
    {
        $route = Route::findOrFail($id);

        Gate::authorize('delete', $route);

        $route->delete();

        return response()->json([
            'message' => 'Route deleted successfully.',
        ]);
    }

    #[Endpoint('Restore route', 'Restore a soft deleted route')]
    #[UrlParam('id', 'integer', 'The route ID', required: true, example: 1)]
    #[ResponseFromApiResource(RouteResource::class, Route::class, additional: ['message' => 'Route restored successfully.'])]
    #[Response(status: 404, description: 'Route not found')]
    public function restore(string $id)
    {
        $route = Route::withTrashed()->findOrFail($id);

        Gate::authorize('restore', $route);

        $route->restore();

        return (new RouteResource($route->fresh()->load($this->allowedIncludes)))
            ->additional(['message' => 'Route restored successfully.']);
    }

    #[Endpoint('Force delete route', 'Permanently delete a route')]
    #[UrlParam('id', 'integer', 'The route ID', required: true, example: 1)]
    #[Response(status: 200, description: 'Route permanently deleted successfully', content: '{"message":"Route permanently deleted successfully."}')]
    #[Response(status: 404, description: 'Route not found')]
    public function forceDestroy(string $id)
    {
        $route = Route::withTrashed()->findOrFail($id);

        Gate::authorize('forceDelete', $route);

        $route->forceDelete();

        return response()->json([
            'message' => 'Route permanently deleted successfully.',
        ]);
    }
}
