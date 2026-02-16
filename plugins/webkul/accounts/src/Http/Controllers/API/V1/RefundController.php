<?php

namespace Webkul\Account\Http\Controllers\API\V1;

use Illuminate\Support\Facades\DB;
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
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Facades\Account as AccountFacade;
use Webkul\Account\Http\Requests\RefundRequest;
use Webkul\Account\Http\Resources\V1\RefundResource;
use Webkul\Account\Models\Refund;

#[Group('Account API Management')]
#[Subgroup('Refunds', 'Manage customer refunds')]
#[Authenticated]
class RefundController extends Controller
{
    #[Endpoint('List refunds', 'Retrieve a paginated list of customer refunds with filtering and sorting')]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> partner, currency, journal, company, invoicePaymentTerm, fiscalPosition, invoiceUser, partnerShipping, partnerBank, invoiceIncoterm, invoiceCashRounding, paymentMethodLine, campaign, source, medium, creator, invoiceLines, invoiceLines.product, invoiceLines.uom, invoiceLines.taxes, invoiceLines.account, invoiceLines.currency, invoiceLines.companyCurrency, invoiceLines.partner, invoiceLines.creator, invoiceLines.journal, invoiceLines.company, invoiceLines.groupTax, invoiceLines.taxGroup, invoiceLines.payment, invoiceLines.taxRepartitionLine', required: false, example: 'partner,invoiceLines.product')]
    #[QueryParam('filter[id]', 'string', 'Comma-separated list of IDs to filter by', required: false, example: 'No-example')]
    #[QueryParam('filter[name]', 'string', 'Filter by refund number (partial match)', required: false, example: 'No-example')]
    #[QueryParam('filter[partner_id]', 'string', 'Comma-separated list of partner IDs to filter by', required: false, example: 'No-example')]
    #[QueryParam('filter[state]', 'string', 'Filter by state (draft, posted, cancel)', required: false, example: 'No-example')]
    #[QueryParam('filter[payment_state]', 'string', 'Filter by payment state', required: false, example: 'No-example')]
    #[QueryParam('sort', 'string', 'Sort field', example: 'invoice_date')]
    #[QueryParam('page', 'int', 'Page number', example: 1)]
    #[ResponseFromApiResource(RefundResource::class, Refund::class, collection: true, paginate: 10)]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function index()
    {
        Gate::authorize('viewAny', Refund::class);

        $refunds = QueryBuilder::for(Refund::class)
            ->where('move_type', MoveType::OUT_REFUND)
            ->allowedFilters([
                AllowedFilter::exact('id'),
                AllowedFilter::partial('name'),
                AllowedFilter::exact('partner_id'),
                AllowedFilter::exact('state'),
                AllowedFilter::exact('payment_state'),
                AllowedFilter::exact('invoice_user_id'),
                AllowedFilter::exact('journal_id'),
                AllowedFilter::exact('currency_id'),
            ])
            ->allowedSorts(['id', 'name', 'invoice_date', 'invoice_date_due', 'amount_total', 'created_at'])
            ->allowedIncludes([
                'partner',
                'currency',
                'journal',
                'company',
                'invoicePaymentTerm',
                'fiscalPosition',
                'invoiceUser',
                'partnerShipping',
                'partnerBank',
                'invoiceIncoterm',
                'invoiceCashRounding',
                'paymentMethodLine',
                'campaign',
                'source',
                'medium',
                'creator',
                'invoiceLines',
                'invoiceLines.product',
                'invoiceLines.uom',
                'invoiceLines.taxes',
                'invoiceLines.account',
                'invoiceLines.currency',
                'invoiceLines.companyCurrency',
                'invoiceLines.partner',
                'invoiceLines.creator',
                'invoiceLines.journal',
                'invoiceLines.company',
                'invoiceLines.groupTax',
                'invoiceLines.taxGroup',
                'invoiceLines.payment',
                'invoiceLines.taxRepartitionLine',
            ])
            ->paginate();

        return RefundResource::collection($refunds);
    }

    #[Endpoint('Create refund', 'Create a new customer refund')]
    #[ResponseFromApiResource(RefundResource::class, Refund::class, status: 201, additional: ['message' => 'Refund created successfully.'])]
    #[Response(status: 422, description: 'Validation error', content: '{"message": "The given data was invalid.", "errors": {"partner_id": ["The partner id field is required."]}}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function store(RefundRequest $request)
    {
        Gate::authorize('create', Refund::class);

        $data = $request->validated();

        return DB::transaction(function () use ($data) {
            $invoiceLines = $data['invoice_lines'];
            unset($data['invoice_lines']);

            $data['move_type'] = MoveType::OUT_REFUND;
            $data['state'] = MoveState::DRAFT;

            $refund = Refund::create($data);

            foreach ($invoiceLines as $lineData) {
                $taxes = $lineData['taxes'] ?? [];
                unset($lineData['taxes']);

                $moveLine = $refund->invoiceLines()->create($lineData);

                if (! empty($taxes)) {
                    $moveLine->taxes()->sync($taxes);
                }
            }

            $refund = AccountFacade::computeAccountMove($refund);

            $refund->load(['invoiceLines.product', 'invoiceLines.uom', 'invoiceLines.taxes']);

            return (new RefundResource($refund))
                ->additional(['message' => 'Refund created successfully.'])
                ->response()
                ->setStatusCode(201);
        });
    }

