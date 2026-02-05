<?php

namespace Webkul\Support\Http\Controllers\API\V1;

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
use Webkul\Support\Http\Requests\CurrencyRequest;
use Webkul\Support\Http\Resources\V1\CurrencyResource;
use Webkul\Support\Models\Currency;

#[Group('Support API Management')]
#[Subgroup('Currencies', 'Manage currencies')]
#[Authenticated]
class CurrencyController extends Controller
{
    #[Endpoint('List currencies', 'Retrieve a paginated list of currencies with filtering and sorting')]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> rates', required: false, example: 'rates')]
    #[QueryParam('filter[id]', 'string', 'Comma-separated list of IDs to filter by', required: false, example: 'No-example')]
    #[QueryParam('filter[name]', 'string', 'Filter by currency name (partial match)', required: false, example: 'No-example')]
    #[QueryParam('filter[symbol]', 'string', 'Filter by currency symbol (partial match)', required: false, example: 'No-example')]
    #[QueryParam('filter[active]', 'boolean', 'Filter by active status', required: false, example: 'No-example')]
    #[QueryParam('sort', 'string', 'Sort field', example: 'name')]
    #[QueryParam('page', 'int', 'Page number', example: 1)]
    #[ResponseFromApiResource(CurrencyResource::class, Currency::class, collection: true, paginate: 10)]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function index()
    {
        Gate::authorize('viewAny', Currency::class);

        $currencies = QueryBuilder::for(Currency::class)
            ->allowedFilters([
                AllowedFilter::exact('id'),
                AllowedFilter::partial('name'),
                AllowedFilter::partial('symbol'),
                AllowedFilter::exact('active'),
            ])
            ->allowedSorts(['id', 'name', 'symbol', 'created_at'])
            ->allowedIncludes([
                'rates',
            ])
            ->paginate();

        return CurrencyResource::collection($currencies);
    }

    #[Endpoint('Create currency', 'Create a new currency')]
    #[ResponseFromApiResource(CurrencyResource::class, Currency::class, status: 201, additional: ['message' => 'Currency created successfully.'])]
    #[Response(status: 422, description: 'Validation error', content: '{"message": "The given data was invalid.", "errors": {"name": ["The name field is required."]}}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function store(CurrencyRequest $request)
    {
        Gate::authorize('create', Currency::class);

        $currency = Currency::create($request->validated());

        return (new CurrencyResource($currency))
            ->additional(['message' => 'Currency created successfully.'])
            ->response()
            ->setStatusCode(201);
    }

    #[Endpoint('Show currency', 'Retrieve a specific currency by its ID')]
    #[UrlParam('id', 'integer', 'The currency ID', required: true, example: 1)]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> rates', required: false, example: 'rates')]
    #[ResponseFromApiResource(CurrencyResource::class, Currency::class)]
    #[Response(status: 404, description: 'Currency not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function show(string $id)
    {
        $currency = QueryBuilder::for(Currency::where('id', $id))
            ->allowedIncludes([
                'rates',
            ])
            ->firstOrFail();

        Gate::authorize('view', $currency);

        return new CurrencyResource($currency);
    }

    #[Endpoint('Update currency', 'Update an existing currency')]
    #[UrlParam('id', 'integer', 'The currency ID', required: true, example: 1)]
    #[ResponseFromApiResource(CurrencyResource::class, Currency::class, additional: ['message' => 'Currency updated successfully.'])]
    #[Response(status: 422, description: 'Validation error', content: '{"message": "The given data was invalid.", "errors": {"name": ["The name field must not exceed 255 characters."]}}')]
    #[Response(status: 404, description: 'Currency not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function update(CurrencyRequest $request, string $id)
    {
        $currency = Currency::findOrFail($id);

        Gate::authorize('update', $currency);

        $currency->update($request->validated());

        return (new CurrencyResource($currency))
            ->additional(['message' => 'Currency updated successfully.']);
    }

    #[Endpoint('Delete currency', 'Delete a currency')]
    #[UrlParam('id', 'integer', 'The currency ID', required: true, example: 1)]
    #[Response(status: 200, description: 'Currency deleted successfully', content: '{"message": "Currency deleted successfully."}')]
    #[Response(status: 404, description: 'Currency not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function destroy(string $id)
    {
        $currency = Currency::findOrFail($id);

        Gate::authorize('delete', $currency);

        $currency->delete();

        return response()->json(['message' => 'Currency deleted successfully.']);
    }
}
