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
use Webkul\Account\Http\Requests\PaymentTermRequest;
use Webkul\Account\Http\Resources\V1\PaymentTermResource;
use Webkul\Account\Models\PaymentTerm;

#[Group('Account API Management')]
#[Subgroup('Payment Terms', 'Manage payment terms')]
#[Authenticated]
class PaymentTermController extends Controller
{
    #[Endpoint('List payment terms', 'Retrieve a paginated list of payment terms with filtering and sorting')]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> company, creator, dueTerms', required: false, example: 'dueTerms')]
    #[QueryParam('filter[id]', 'string', 'Comma-separated list of IDs to filter by', required: false, example: 'No-example')]
    #[QueryParam('filter[name]', 'string', 'Filter by payment term name (partial match)', required: false, example: 'No-example')]
    #[QueryParam('filter[company_id]', 'int', 'Filter by company ID', required: false, example: 'No-example')]
    #[QueryParam('filter[trashed]', 'string', 'Filter by trashed status. Options: with, only', required: false, example: 'No-example')]
    #[QueryParam('sort', 'string', 'Sort field', example: 'name')]
    #[QueryParam('page', 'int', 'Page number', example: 1)]
    #[ResponseFromApiResource(PaymentTermResource::class, PaymentTerm::class, collection: true, paginate: 10, with: ['dueTerms'])]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function index()
    {
        Gate::authorize('viewAny', PaymentTerm::class);

        $paymentTerms = QueryBuilder::for(PaymentTerm::class)
            ->allowedFilters([
                AllowedFilter::exact('id'),
                AllowedFilter::partial('name'),
                AllowedFilter::exact('company_id'),
                AllowedFilter::trashed(),
            ])
            ->allowedSorts(['id', 'name', 'sort', 'created_at'])
            ->allowedIncludes([
                'company',
                'creator',
                'dueTerms',
            ])
            ->paginate();

        return PaymentTermResource::collection($paymentTerms);
    }

    #[Endpoint('Create payment term', 'Create a new payment term')]
    #[ResponseFromApiResource(PaymentTermResource::class, PaymentTerm::class, status: 201, additional: ['message' => 'Payment term created successfully.'])]
    #[Response(status: 422, description: 'Validation error', content: '{"message": "The given data was invalid.", "errors": {"name": ["The name field is required."]}}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function store(PaymentTermRequest $request)
    {
        Gate::authorize('create', PaymentTerm::class);

        $data = $request->validated();

        $paymentTerm = PaymentTerm::create($data);

        return (new PaymentTermResource($paymentTerm))
            ->additional(['message' => 'Payment term created successfully.'])
            ->response()
            ->setStatusCode(201);
    }

    #[Endpoint('Show payment term', 'Retrieve a specific payment term by its ID')]
    #[UrlParam('id', 'integer', 'The payment term ID', required: true, example: 1)]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> company, creator, dueTerms', required: false, example: 'company')]
    #[ResponseFromApiResource(PaymentTermResource::class, PaymentTerm::class, with: ['dueTerms'])]
    #[Response(status: 404, description: 'Payment term not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function show(string $id)
    {
        $paymentTerm = QueryBuilder::for(PaymentTerm::where('id', $id))
            ->allowedIncludes([
                'company',
                'creator',
                'dueTerms',
            ])
            ->firstOrFail();

        Gate::authorize('view', $paymentTerm);

        return new PaymentTermResource($paymentTerm);
    }

    #[Endpoint('Update payment term', 'Update an existing payment term')]
    #[UrlParam('id', 'integer', 'The payment term ID', required: true, example: 1)]
    #[ResponseFromApiResource(PaymentTermResource::class, PaymentTerm::class, additional: ['message' => 'Payment term updated successfully.'])]
    #[Response(status: 404, description: 'Payment term not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 422, description: 'Validation error', content: '{"message": "The given data was invalid."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function update(PaymentTermRequest $request, string $id)
    {
        $paymentTerm = PaymentTerm::findOrFail($id);

        Gate::authorize('update', $paymentTerm);

        $paymentTerm->update($request->validated());

        return (new PaymentTermResource($paymentTerm))
            ->additional(['message' => 'Payment term updated successfully.']);
    }

    #[Endpoint('Delete payment term', 'Soft delete a payment term')]
    #[UrlParam('id', 'integer', 'The payment term ID', required: true, example: 1)]
    #[Response(status: 200, description: 'Payment term deleted', content: '{"message": "Payment term deleted successfully."}')]
    #[Response(status: 404, description: 'Payment term not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function destroy(string $id)
    {
        $paymentTerm = PaymentTerm::findOrFail($id);

        Gate::authorize('delete', $paymentTerm);

        $paymentTerm->delete();

        return response()->json([
            'message' => 'Payment term deleted successfully.',
        ]);
    }

    #[Endpoint('Restore payment term', 'Restore a soft-deleted payment term')]
    #[UrlParam('id', 'integer', 'The payment term ID', required: true, example: 1)]
    #[ResponseFromApiResource(PaymentTermResource::class, PaymentTerm::class, additional: ['message' => 'Payment term restored successfully.'])]
    #[Response(status: 404, description: 'Payment term not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function restore(string $id)
    {
        $paymentTerm = PaymentTerm::withTrashed()->findOrFail($id);

        Gate::authorize('restore', $paymentTerm);

        $paymentTerm->restore();

        return (new PaymentTermResource($paymentTerm))
            ->additional(['message' => 'Payment term restored successfully.']);
    }

    #[Endpoint('Force delete payment term', 'Permanently delete a payment term')]
    #[UrlParam('id', 'integer', 'The payment term ID', required: true, example: 1)]
    #[Response(status: 200, description: 'Payment term permanently deleted', content: '{"message": "Payment term permanently deleted."}')]
    #[Response(status: 404, description: 'Payment term not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function forceDestroy(string $id)
    {
        $paymentTerm = PaymentTerm::withTrashed()->findOrFail($id);

        Gate::authorize('forceDelete', $paymentTerm);

        $paymentTerm->forceDelete();

        return response()->json([
            'message' => 'Payment term permanently deleted.',
        ]);
    }
}
