<?php

namespace Webkul\Support\Http\Controllers\API\V1;

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
use Webkul\Support\Http\Resources\V1\CountryResource;
use Webkul\Support\Models\Country;

#[Group('Support API Management')]
#[Subgroup('Countries', 'Manage countries')]
#[Authenticated]
class CountryController extends Controller
{
    #[Endpoint('List countries', 'Retrieve a paginated list of countries with filtering and sorting')]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> currency, states', required: false, example: 'currency')]
    #[QueryParam('filter[id]', 'string', 'Comma-separated list of IDs to filter by', required: false, example: 'No-example')]
    #[QueryParam('filter[name]', 'string', 'Filter by country name (partial match)', required: false, example: 'No-example')]
    #[QueryParam('filter[code]', 'string', 'Filter by country code (exact match)', required: false, example: 'No-example')]
    #[QueryParam('filter[phone_code]', 'string', 'Filter by phone code (partial match)', required: false, example: 'No-example')]
    #[QueryParam('filter[currency_id]', 'int', 'Filter by currency ID', required: false, example: 'No-example')]
    #[QueryParam('sort', 'string', 'Sort field', example: 'name')]
    #[QueryParam('page', 'int', 'Page number', example: 1)]
    #[ResponseFromApiResource(CountryResource::class, Country::class, collection: true, paginate: 10)]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function index()
    {
        $countries = QueryBuilder::for(Country::class)
            ->allowedFilters([
                AllowedFilter::exact('id'),
                AllowedFilter::partial('name'),
                AllowedFilter::exact('code'),
                AllowedFilter::partial('phone_code'),
                AllowedFilter::exact('currency_id'),
            ])
            ->allowedSorts(['id', 'name', 'code', 'created_at'])
            ->allowedIncludes([
                'currency',
                'states',
            ])
            ->paginate();

        return CountryResource::collection($countries);
    }

    #[Endpoint('Show country', 'Retrieve a specific country by its ID')]
    #[UrlParam('id', 'integer', 'The country ID', required: true, example: 1)]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> currency, states', required: false, example: 'currency,states')]
    #[ResponseFromApiResource(CountryResource::class, Country::class)]
    #[Response(status: 404, description: 'Country not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function show(string $id)
    {
        $country = QueryBuilder::for(Country::where('id', $id))
            ->allowedIncludes([
                'currency',
                'states',
            ])
            ->firstOrFail();

        return new CountryResource($country);
    }
}
