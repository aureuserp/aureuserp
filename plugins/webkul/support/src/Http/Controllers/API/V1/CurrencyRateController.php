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
use Webkul\Support\Http\Requests\CurrencyRateRequest;
use Webkul\Support\Http\Resources\V1\CurrencyRateResource;
use Webkul\Support\Models\Currency;
use Webkul\Support\Models\CurrencyRate;

#[Group('Support API Management')]
#[Subgroup('Currency Rates', 'Manage currency exchange rates')]
#[Authenticated]
class CurrencyRateController extends Controller
{
    #[Endpoint('List currency rates', 'Retrieve a paginated list of currency rates for a specific currency with filtering and sorting')]
    #[UrlParam('currency_id', 'integer', 'The currency ID', required: true, example: 1)]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> company, creator', required: false, example: 'currency')]
    #[QueryParam('filter[id]', 'string', 'Comma-separated list of IDs to filter by', required: false, example: 'No-example')]
    #[QueryParam('filter[company_id]', 'string', 'Comma-separated list of company IDs to filter by', required: false, example: 'No-example')]
    #[QueryParam('filter[name]', 'string', 'Filter by rate date (YYYY-MM-DD)', required: false, example: 'No-example')]
    #[QueryParam('sort', 'string', 'Sort field', example: 'name')]
    #[QueryParam('page', 'int', 'Page number', example: 1)]
    #[ResponseFromApiResource(CurrencyRateResource::class, CurrencyRate::class, collection: true, paginate: 10, with: ['currency'])]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function index(string $currency)
    {
        $currencyModel = Currency::findOrFail($currency);

        Gate::authorize('view', $currencyModel);

        $currencyRates = QueryBuilder::for(CurrencyRate::where('currency_id', $currency))
            ->allowedFilters([
                AllowedFilter::exact('id'),
                AllowedFilter::exact('company_id'),
                AllowedFilter::exact('name'),
            ])
            ->allowedSorts(['id', 'name', 'rate', 'created_at'])
            ->allowedIncludes([
                'company',
                'creator',
            ])
            ->paginate();

        return CurrencyRateResource::collection($currencyRates);
    }

    #[Endpoint('Create currency rate', 'Create a new currency exchange rate for a specific currency')]
    #[UrlParam('currency_id', 'integer', 'The currency ID', required: true, example: 1)]
    #[ResponseFromApiResource(CurrencyRateResource::class, CurrencyRate::class, status: 201, with: ['currency'], additional: ['message' => 'Currency rate created successfully.'])]
    #[Response(status: 422, description: 'Validation error', content: '{"message": "The given data was invalid.", "errors": {"name": ["The name field is required."]}}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function store(CurrencyRateRequest $request, string $currency)
    {
        $currencyModel = Currency::findOrFail($currency);

        Gate::authorize('update', $currencyModel);

        $data = $request->validated();
        $data['currency_id'] = $currency;

        $currencyRate = CurrencyRate::create($data);

        return (new CurrencyRateResource($currencyRate->load(['currency'])))
            ->additional(['message' => 'Currency rate created successfully.'])
            ->response()
            ->setStatusCode(201);
    }

    #[Endpoint('Show currency rate', 'Retrieve a specific currency rate by its ID')]
    #[UrlParam('currency_id', 'integer', 'The currency ID', required: true, example: 1)]
    #[UrlParam('id', 'integer', 'The currency rate ID', required: true, example: 1)]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> company, creator', required: false, example: 'currency')]
    #[ResponseFromApiResource(CurrencyRateResource::class, CurrencyRate::class, with: ['currency'])]
    #[Response(status: 404, description: 'Currency rate not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function show(string $currency, string $rate)
    {
        $currencyModel = Currency::findOrFail($currency);

        Gate::authorize('view', $currencyModel);

        $currencyRate = QueryBuilder::for(CurrencyRate::where('id', $rate)->where('currency_id', $currency))
            ->allowedIncludes([
                'company',
                'creator',
            ])
            ->firstOrFail();

        return new CurrencyRateResource($currencyRate);
    }

    #[Endpoint('Update currency rate', 'Update an existing currency rate')]
    #[UrlParam('currency_id', 'integer', 'The currency ID', required: true, example: 1)]
    #[UrlParam('id', 'integer', 'The currency rate ID', required: true, example: 1)]
    #[ResponseFromApiResource(CurrencyRateResource::class, CurrencyRate::class, with: ['currency'], additional: ['message' => 'Currency rate updated successfully.'])]
    #[Response(status: 422, description: 'Validation error', content: '{"message": "The given data was invalid.", "errors": {"rate": ["The rate field must be a number."]}}')]
    #[Response(status: 404, description: 'Currency rate not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function update(CurrencyRateRequest $request, string $currency, string $rate)
    {
        $currencyModel = Currency::findOrFail($currency);

        Gate::authorize('update', $currencyModel);

        $currencyRate = CurrencyRate::where('id', $rate)->where('currency_id', $currency)->firstOrFail();

        $currencyRate->update($request->validated());

        return (new CurrencyRateResource($currencyRate->load(['currency'])))
            ->additional(['message' => 'Currency rate updated successfully.']);
    }

    #[Endpoint('Delete currency rate', 'Delete a currency rate')]
    #[UrlParam('currency_id', 'integer', 'The currency ID', required: true, example: 1)]
    #[UrlParam('id', 'integer', 'The currency rate ID', required: true, example: 1)]
    #[Response(status: 200, description: 'Currency rate deleted successfully', content: '{"message": "Currency rate deleted successfully."}')]
    #[Response(status: 404, description: 'Currency rate not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function destroy(string $currency, string $rate)
    {
        $currencyModel = Currency::findOrFail($currency);

        Gate::authorize('update', $currencyModel);

        $currencyRate = CurrencyRate::where('id', $rate)->where('currency_id', $currency)->firstOrFail();

        Gate::authorize('delete', $currencyRate->currency);

        $currencyRate->delete();

        return response()->json([
            'message' => 'Currency rate deleted successfully.',
        ]);
    }
}
