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
use Webkul\Inventory\Http\Requests\LotRequest;
use Webkul\Inventory\Http\Resources\V1\LotResource;
use Webkul\Inventory\Models\Lot;

#[Group('Inventories API Management')]
#[Subgroup('Lots', 'Manage lot/serial records')]
#[Authenticated]
class LotController extends Controller
{
    protected array $allowedIncludes = [
        'product',
        'uom',
        'location',
        'company',
        'creator',
        'quantities',
    ];

    #[Endpoint('List lots', 'Retrieve a paginated list of lots with filtering and sorting')]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> product, uom, location, company, creator, quantities', required: false, example: 'product,location')]
    #[QueryParam('filter[id]', 'string', 'Comma-separated list of IDs to filter by', required: false)]
    #[QueryParam('filter[name]', 'string', 'Filter by lot name', required: false, example: 'LOT')]
    #[QueryParam('filter[product_id]', 'string', 'Filter by product IDs', required: false)]
    #[QueryParam('filter[reference]', 'string', 'Filter by reference', required: false, example: 'BATCH')]
    #[QueryParam('filter[location_id]', 'string', 'Filter by location IDs', required: false)]
    #[QueryParam('filter[company_id]', 'string', 'Filter by company IDs', required: false)]
    #[QueryParam('sort', 'string', 'Sort field', required: false, example: '-created_at')]
    #[ResponseFromApiResource(LotResource::class, Lot::class, collection: true, paginate: 10)]
    public function index()
    {
        Gate::authorize('viewAny', Lot::class);

        $lots = QueryBuilder::for(Lot::class)
            ->allowedFilters([
                AllowedFilter::exact('id'),
                AllowedFilter::partial('name'),
                AllowedFilter::exact('product_id'),
                AllowedFilter::partial('reference'),
                AllowedFilter::exact('location_id'),
                AllowedFilter::exact('company_id'),
            ])
            ->allowedSorts(['id', 'name', 'reference', 'created_at', 'updated_at'])
            ->allowedIncludes($this->allowedIncludes)
            ->paginate();

        return LotResource::collection($lots);
    }

    #[Endpoint('Create lot', 'Create a new lot')]
    #[ResponseFromApiResource(LotResource::class, Lot::class, status: 201, additional: ['message' => 'Lot created successfully.'])]
    #[Response(status: 422, description: 'Validation error')]
    public function store(LotRequest $request)
    {
        Gate::authorize('create', Lot::class);

        $lot = Lot::create($request->validated());

        return (new LotResource($lot->load($this->allowedIncludes)))
            ->additional(['message' => 'Lot created successfully.'])
            ->response()
            ->setStatusCode(201);
    }

    #[Endpoint('Show lot', 'Retrieve a specific lot by ID')]
    #[UrlParam('id', 'integer', 'The lot ID', required: true, example: 1)]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> product, uom, location, company, creator, quantities', required: false, example: 'product,location')]
    #[ResponseFromApiResource(LotResource::class, Lot::class)]
    #[Response(status: 404, description: 'Lot not found')]
    public function show(string $id)
    {
        $lot = QueryBuilder::for(Lot::where('id', $id))
            ->allowedIncludes($this->allowedIncludes)
            ->firstOrFail();

        Gate::authorize('view', $lot);

        return new LotResource($lot);
    }

    #[Endpoint('Update lot', 'Update an existing lot')]
    #[UrlParam('id', 'integer', 'The lot ID', required: true, example: 1)]
    #[ResponseFromApiResource(LotResource::class, Lot::class, additional: ['message' => 'Lot updated successfully.'])]
    #[Response(status: 404, description: 'Lot not found')]
    #[Response(status: 422, description: 'Validation error')]
    public function update(LotRequest $request, string $id)
    {
        $lot = Lot::findOrFail($id);

        Gate::authorize('update', $lot);

        $lot->update($request->validated());

        return (new LotResource($lot->load($this->allowedIncludes)))
            ->additional(['message' => 'Lot updated successfully.']);
    }

    #[Endpoint('Delete lot', 'Delete a lot')]
    #[UrlParam('id', 'integer', 'The lot ID', required: true, example: 1)]
    #[Response(status: 200, description: 'Lot deleted successfully', content: '{"message":"Lot deleted successfully."}')]
    #[Response(status: 404, description: 'Lot not found')]
    public function destroy(string $id)
    {
        $lot = Lot::findOrFail($id);

        Gate::authorize('delete', $lot);

        $lot->delete();

        return response()->json([
            'message' => 'Lot deleted successfully.',
        ]);
    }
}
