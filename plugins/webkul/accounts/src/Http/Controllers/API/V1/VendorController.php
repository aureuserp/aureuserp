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
use Webkul\Account\Http\Requests\VendorRequest;
use Webkul\Account\Http\Resources\V1\PartnerResource;
use Webkul\Account\Models\Partner;

#[Group('Account API Management')]
#[Subgroup('Vendors', 'Manage vendors/suppliers with accounting properties')]
#[Authenticated]
class VendorController extends Controller
{
    #[Endpoint('List vendors', 'Retrieve a paginated list of vendors with filtering and sorting')]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> parent, country, state, title, company, industry, user, creator, addresses, contacts, bankAccounts, tags, propertyAccountPayable, propertyAccountReceivable, propertyAccountPosition, propertyPaymentTerm, propertySupplierPaymentTerm, propertyOutboundPaymentMethodLine, propertyInboundPaymentMethodLine', required: false, example: 'propertyAccountPayable,propertySupplierPaymentTerm')]
    #[QueryParam('filter[id]', 'string', 'Comma-separated list of IDs to filter by', required: false, example: 'No-example')]
    #[QueryParam('filter[name]', 'string', 'Filter by vendor name (partial match)', required: false, example: 'No-example')]
    #[QueryParam('filter[email]', 'string', 'Filter by email (partial match)', required: false, example: 'No-example')]
    #[QueryParam('filter[phone]', 'string', 'Filter by phone (partial match)', required: false, example: 'No-example')]
    #[QueryParam('filter[parent_id]', 'string', 'Comma-separated list of parent IDs to filter by', required: false, example: 'No-example')]
    #[QueryParam('filter[company_id]', 'string', 'Comma-separated list of company IDs to filter by', required: false, example: 'No-example')]
    #[QueryParam('filter[country_id]', 'string', 'Comma-separated list of country IDs to filter by', required: false, example: 'No-example')]
    #[QueryParam('filter[trashed]', 'string', 'Filter by trashed status. </br></br><b>Available options:</b> with, without, only', required: false, example: 'No-example')]
    #[QueryParam('sort', 'string', 'Sort field', example: 'name')]
    #[QueryParam('page', 'int', 'Page number', example: 1)]
    #[ResponseFromApiResource(PartnerResource::class, Partner::class, collection: true, paginate: 10)]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function index()
    {
        Gate::authorize('viewAny', Partner::class);

        $vendors = QueryBuilder::for(Partner::class)
            ->where('supplier_rank', '>', 0)
            ->allowedFilters([
                AllowedFilter::exact('id'),
                AllowedFilter::partial('name'),
                AllowedFilter::partial('email'),
                AllowedFilter::partial('phone'),
                AllowedFilter::exact('parent_id'),
                AllowedFilter::exact('company_id'),
                AllowedFilter::exact('country_id'),
                AllowedFilter::trashed(),
            ])
            ->allowedSorts(['id', 'name', 'email', 'supplier_rank', 'created_at'])
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
                'propertyAccountPayable',
                'propertyAccountReceivable',
                'propertyAccountPosition',
                'propertyPaymentTerm',
                'propertySupplierPaymentTerm',
                'propertyOutboundPaymentMethodLine',
                'propertyInboundPaymentMethodLine',
            ])
            ->paginate();

        return PartnerResource::collection($vendors);
    }

    #[Endpoint('Create vendor', 'Create a new vendor')]
    #[ResponseFromApiResource(PartnerResource::class, Partner::class, status: 201, additional: ['message' => 'Vendor created successfully.'])]
    #[Response(status: 422, description: 'Validation error', content: '{"message": "The given data was invalid.", "errors": {"name": ["The name field is required."]}}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function store(VendorRequest $request)
    {
        Gate::authorize('create', Partner::class);

        $data = $request->validated();
        if (! isset($data['supplier_rank']) || $data['supplier_rank'] <= 0) {
            $data['supplier_rank'] = 1;
        }

        $vendor = Partner::create($data);

        return (new PartnerResource($vendor))
            ->additional(['message' => 'Vendor created successfully.'])
            ->response()
            ->setStatusCode(201);
    }

    #[Endpoint('Show vendor', 'Retrieve a specific vendor by its ID')]
    #[UrlParam('id', 'integer', 'The vendor ID', required: true, example: 1)]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> parent, country, state, title, company, industry, user, creator, addresses, contacts, bankAccounts, tags, propertyAccountPayable, propertyAccountReceivable, propertyAccountPosition, propertyPaymentTerm, propertySupplierPaymentTerm, propertyOutboundPaymentMethodLine, propertyInboundPaymentMethodLine', required: false, example: 'propertyAccountPayable,propertySupplierPaymentTerm')]
    #[ResponseFromApiResource(PartnerResource::class, Partner::class)]
    #[Response(status: 404, description: 'Vendor not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function show(string $id)
    {
        $vendor = QueryBuilder::for(Partner::where('id', $id)->where('supplier_rank', '>', 0))
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
                'propertyAccountPayable',
                'propertyAccountReceivable',
                'propertyAccountPosition',
                'propertyPaymentTerm',
                'propertySupplierPaymentTerm',
                'propertyOutboundPaymentMethodLine',
                'propertyInboundPaymentMethodLine',
            ])
            ->firstOrFail();

        Gate::authorize('view', $vendor);

        return new PartnerResource($vendor);
    }

    #[Endpoint('Update vendor', 'Update an existing vendor')]
    #[UrlParam('id', 'integer', 'The vendor ID', required: true, example: 1)]
    #[ResponseFromApiResource(PartnerResource::class, Partner::class, additional: ['message' => 'Vendor updated successfully.'])]
    #[Response(status: 422, description: 'Validation error', content: '{"message": "The given data was invalid.", "errors": {"name": ["The name field must not exceed 255 characters."]}}')]
    #[Response(status: 404, description: 'Vendor not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function update(VendorRequest $request, string $id)
    {
        $vendor = Partner::where('supplier_rank', '>', 0)->findOrFail($id);

        Gate::authorize('update', $vendor);

        $vendor->update($request->validated());

        return (new PartnerResource($vendor))
            ->additional(['message' => 'Vendor updated successfully.']);
    }

    #[Endpoint('Delete vendor', 'Soft delete a vendor')]
    #[UrlParam('id', 'integer', 'The vendor ID', required: true, example: 1)]
    #[Response(status: 200, description: 'Vendor deleted', content: '{"message": "Vendor deleted successfully."}')]
    #[Response(status: 404, description: 'Vendor not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function destroy(string $id)
    {
        $vendor = Partner::where('supplier_rank', '>', 0)->findOrFail($id);

        Gate::authorize('delete', $vendor);

        $vendor->delete();

        return response()->json([
            'message' => 'Vendor deleted successfully.',
        ]);
    }

    #[Endpoint('Restore vendor', 'Restore a soft deleted vendor')]
    #[UrlParam('id', 'integer', 'The vendor ID', required: true, example: 1)]
    #[Response(status: 200, description: 'Vendor restored', content: '{"message": "Vendor restored successfully."}')]
    #[Response(status: 404, description: 'Vendor not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function restore(string $id)
    {
        $vendor = Partner::where('supplier_rank', '>', 0)->withTrashed()->findOrFail($id);

        Gate::authorize('restore', $vendor);

        $vendor->restore();

        return response()->json([
            'message' => 'Vendor restored successfully.',
        ]);
    }

    #[Endpoint('Force delete vendor', 'Permanently delete a vendor')]
    #[UrlParam('id', 'integer', 'The vendor ID', required: true, example: 1)]
    #[Response(status: 200, description: 'Vendor permanently deleted', content: '{"message": "Vendor permanently deleted."}')]
    #[Response(status: 404, description: 'Vendor not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function forceDestroy(string $id)
    {
        $vendor = Partner::where('supplier_rank', '>', 0)->withTrashed()->findOrFail($id);

        Gate::authorize('forceDelete', $vendor);

        $vendor->forceDelete();

        return response()->json([
            'message' => 'Vendor permanently deleted.',
        ]);
    }
}
