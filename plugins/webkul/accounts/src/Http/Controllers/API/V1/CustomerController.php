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
use Webkul\Account\Http\Requests\CustomerRequest;
use Webkul\Account\Http\Resources\V1\PartnerResource;
use Webkul\Account\Models\Partner;

#[Group('Account API Management')]
#[Subgroup('Customers', 'Manage customers with accounting properties')]
#[Authenticated]
class CustomerController extends Controller
{
    #[Endpoint('List customers', 'Retrieve a paginated list of customers with filtering and sorting')]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> parent, country, state, title, company, industry, user, creator, addresses, contacts, bankAccounts, tags, propertyAccountPayable, propertyAccountReceivable, propertyAccountPosition, propertyPaymentTerm, propertySupplierPaymentTerm, propertyOutboundPaymentMethodLine, propertyInboundPaymentMethodLine', required: false, example: 'propertyAccountReceivable,propertyPaymentTerm')]
    #[QueryParam('filter[id]', 'string', 'Comma-separated list of IDs to filter by', required: false, example: 'No-example')]
    #[QueryParam('filter[name]', 'string', 'Filter by customer name (partial match)', required: false, example: 'No-example')]
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

        $customers = QueryBuilder::for(Partner::class)
            ->where('customer_rank', '>', 0)
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
            ->allowedSorts(['id', 'name', 'email', 'customer_rank', 'created_at'])
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

        return PartnerResource::collection($customers);
    }

    #[Endpoint('Create customer', 'Create a new customer')]
    #[ResponseFromApiResource(PartnerResource::class, Partner::class, status: 201, additional: ['message' => 'Customer created successfully.'])]
    #[Response(status: 422, description: 'Validation error', content: '{"message": "The given data was invalid.", "errors": {"name": ["The name field is required."]}}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function store(CustomerRequest $request)
    {
        Gate::authorize('create', Partner::class);

        $data = $request->validated();
        if (! isset($data['customer_rank']) || $data['customer_rank'] <= 0) {
            $data['customer_rank'] = 1;
        }

        $customer = Partner::create($data);

        return (new PartnerResource($customer))
            ->additional(['message' => 'Customer created successfully.'])
            ->response()
            ->setStatusCode(201);
    }

    #[Endpoint('Show customer', 'Retrieve a specific customer by its ID')]
    #[UrlParam('id', 'integer', 'The customer ID', required: true, example: 1)]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> parent, country, state, title, company, industry, user, creator, addresses, contacts, bankAccounts, tags, propertyAccountPayable, propertyAccountReceivable, propertyAccountPosition, propertyPaymentTerm, propertySupplierPaymentTerm, propertyOutboundPaymentMethodLine, propertyInboundPaymentMethodLine', required: false, example: 'propertyAccountReceivable,propertyPaymentTerm')]
    #[ResponseFromApiResource(PartnerResource::class, Partner::class)]
    #[Response(status: 404, description: 'Customer not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function show(string $id)
    {
        $customer = QueryBuilder::for(Partner::where('id', $id)->where('customer_rank', '>', 0))
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

        Gate::authorize('view', $customer);

        return new PartnerResource($customer);
    }

    #[Endpoint('Update customer', 'Update an existing customer')]
    #[UrlParam('id', 'integer', 'The customer ID', required: true, example: 1)]
    #[ResponseFromApiResource(PartnerResource::class, Partner::class, additional: ['message' => 'Customer updated successfully.'])]
    #[Response(status: 422, description: 'Validation error', content: '{"message": "The given data was invalid.", "errors": {"name": ["The name field must not exceed 255 characters."]}}')]
    #[Response(status: 404, description: 'Customer not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function update(CustomerRequest $request, string $id)
    {
        $customer = Partner::where('customer_rank', '>', 0)->findOrFail($id);

        Gate::authorize('update', $customer);

        $customer->update($request->validated());

        return (new PartnerResource($customer))
            ->additional(['message' => 'Customer updated successfully.']);
    }

    #[Endpoint('Delete customer', 'Soft delete a customer')]
    #[UrlParam('id', 'integer', 'The customer ID', required: true, example: 1)]
    #[Response(status: 200, description: 'Customer deleted', content: '{"message": "Customer deleted successfully."}')]
    #[Response(status: 404, description: 'Customer not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function destroy(string $id)
    {
        $customer = Partner::where('customer_rank', '>', 0)->findOrFail($id);

        Gate::authorize('delete', $customer);

        $customer->delete();

        return response()->json([
            'message' => 'Customer deleted successfully.',
        ]);
    }

    #[Endpoint('Restore customer', 'Restore a soft deleted customer')]
    #[UrlParam('id', 'integer', 'The customer ID', required: true, example: 1)]
    #[Response(status: 200, description: 'Customer restored', content: '{"message": "Customer restored successfully."}')]
    #[Response(status: 404, description: 'Customer not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function restore(string $id)
    {
        $customer = Partner::where('customer_rank', '>', 0)->withTrashed()->findOrFail($id);

        Gate::authorize('restore', $customer);

        $customer->restore();

        return response()->json([
            'message' => 'Customer restored successfully.',
        ]);
    }

    #[Endpoint('Force delete customer', 'Permanently delete a customer')]
    #[UrlParam('id', 'integer', 'The customer ID', required: true, example: 1)]
    #[Response(status: 200, description: 'Customer permanently deleted', content: '{"message": "Customer permanently deleted."}')]
    #[Response(status: 404, description: 'Customer not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function forceDestroy(string $id)
    {
        $customer = Partner::where('customer_rank', '>', 0)->withTrashed()->findOrFail($id);

        Gate::authorize('forceDelete', $customer);

        $customer->forceDelete();

        return response()->json([
            'message' => 'Customer permanently deleted.',
        ]);
    }
}