    #[Endpoint('Show refund', 'Retrieve a specific refund by its ID')]
    #[UrlParam('id', 'integer', 'The refund ID', required: true, example: 1)]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> partner, currency, journal, company, invoicePaymentTerm, fiscalPosition, invoiceUser, partnerShipping, partnerBank, invoiceIncoterm, invoiceCashRounding, paymentMethodLine, campaign, source, medium, creator, invoiceLines, invoiceLines.product, invoiceLines.uom, invoiceLines.taxes, invoiceLines.account, invoiceLines.currency, invoiceLines.companyCurrency, invoiceLines.partner, invoiceLines.creator, invoiceLines.journal, invoiceLines.company, invoiceLines.groupTax, invoiceLines.taxGroup, invoiceLines.payment, invoiceLines.taxRepartitionLine', required: false, example: 'partner,invoiceLines')]
    #[ResponseFromApiResource(RefundResource::class, Refund::class)]
    #[Response(status: 404, description: 'Refund not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function show(string $id)
    {
        $refund = QueryBuilder::for(Refund::where('id', $id)->where('move_type', MoveType::OUT_REFUND))
            ->allowedIncludes([
                'partner',
                'currency',
                'journal',
                'company',
                'invoicePaymentTerm',
                'fiscalPosition',
                'invoiceUser',
                'partnerShipping',
                'partnerBank',
                'invoiceIncoterm',
                'invoiceCashRounding',
                'paymentMethodLine',
                'campaign',
                'source',
                'medium',
                'creator',
                'invoiceLines',
                'invoiceLines.product',
                'invoiceLines.uom',
                'invoiceLines.taxes',
                'invoiceLines.account',
                'invoiceLines.currency',
                'invoiceLines.companyCurrency',
                'invoiceLines.partner',
                'invoiceLines.creator',
                'invoiceLines.journal',
                'invoiceLines.company',
                'invoiceLines.groupTax',
                'invoiceLines.taxGroup',
                'invoiceLines.payment',
                'invoiceLines.taxRepartitionLine',
            ])
            ->firstOrFail();

        Gate::authorize('view', $refund);

        return new RefundResource($refund);
    }

    #[Endpoint('Update refund', 'Update an existing refund')]
    #[UrlParam('id', 'integer', 'The refund ID', required: true, example: 1)]
    #[ResponseFromApiResource(RefundResource::class, Refund::class, additional: ['message' => 'Refund updated successfully.'])]
    #[Response(status: 404, description: 'Refund not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 422, description: 'Validation error', content: '{"message": "The given data was invalid.", "errors": {"state": ["Cannot update a posted refund."]}}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function update(RefundRequest $request, string $id)
    {
        $refund = Refund::where('move_type', MoveType::OUT_REFUND)->findOrFail($id);

        Gate::authorize('update', $refund);

        if ($refund->state === MoveState::POSTED) {
            return response()->json([
                'message' => 'Cannot update a posted refund.',
            ], 422);
        }

        $data = $request->validated();

        return DB::transaction(function () use ($refund, $data) {
            $invoiceLines = $data['invoice_lines'] ?? null;
            unset($data['invoice_lines']);

            $refund->update($data);

            if ($invoiceLines !== null) {
                $this->syncInvoiceLines($refund, $invoiceLines);
            }

            $refund = AccountFacade::computeAccountMove($refund);

            $refund->load(['invoiceLines.product', 'invoiceLines.uom', 'invoiceLines.taxes']);

            return (new RefundResource($refund))
                ->additional(['message' => 'Refund updated successfully.']);
        });
    }

    #[Endpoint('Delete refund', 'Delete a refund (only draft refunds)')]
    #[UrlParam('id', 'integer', 'The refund ID', required: true, example: 1)]
    #[Response(status: 200, description: 'Refund deleted', content: '{"message": "Refund deleted successfully."}')]
    #[Response(status: 404, description: 'Refund not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 422, description: 'Cannot delete', content: '{"message": "Cannot delete a posted refund."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function destroy(string $id)
    {
        $refund = Refund::where('move_type', MoveType::OUT_REFUND)->findOrFail($id);

        Gate::authorize('delete', $refund);

        if ($refund->state !== MoveState::DRAFT) {
            return response()->json([
                'message' => 'Cannot delete a posted or cancelled refund.',
            ], 422);
        }

        $refund->delete();

        return response()->json([
            'message' => 'Refund deleted successfully.',
        ]);
    }

    /**
     * Sync invoice lines with ID-based approach
     */
    protected function syncInvoiceLines(Refund $refund, array $linesData): void
    {
        $submittedIds = collect($linesData)
            ->pluck('id')
            ->filter()
            ->toArray();

        $refund->invoiceLines()
            ->whereNotIn('id', $submittedIds)
            ->delete();

        foreach ($linesData as $lineData) {
            $taxes = $lineData['taxes'] ?? [];
            unset($lineData['taxes']);

            if (isset($lineData['id'])) {
                $moveLine = $refund->invoiceLines()->find($lineData['id']);

                if ($moveLine) {
                    $moveLine->update($lineData);
                    $moveLine->taxes()->sync($taxes);
                }
            } else {
                $moveLine = $refund->invoiceLines()->create($lineData);

                if (! empty($taxes)) {
                    $moveLine->taxes()->sync($taxes);
                }
            }
        }
    }
}
