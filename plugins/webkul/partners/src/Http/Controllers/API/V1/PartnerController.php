<?php

namespace Webkul\Partner\Http\Controllers\API\V1;

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
use Webkul\Partner\Http\Requests\PartnerRequest;
use Webkul\Partner\Http\Resources\V1\PartnerResource;
use Webkul\Partner\Enums\AccountType;
use Webkul\Partner\Models\Partner;

#[Group('Partner API Management')]
#[Subgroup('Partners', 'Manage partners (customers, suppliers, contacts)')]
#[Authenticated]
class PartnerController extends Controller
{
    #[Endpoint('List partners', 'Retrieve a paginated list of partners with filtering and sorting')]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> parent, country, state, title, company, industry, user, creator, addresses, contacts, bankAccounts, tags', required: false, example: 'tags,addresses')]
    #[QueryParam('filter[id]', 'string', 'Comma-separated list of IDs to filter by', required: false, example: 'No-example')]
    #[QueryParam('filter[account_type]', 'string', 'Filter by account type', enum: AccountType::class, required: false, example: 'No-example')]
    #[QueryParam('filter[name]', 'string', 'Filter by partner name (partial match)', required: false, example: 'No-example')]
    #[QueryParam('filter[email]', 'string', 'Filter by email (partial match)', required: false, example: 'No-example')]
    #[QueryParam('filter[phone]', 'string', 'Filter by phone (partial match)', required: false, example: 'No-example')]
    #[QueryParam('filter[parent_id]', 'string', 'Comma-separated list of parent IDs to filter by', required: false, example: 'No-example')]
    #[QueryParam('filter[user_id]', 'string', 'Comma-separated list of user IDs to filter by', required: false, example: 'No-example')]
    #[QueryParam('filter[company_id]', 'string', 'Comma-separated list of company IDs to filter by', required: false, example: 'No-example')]
    #[QueryParam('filter[title_id]', 'string', 'Comma-separated list of title IDs to filter by', required: false, example: 'No-example')]
    #[QueryParam('filter[industry_id]', 'string', 'Comma-separated list of industry IDs to filter by', required: false, example: 'No-example')]
    #[QueryParam('filter[country_id]', 'string', 'Comma-separated list of country IDs to filter by', required: false, example: 'No-example')]
    #[QueryParam('filter[state_id]', 'string', 'Comma-separated list of state IDs to filter by', required: false, example: 'No-example')]
    #[QueryParam('filter[trashed]', 'string', 'Filter by trashed status. </br></br><b>Available options:</b> with, without, only', required: false, example: 'No-example')]
    #[QueryParam('sort', 'string', 'Sort field', example: 'name')]
    #[QueryParam('page', 'int', 'Page number', example: 1)]
    #[ResponseFromApiResource(PartnerResource::class, Partner::class, collection: true, paginate: 10)]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function index()
    {
        Gate::authorize('viewAny', Partner::class);

        $partners = QueryBuilder::for(Partner::class)
            ->allowedFilters([
                AllowedFilter::exact('id'),
                AllowedFilter::exact('account_type'),
                AllowedFilter::partial('name'),
                AllowedFilter::partial('email'),
                AllowedFilter::partial('phone'),
                AllowedFilter::exact('parent_id'),
                AllowedFilter::exact('user_id'),
                AllowedFilter::exact('company_id'),
                AllowedFilter::exact('title_id'),
                AllowedFilter::exact('industry_id'),
                AllowedFilter::exact('country_id'),
                AllowedFilter::exact('state_id'),
                AllowedFilter::trashed(),
            ])
            ->allowedSorts(['id', 'name', 'email', 'created_at'])
            ->allowedIncludes([
                'parent',
                'country',
                'state',
                'title',
                'company',
                'industry',
                'user',
                'creator',
                'addresses',
                'contacts',
                'bankAccounts',
                'tags',
            ])
            ->paginate();

        return PartnerResource::collection($partners);
    }

