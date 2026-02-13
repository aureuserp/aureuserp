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
use Webkul\Partner\Enums\AccountType;
use Webkul\Partner\Enums\AddressType;
use Webkul\Partner\Http\Requests\AddressRequest;
use Webkul\Partner\Http\Resources\V1\AddressResource;
use Webkul\Partner\Models\Partner;

#[Group('Partner API Management')]
#[Subgroup('Addresses', 'Manage partner addresses')]
#[Authenticated]
class AddressController extends Controller
{
    #[Endpoint('List addresses', 'Retrieve a paginated list of addresses for a specific partner with filtering and sorting')]
    #[UrlParam('partner_id', 'integer', 'The partner ID', required: true, example: 1)]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> creator', required: false, example: 'creator')]
    #[QueryParam('filter[id]', 'string', 'Comma-separated list of IDs to filter by', required: false, example: 'No-example')]
    #[QueryParam('filter[sub_type]', 'string', 'Filter by address type', enum: AddressType::class, required: false, example: 'No-example')]
    #[QueryParam('filter[name]', 'string', 'Filter by address name (partial match)', required: false, example: 'No-example')]
    #[QueryParam('filter[city]', 'string', 'Filter by city (partial match)', required: false, example: 'No-example')]
    #[QueryParam('filter[country_id]', 'string', 'Comma-separated list of country IDs to filter by', required: false, example: 'No-example')]
    #[QueryParam('filter[state_id]', 'string', 'Comma-separated list of state IDs to filter by', required: false, example: 'No-example')]
    #[QueryParam('filter[trashed]', 'string', 'Filter by trashed status. </br></br><b>Available options:</b> with, without, only', required: false, example: 'No-example')]
    #[QueryParam('sort', 'string', 'Sort field', example: 'name')]
    #[QueryParam('page', 'int', 'Page number', example: 1)]
    #[ResponseFromApiResource(AddressResource::class, Partner::class, collection: true, paginate: 10)]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function index(string $partner)
    {
        $partnerModel = Partner::findOrFail($partner);

        Gate::authorize('view', $partnerModel);

        $addresses = QueryBuilder::for(Partner::where('parent_id', $partner)->where('account_type', AccountType::ADDRESS))
            ->allowedFilters([
                AllowedFilter::exact('id'),
                AllowedFilter::exact('sub_type'),
                AllowedFilter::partial('name'),
                AllowedFilter::partial('city'),
                AllowedFilter::exact('country_id'),
                AllowedFilter::exact('state_id'),
                AllowedFilter::trashed(),
            ])
            ->allowedSorts(['id', 'name', 'city', 'created_at'])
            ->allowedIncludes([
                'creator',
            ])
            ->paginate();

        return AddressResource::collection($addresses);
    }

    #[Endpoint('Create address', 'Create a new address for a specific partner')]
    #[UrlParam('partner_id', 'integer', 'The partner ID', required: true, example: 1)]
    #[ResponseFromApiResource(AddressResource::class, Partner::class, status: 201, additional: ['message' => 'Address created successfully.'])]
    #[Response(status: 422, description: 'Validation error', content: '{"message": "The given data was invalid.", "errors": {"name": ["The name field is required."]}}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function store(AddressRequest $request, string $partner)
    {
        $partnerModel = Partner::findOrFail($partner);

        Gate::authorize('update', $partnerModel);

        $data = $request->validated();
        $data['parent_id'] = $partner;
        $data['account_type'] = AccountType::ADDRESS;

        $address = Partner::create($data);

        return (new AddressResource($address))
            ->additional(['message' => 'Address created successfully.'])
            ->response()
            ->setStatusCode(201);
    }

    #[Endpoint('Show address', 'Retrieve a specific address by its ID')]
    #[UrlParam('partner_id', 'integer', 'The partner ID', required: true, example: 1)]
    #[UrlParam('id', 'integer', 'The address ID', required: true, example: 1)]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> creator', required: false, example: 'country,state')]
    #[ResponseFromApiResource(AddressResource::class, Partner::class, with: ['parent'])]
    #[Response(status: 404, description: 'Address not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function show(string $partner, string $address)
    {
        $partnerModel = Partner::findOrFail($partner);

        Gate::authorize('view', $partnerModel);

        $addressModel = QueryBuilder::for(
            Partner::where('id', $address)
                ->where('parent_id', $partner)
                ->where('account_type', AccountType::ADDRESS)
        )
            ->allowedIncludes([
                'creator',
            ])
            ->firstOrFail();

        return new AddressResource($addressModel);
    }

