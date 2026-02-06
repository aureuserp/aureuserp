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
use Webkul\Account\Enums\DelayType;
use Webkul\Account\Enums\DueTermValue;
use Webkul\Account\Http\Requests\PaymentDueTermRequest;
use Webkul\Account\Http\Resources\V1\PaymentDueTermResource;
use Webkul\Account\Models\PaymentDueTerm;
use Webkul\Account\Models\PaymentTerm;

#[Group('Account API Management')]
#[Subgroup('Payment Due Terms', 'Manage payment term due terms')]
#[Authenticated]
class PaymentDueTermController extends Controller
{
    #[Endpoint('List payment due terms', 'Retrieve a paginated list of payment due terms for a specific payment term')]
    #[UrlParam('payment_term_id', 'integer', 'The payment term ID', required: true, example: 1)]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> paymentTerm, creator', required: false, example: 'paymentTerm')]
    #[QueryParam('filter[id]', 'string', 'Comma-separated list of IDs to filter by', required: false, example: 'No-example')]
    #[QueryParam('filter[value]', 'string', 'Filter by value type', enum: DueTermValue::class, required: false, example: 'No-example')]
    #[QueryParam('filter[delay_type]', 'string', 'Filter by delay type', enum: DelayType::class, required: false, example: 'No-example')]
    #[QueryParam('sort', 'string', 'Sort field', example: 'created_at')]
    #[QueryParam('page', 'int', 'Page number', example: 1)]
    #[ResponseFromApiResource(PaymentDueTermResource::class, PaymentDueTerm::class, collection: true, paginate: 10)]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function index(string $paymentTerm)
    {
        $paymentTermModel = PaymentTerm::findOrFail($paymentTerm);

        Gate::authorize('view', $paymentTermModel);

        $paymentDueTerms = QueryBuilder::for(PaymentDueTerm::where('payment_id', $paymentTerm))
            ->allowedFilters([
                AllowedFilter::exact('id'),
                AllowedFilter::exact('value'),
                AllowedFilter::exact('delay_type'),
            ])
            ->allowedSorts(['id', 'nb_days', 'created_at'])
            ->allowedIncludes([
                'paymentTerm',
                'creator',
            ])
            ->paginate();

        return PaymentDueTermResource::collection($paymentDueTerms);
    }

    #[Endpoint('Create payment due term', 'Create a new payment due term for a specific payment term')]
    #[UrlParam('payment_term_id', 'integer', 'The payment term ID', required: true, example: 1)]
    #[ResponseFromApiResource(PaymentDueTermResource::class, PaymentDueTerm::class, status: 201, additional: ['message' => 'Payment due term created successfully.'])]
    #[Response(status: 422, description: 'Validation error', content: '{"message": "The given data was invalid.", "errors": {"value": ["The value field is required."]}}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function store(PaymentDueTermRequest $request, string $paymentTerm)
    {
        $paymentTermModel = PaymentTerm::findOrFail($paymentTerm);

        Gate::authorize('update', $paymentTermModel);

        $data = $request->validated();
        $data['payment_id'] = $paymentTerm;

        $paymentDueTerm = PaymentDueTerm::create($data);

        return (new PaymentDueTermResource($paymentDueTerm->load(['paymentTerm'])))
            ->additional(['message' => 'Payment due term created successfully.'])
            ->response()
            ->setStatusCode(201);
    }

    #[Endpoint('Show payment due term', 'Retrieve a specific payment due term by its ID')]
    #[UrlParam('payment_term_id', 'integer', 'The payment term ID', required: true, example: 1)]
    #[UrlParam('id', 'integer', 'The payment due term ID', required: true, example: 1)]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> paymentTerm, creator', required: false, example: 'paymentTerm')]
    #[ResponseFromApiResource(PaymentDueTermResource::class, PaymentDueTerm::class)]
    #[Response(status: 404, description: 'Payment due term not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function show(string $paymentTerm, string $paymentDueTerm)
    {
        $paymentTermModel = PaymentTerm::findOrFail($paymentTerm);

        Gate::authorize('view', $paymentTermModel);

        $paymentDueTermModel = QueryBuilder::for(PaymentDueTerm::where('id', $paymentDueTerm)->where('payment_id', $paymentTerm))
            ->allowedIncludes([
                'paymentTerm',
                'creator',
            ])
            ->firstOrFail();

        return new PaymentDueTermResource($paymentDueTermModel);
    }

    #[Endpoint('Update payment due term', 'Update an existing payment due term')]
    #[UrlParam('payment_term_id', 'integer', 'The payment term ID', required: true, example: 1)]
    #[UrlParam('id', 'integer', 'The payment due term ID', required: true, example: 1)]
    #[ResponseFromApiResource(PaymentDueTermResource::class, PaymentDueTerm::class, additional: ['message' => 'Payment due term updated successfully.'])]
    #[Response(status: 404, description: 'Payment due term not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 422, description: 'Validation error', content: '{"message": "The given data was invalid."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function update(PaymentDueTermRequest $request, string $paymentTerm, string $paymentDueTerm)
    {
        $paymentTermModel = PaymentTerm::findOrFail($paymentTerm);

        Gate::authorize('update', $paymentTermModel);

        $paymentDueTermModel = PaymentDueTerm::where('id', $paymentDueTerm)->where('payment_id', $paymentTerm)->firstOrFail();

        $paymentDueTermModel->update($request->validated());

        return (new PaymentDueTermResource($paymentDueTermModel->load(['paymentTerm'])))
            ->additional(['message' => 'Payment due term updated successfully.']);
    }

    #[Endpoint('Delete payment due term', 'Delete a payment due term')]
    #[UrlParam('payment_term_id', 'integer', 'The payment term ID', required: true, example: 1)]
    #[UrlParam('id', 'integer', 'The payment due term ID', required: true, example: 1)]
    #[Response(status: 200, description: 'Payment due term deleted', content: '{"message": "Payment due term deleted successfully."}')]
    #[Response(status: 404, description: 'Payment due term not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function destroy(string $paymentTerm, string $paymentDueTerm)
    {
        $paymentTermModel = PaymentTerm::findOrFail($paymentTerm);

        Gate::authorize('update', $paymentTermModel);

        $paymentDueTermModel = PaymentDueTerm::where('id', $paymentDueTerm)->where('payment_id', $paymentTerm)->firstOrFail();

        $paymentDueTermModel->delete();

        return response()->json([
            'message' => 'Payment due term deleted successfully.',
        ]);
    }
}