    #[Endpoint('Create partner', 'Create a new partner')]
    #[ResponseFromApiResource(PartnerResource::class, Partner::class, status: 201, additional: ['message' => 'Partner created successfully.'])]
    #[Response(status: 422, description: 'Validation error', content: '{"message": "The given data was invalid.", "errors": {"name": ["The name field is required."]}}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function store(PartnerRequest $request)
    {
        Gate::authorize('create', Partner::class);

        $partner = Partner::create($request->validated());

        return (new PartnerResource($partner))
            ->additional(['message' => 'Partner created successfully.'])
            ->response()
            ->setStatusCode(201);
    }

    #[Endpoint('Show partner', 'Retrieve a specific partner by its ID')]
    #[UrlParam('id', 'integer', 'The partner ID', required: true, example: 1)]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> parent, country, state, title, company, industry, user, creator, addresses, contacts, bankAccounts, tags', required: false, example: 'tags,addresses')]
    #[ResponseFromApiResource(PartnerResource::class, Partner::class)]
    #[Response(status: 404, description: 'Partner not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function show(string $id)
    {
        $partner = QueryBuilder::for(Partner::where('id', $id))
            ->allowedIncludes([
                'parent',
                'country',
                'state',
                'title',
                'company',
                'industry',
                'user',
                'creator',
                'addresses',
                'contacts',
                'bankAccounts',
                'tags',
            ])
            ->firstOrFail();

        Gate::authorize('view', $partner);

        return new PartnerResource($partner);
    }

    #[Endpoint('Update partner', 'Update an existing partner')]
    #[UrlParam('id', 'integer', 'The partner ID', required: true, example: 1)]
    #[ResponseFromApiResource(PartnerResource::class, Partner::class, additional: ['message' => 'Partner updated successfully.'])]
    #[Response(status: 422, description: 'Validation error', content: '{"message": "The given data was invalid.", "errors": {"name": ["The name field must not exceed 255 characters."]}}')]
    #[Response(status: 404, description: 'Partner not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function update(PartnerRequest $request, string $id)
    {
        $partner = Partner::findOrFail($id);

        Gate::authorize('update', $partner);

        $partner->update($request->validated());

        return (new PartnerResource($partner))
            ->additional(['message' => 'Partner updated successfully.']);
    }

    #[Endpoint('Delete partner', 'Soft delete a partner')]
    #[UrlParam('id', 'integer', 'The partner ID', required: true, example: 1)]
    #[Response(status: 200, description: 'Partner deleted successfully', content: '{"message": "Partner deleted successfully."}')]
    #[Response(status: 404, description: 'Partner not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function destroy(string $id)
    {
        $partner = Partner::findOrFail($id);

        Gate::authorize('delete', $partner);

        $partner->delete();

        return response()->json(['message' => 'Partner deleted successfully.']);
    }

    #[Endpoint('Restore partner', 'Restore a soft-deleted partner')]
    #[UrlParam('id', 'integer', 'The partner ID', required: true, example: 1)]
    #[Response(status: 200, description: 'Partner restored successfully', content: '{"message": "Partner restored successfully."}')]
    #[Response(status: 404, description: 'Partner not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function restore(string $id)
    {
        $partner = Partner::onlyTrashed()->findOrFail($id);

        Gate::authorize('restore', $partner);

        $partner->restore();

        return response()->json(['message' => 'Partner restored successfully.']);
    }

    #[Endpoint('Force delete partner', 'Permanently delete a partner')]
    #[UrlParam('id', 'integer', 'The partner ID', required: true, example: 1)]
    #[Response(status: 200, description: 'Partner permanently deleted', content: '{"message": "Partner permanently deleted."}')]
    #[Response(status: 404, description: 'Partner not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function forceDestroy(string $id)
    {
        $partner = Partner::withTrashed()->findOrFail($id);

        Gate::authorize('forceDelete', $partner);

        $partner->forceDelete();

        return response()->json(['message' => 'Partner permanently deleted.']);
    }
}