    #[Endpoint('Update address', 'Update an existing address')]
    #[UrlParam('partner_id', 'integer', 'The partner ID', required: true, example: 1)]
    #[UrlParam('id', 'integer', 'The address ID', required: true, example: 1)]
    #[ResponseFromApiResource(AddressResource::class, Partner::class, with: ['parent'], additional: ['message' => 'Address updated successfully.'])]
    #[Response(status: 422, description: 'Validation error', content: '{"message": "The given data was invalid.", "errors": {"name": ["The name field must be a string."]}}')]
    #[Response(status: 404, description: 'Address not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function update(AddressRequest $request, string $partner, string $address)
    {
        $partnerModel = Partner::findOrFail($partner);

        Gate::authorize('update', $partnerModel);

        $addressModel = Partner::where('id', $address)
            ->where('parent_id', $partner)
            ->where('account_type', AccountType::ADDRESS)
            ->firstOrFail();

        $addressModel->update($request->validated());

        return (new AddressResource($addressModel->load(['parent'])))
            ->additional(['message' => 'Address updated successfully.']);
    }

    #[Endpoint('Delete address', 'Soft delete an address')]
    #[UrlParam('partner_id', 'integer', 'The partner ID', required: true, example: 1)]
    #[UrlParam('id', 'integer', 'The address ID', required: true, example: 1)]
    #[Response(status: 200, description: 'Address deleted successfully', content: '{"message": "Address deleted successfully."}')]
    #[Response(status: 404, description: 'Address not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function destroy(string $partner, string $address)
    {
        $partnerModel = Partner::findOrFail($partner);

        Gate::authorize('update', $partnerModel);

        $addressModel = Partner::where('id', $address)
            ->where('parent_id', $partner)
            ->where('account_type', AccountType::ADDRESS)
            ->firstOrFail();

        $addressModel->delete();

        return response()->json([
            'message' => 'Address deleted successfully.',
        ]);
    }

    #[Endpoint('Restore address', 'Restore a soft-deleted address')]
    #[UrlParam('partner_id', 'integer', 'The partner ID', required: true, example: 1)]
    #[UrlParam('id', 'integer', 'The address ID', required: true, example: 1)]
    #[Response(status: 200, description: 'Address restored successfully', content: '{"message": "Address restored successfully."}')]
    #[Response(status: 404, description: 'Address not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function restore(string $partner, string $address)
    {
        $partnerModel = Partner::findOrFail($partner);

        Gate::authorize('update', $partnerModel);

        $addressModel = Partner::onlyTrashed()
            ->where('id', $address)
            ->where('parent_id', $partner)
            ->where('account_type', AccountType::ADDRESS)
            ->firstOrFail();

        $addressModel->restore();

        return response()->json([
            'message' => 'Address restored successfully.',
        ]);
    }

    #[Endpoint('Force delete address', 'Permanently delete an address')]
    #[UrlParam('partner_id', 'integer', 'The partner ID', required: true, example: 1)]
    #[UrlParam('id', 'integer', 'The address ID', required: true, example: 1)]
    #[Response(status: 200, description: 'Address permanently deleted', content: '{"message": "Address permanently deleted."}')]
    #[Response(status: 404, description: 'Address not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function forceDestroy(string $partner, string $address)
    {
        $partnerModel = Partner::findOrFail($partner);
        
        Gate::authorize('update', $partnerModel);

        $addressModel = Partner::withTrashed()
            ->where('id', $address)
            ->where('parent_id', $partner)
            ->where('account_type', AccountType::ADDRESS)
            ->firstOrFail();

        $addressModel->forceDelete();

        return response()->json([
            'message' => 'Address permanently deleted.',
        ]);
    }
}
