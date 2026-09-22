<?php

namespace Webkul\Product\Http\Controllers\API\V1;

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
use Webkul\Product\Http\Requests\PriceListRequest;
use Webkul\Product\Http\Resources\V1\PriceListResource;
use Webkul\Product\Models\PriceList;

#[Group('Product API Management')]
#[Subgroup('Price Lists', 'Manage price lists and their pricing rules')]
#[Authenticated]
class PriceListController extends Controller
{
    #[Endpoint('List price lists', 'Retrieve a paginated list of price lists with filtering and sorting')]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> currency, company, creator, items', required: false, example: 'currency,items')]
    #[QueryParam('filter[id]', 'string', 'Comma-separated list of IDs to filter by', required: false, example: 'No-example')]
    #[QueryParam('filter[name]', 'string', 'Filter by price list name (partial match)', required: false, example: 'No-example')]
    #[QueryParam('filter[currency_id]', 'string', 'Comma-separated list of currency IDs to filter by', required: false, example: 'No-example')]
    #[QueryParam('filter[is_active]', 'boolean', 'Filter by active state', required: false, example: 'No-example')]
    #[QueryParam('sort', 'string', 'Sort field', example: 'sort')]
    #[QueryParam('page', 'int', 'Page number', example: 1)]
    #[ResponseFromApiResource(PriceListResource::class, PriceList::class, collection: true, paginate: 10)]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function index()
    {
        Gate::authorize('viewAny', PriceList::class);

        $priceLists = QueryBuilder::for(PriceList::class)
            ->allowedFilters(
                AllowedFilter::exact('id'),
                AllowedFilter::partial('name'),
                AllowedFilter::exact('currency_id'),
                AllowedFilter::exact('is_active'),
            )
            ->allowedSorts('id', 'name', 'sort', 'created_at')
            ->allowedIncludes(
                'currency',
                'company',
                'creator',
                'items',
            )
            ->paginate();

        return PriceListResource::collection($priceLists);
    }

    #[Endpoint('Create price list', 'Create a new price list together with its pricing rules')]
    #[ResponseFromApiResource(PriceListResource::class, PriceList::class, status: 201, additional: ['message' => 'Price list created successfully.'])]
    #[Response(status: 422, description: 'Validation error', content: '{"message": "The given data was invalid.", "errors": {"name": ["The name field is required."]}}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function store(PriceListRequest $request)
    {
        Gate::authorize('create', PriceList::class);

        $data = $request->validated();

        $priceList = DB::transaction(function () use ($data): PriceList {
            $priceList = PriceList::create(Arr::except($data, ['items']));

            $this->syncItems($priceList, $data['items'] ?? []);

            return $priceList;
        });

        return (new PriceListResource($priceList->load(['currency', 'items'])))
            ->additional(['message' => 'Price list created successfully.'])
            ->response()
            ->setStatusCode(201);
    }

    #[Endpoint('Show price list', 'Retrieve a specific price list by its ID')]
    #[UrlParam('id', 'integer', 'The price list ID', required: true, example: 1)]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> currency, company, creator, items', required: false, example: 'currency,items')]
    #[ResponseFromApiResource(PriceListResource::class, PriceList::class)]
    #[Response(status: 404, description: 'Price list not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function show(string $id)
    {
        $priceList = QueryBuilder::for(PriceList::where('id', $id))
            ->allowedIncludes(
                'currency',
                'company',
                'creator',
                'items',
            )
            ->firstOrFail();

        Gate::authorize('view', $priceList);

        return new PriceListResource($priceList);
    }

    #[Endpoint('Update price list', 'Update an existing price list. Sending items replaces the existing rules.')]
    #[UrlParam('id', 'integer', 'The price list ID', required: true, example: 1)]
    #[ResponseFromApiResource(PriceListResource::class, PriceList::class, additional: ['message' => 'Price list updated successfully.'])]
    #[Response(status: 404, description: 'Price list not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 422, description: 'Validation error', content: '{"message": "The given data was invalid.", "errors": {"name": ["The name field must be a string."]}}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function update(PriceListRequest $request, string $id)
    {
        $priceList = PriceList::findOrFail($id);

        Gate::authorize('update', $priceList);

        $data = $request->validated();

        DB::transaction(function () use ($priceList, $data): void {
            $priceList->update(Arr::except($data, ['items']));

            if (array_key_exists('items', $data)) {
                $priceList->items()->delete();

                $this->syncItems($priceList, $data['items'] ?? []);
            }
        });

        return (new PriceListResource($priceList->load(['currency', 'items'])))
            ->additional(['message' => 'Price list updated successfully.']);
    }

    #[Endpoint('Delete price list', 'Delete a price list and its pricing rules')]
    #[UrlParam('id', 'integer', 'The price list ID', required: true, example: 1)]
    #[Response(status: 200, description: 'Price list deleted', content: '{"message": "Price list deleted successfully."}')]
    #[Response(status: 404, description: 'Price list not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function destroy(string $id)
    {
        $priceList = PriceList::findOrFail($id);

        Gate::authorize('delete', $priceList);

        $priceList->delete();

        return response()->json([
            'message' => 'Price list deleted successfully.',
        ]);
    }

    /**
     * @param  array<int, array<string, mixed>>  $items
     */
    private function syncItems(PriceList $priceList, array $items): void
    {
        foreach ($items as $item) {
            $priceList->items()->create(Arr::except($item, ['id']));
        }
    }
}
