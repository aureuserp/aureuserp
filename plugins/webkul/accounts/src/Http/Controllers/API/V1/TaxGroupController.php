<?php

namespace Webkul\Account\Http\Controllers\API\V1;

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
use Webkul\Account\Http\Requests\TaxGroupRequest;
use Webkul\Account\Http\Resources\V1\TaxGroupResource;
use Webkul\Account\Models\TaxGroup;

#[Group('Account API Management')]
#[Subgroup('Tax Groups', 'Manage tax groups')]
#[Authenticated]
class TaxGroupController extends Controller
{
    #[Endpoint('List tax groups', 'Retrieve a paginated list of tax groups with filtering and sorting')]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> company, country, creator', required: false, example: 'company')]
    #[QueryParam('filter[id]', 'string', 'Comma-separated list of IDs to filter by', required: false, example: 'No-example')]
    #[QueryParam('filter[name]', 'string', 'Filter by tax group name (partial match)', required: false, example: 'No-example')]
    #[QueryParam('filter[company_id]', 'int', 'Filter by company ID', required: false, example: 'No-example')]
    #[QueryParam('filter[country_id]', 'int', 'Filter by country ID', required: false, example: 'No-example')]
    #[QueryParam('sort', 'string', 'Sort field', example: 'sort')]
    #[QueryParam('page', 'int', 'Page number', example: 1)]
    #[ResponseFromApiResource(TaxGroupResource::class, TaxGroup::class, collection: true, paginate: 10)]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function index()
    {
        Gate::authorize('viewAny', TaxGroup::class);

        $taxGroups = QueryBuilder::for(TaxGroup::class)
            ->allowedFilters([
                AllowedFilter::exact('id'),
                AllowedFilter::partial('name'),
                AllowedFilter::exact('company_id'),
                AllowedFilter::exact('country_id'),
            ])
            ->allowedSorts(['id', 'name', 'sort', 'created_at'])
            ->allowedIncludes([
                'company',
                'country',
                'creator',
            ])
            ->paginate();

        return TaxGroupResource::collection($taxGroups);
    }

    #[Endpoint('Create tax group', 'Create a new tax group')]
    #[ResponseFromApiResource(TaxGroupResource::class, TaxGroup::class, status: 201, additional: ['message' => 'Tax group created successfully.'])]
    #[Response(status: 422, description: 'Validation error', content: '{"message": "The given data was invalid.", "errors": {"name": ["The name field is required."]}}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function store(TaxGroupRequest $request)
    {
        Gate::authorize('create', TaxGroup::class);

        $data = $request->validated();

        $taxGroup = TaxGroup::create($data);

        return (new TaxGroupResource($taxGroup))
            ->additional(['message' => 'Tax group created successfully.'])
            ->response()
            ->setStatusCode(201);
    }

    #[Endpoint('Show tax group', 'Retrieve a specific tax group by its ID')]
    #[UrlParam('id', 'integer', 'The tax group ID', required: true, example: 1)]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> company, country, creator', required: false, example: 'company,country')]
    #[ResponseFromApiResource(TaxGroupResource::class, TaxGroup::class)]
    #[Response(status: 404, description: 'Tax group not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function show(string $id)
    {
        $taxGroup = QueryBuilder::for(TaxGroup::where('id', $id))
            ->allowedIncludes([
                'company',
                'country',
                'creator',
            ])
            ->firstOrFail();

        Gate::authorize('view', $taxGroup);

        return new TaxGroupResource($taxGroup);
    }

    #[Endpoint('Update tax group', 'Update an existing tax group')]
    #[UrlParam('id', 'integer', 'The tax group ID', required: true, example: 1)]
    #[ResponseFromApiResource(TaxGroupResource::class, TaxGroup::class, additional: ['message' => 'Tax group updated successfully.'])]
    #[Response(status: 404, description: 'Tax group not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 422, description: 'Validation error', content: '{"message": "The given data was invalid."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function update(TaxGroupRequest $request, string $id)
    {
        $taxGroup = TaxGroup::findOrFail($id);

        Gate::authorize('update', $taxGroup);

        $taxGroup->update($request->validated());

        return (new TaxGroupResource($taxGroup))
            ->additional(['message' => 'Tax group updated successfully.']);
    }

    #[Endpoint('Delete tax group', 'Delete a tax group')]
    #[UrlParam('id', 'integer', 'The tax group ID', required: true, example: 1)]
    #[Response(status: 200, description: 'Tax group deleted', content: '{"message": "Tax group deleted successfully."}')]
    #[Response(status: 404, description: 'Tax group not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function destroy(string $id)
    {
        $taxGroup = TaxGroup::findOrFail($id);

        Gate::authorize('delete', $taxGroup);

        $taxGroup->delete();

        return response()->json([
            'message' => 'Tax group deleted successfully.',
        ]);
    }
}
