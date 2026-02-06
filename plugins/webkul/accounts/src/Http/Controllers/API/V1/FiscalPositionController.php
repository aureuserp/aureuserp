<?php

namespace Webkul\Account\Http\Controllers\API\V1;

use Illuminate\Support\Facades\Auth;
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
use Webkul\Account\Http\Requests\FiscalPositionRequest;
use Webkul\Account\Http\Resources\V1\FiscalPositionResource;
use Webkul\Account\Models\FiscalPosition;

#[Group('Account API Management')]
#[Subgroup('Fiscal Positions', 'Manage fiscal positions')]
#[Authenticated]
class FiscalPositionController extends Controller
{
    #[Endpoint('List fiscal positions', 'Retrieve a paginated list of fiscal positions with filtering and sorting')]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> company, country, countryGroup, createdBy, taxes, accounts', required: false, example: 'company')]
    #[QueryParam('filter[id]', 'string', 'Comma-separated list of IDs to filter by', required: false, example: 'No-example')]
    #[QueryParam('filter[name]', 'string', 'Filter by fiscal position name (partial match)', required: false, example: 'No-example')]
    #[QueryParam('filter[company_id]', 'int', 'Filter by company ID', required: false, example: 'No-example')]
    #[QueryParam('filter[country_id]', 'int', 'Filter by country ID', required: false, example: 'No-example')]
    #[QueryParam('filter[vat_required]', 'boolean', 'Filter by VAT required flag', required: false, example: 'No-example')]
    #[QueryParam('sort', 'string', 'Sort field', example: 'sort')]
    #[QueryParam('page', 'int', 'Page number', example: 1)]
    #[ResponseFromApiResource(FiscalPositionResource::class, FiscalPosition::class, collection: true, paginate: 10)]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function index()
    {
        Gate::authorize('viewAny', FiscalPosition::class);

        $fiscalPositions = QueryBuilder::for(FiscalPosition::class)
            ->allowedFilters([
                AllowedFilter::exact('id'),
                AllowedFilter::partial('name'),
                AllowedFilter::exact('company_id'),
                AllowedFilter::exact('country_id'),
                AllowedFilter::exact('vat_required'),
            ])
            ->allowedSorts(['id', 'name', 'sort', 'created_at'])
            ->allowedIncludes([
                'company',
                'country',
                'countryGroup',
                'createdBy',
                'taxes',
                'accounts',
            ])
            ->paginate();

        return FiscalPositionResource::collection($fiscalPositions);
    }

    #[Endpoint('Create fiscal position', 'Create a new fiscal position')]
    #[ResponseFromApiResource(FiscalPositionResource::class, FiscalPosition::class, status: 201, additional: ['message' => 'Fiscal position created successfully.'])]
    #[Response(status: 422, description: 'Validation error', content: '{"message": "The given data was invalid.", "errors": {"name": ["The name field is required."]}}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function store(FiscalPositionRequest $request)
    {
        Gate::authorize('create', FiscalPosition::class);

        $data = $request->validated();
        $data['creator_id'] = Auth::id();

        $fiscalPosition = FiscalPosition::create($data);

        return (new FiscalPositionResource($fiscalPosition))
            ->additional(['message' => 'Fiscal position created successfully.'])
            ->response()
            ->setStatusCode(201);
    }

    #[Endpoint('Show fiscal position', 'Retrieve a specific fiscal position by its ID')]
    #[UrlParam('fiscal_position', 'integer', 'The fiscal position ID', required: true, example: 1)]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> company, country, countryGroup, createdBy, taxes, accounts', required: false, example: 'company,taxes')]
    #[ResponseFromApiResource(FiscalPositionResource::class, FiscalPosition::class)]
    #[Response(status: 404, description: 'Fiscal position not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function show(string $id)
    {
        $fiscalPosition = QueryBuilder::for(FiscalPosition::where('id', $id))
            ->allowedIncludes([
                'company',
                'country',
                'countryGroup',
                'createdBy',
                'taxes',
                'accounts',
            ])
            ->firstOrFail();

        Gate::authorize('view', $fiscalPosition);

        return new FiscalPositionResource($fiscalPosition);
    }

    #[Endpoint('Update fiscal position', 'Update an existing fiscal position')]
    #[UrlParam('fiscal_position', 'integer', 'The fiscal position ID', required: true, example: 1)]
    #[ResponseFromApiResource(FiscalPositionResource::class, FiscalPosition::class, additional: ['message' => 'Fiscal position updated successfully.'])]
    #[Response(status: 404, description: 'Fiscal position not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 422, description: 'Validation error', content: '{"message": "The given data was invalid."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function update(FiscalPositionRequest $request, string $id)
    {
        $fiscalPosition = FiscalPosition::findOrFail($id);

        Gate::authorize('update', $fiscalPosition);

        $fiscalPosition->update($request->validated());

        return (new FiscalPositionResource($fiscalPosition))
            ->additional(['message' => 'Fiscal position updated successfully.']);
    }

    #[Endpoint('Delete fiscal position', 'Delete a fiscal position')]
    #[UrlParam('fiscal_position', 'integer', 'The fiscal position ID', required: true, example: 1)]
    #[Response(status: 204, description: 'Fiscal position deleted successfully')]
    #[Response(status: 404, description: 'Fiscal position not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function destroy(string $id)
    {
        $fiscalPosition = FiscalPosition::findOrFail($id);

        Gate::authorize('delete', $fiscalPosition);

        $fiscalPosition->delete();

        return response()->noContent();
    }
}
