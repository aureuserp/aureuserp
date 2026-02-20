<?php

namespace Webkul\Account\Http\Controllers\API\V1;

use Exception;
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
use Webkul\Account\Enums\PaymentState;
use Webkul\Account\Enums\PaymentType;
use Webkul\Account\Facades\Account as AccountFacade;
use Webkul\Account\Http\Requests\MovePaymentRequest;
use Webkul\Account\Http\Requests\BillRequest;
use Webkul\Account\Http\Resources\V1\MoveResource;
use Webkul\Account\Models\PaymentRegister;
use Webkul\Account\Models\Refund;
use Webkul\Accounting\Models\Journal;

#[Group('Account API Management')]
#[Subgroup('Refunds', 'Manage vendor refunds')]
#[Authenticated]
class RefundController extends Controller
{
    #[Endpoint('List refunds', 'Retrieve a paginated list of vendor refunds with filtering and sorting')]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> partner, currency, journal, company, invoicePaymentTerm, fiscalPosition, invoiceUser, partnerShipping, partnerBank, invoiceIncoterm, invoiceCashRounding, paymentMethodLine, campaign, source, medium, creator, invoiceLines, invoiceLines.product, invoiceLines.uom, invoiceLines.taxes, invoiceLines.account, invoiceLines.currency, invoiceLines.companyCurrency, invoiceLines.partner, invoiceLines.creator, invoiceLines.journal, invoiceLines.company, invoiceLines.groupTax, invoiceLines.taxGroup, invoiceLines.payment, invoiceLines.taxRepartitionLine', required: false, example: 'partner,invoiceLines.product')]
    #[QueryParam('filter[id]', 'string', 'Comma-separated list of IDs to filter by', required: false, example: 'No-example')]
    #[QueryParam('filter[name]', 'string', 'Filter by refund number (partial match)', required: false, example: 'No-example')]
    #[QueryParam('filter[partner_id]', 'string', 'Comma-separated list of partner IDs to filter by', required: false, example: 'No-example')]
    #[QueryParam('filter[state]', 'string', 'Filter by state (draft, posted, cancel)', required: false, example: 'No-example')]
    #[QueryParam('filter[payment_state]', 'string', 'Filter by payment state', required: false, example: 'No-example')]
    #[QueryParam('sort', 'string', 'Sort field', example: 'invoice_date')]
    #[QueryParam('page', 'int', 'Page number', example: 1)]
    #[ResponseFromApiResource(MoveResource::class, Refund::class, collection: true, paginate: 10)]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function index()
    {
        Gate::authorize('viewAny', Refund::class);

        $refunds = QueryBuilder::for(Refund::class)
            ->where('move_type', MoveType::IN_REFUND)
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

        return MoveResource::collection($refunds);
    }

    #[Endpoint('Create refund', 'Create a new vendor refund')]
    #[ResponseFromApiResource(MoveResource::class, Refund::class, status: 201, additional: ['message' => 'Refund created successfully.'])]
    #[Response(status: 422, description: 'Validation error', content: '{"message": "The given data was invalid.", "errors": {"partner_id": ["The partner id field is required."]}}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function store(BillRequest $request)
    {
        Gate::authorize('create', Refund::class);

        $data = $request->validated();

        return DB::transaction(function () use ($data) {
            $invoiceLines = $data['invoice_lines'];
            unset($data['invoice_lines']);

            $data["move_type"] = MoveType::IN_REFUND;
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

            return (new MoveResource($refund))
                ->additional(['message' => 'Refund created successfully.'])
                ->response()
                ->setStatusCode(201);
        });
    }

    #[Endpoint('Show refund', 'Retrieve a specific refund by its ID')]
    #[UrlParam('id', 'integer', 'The refund ID', required: true, example: 1)]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> partner, currency, journal, company, invoicePaymentTerm, fiscalPosition, invoiceUser, partnerShipping, partnerBank, invoiceIncoterm, invoiceCashRounding, paymentMethodLine, campaign, source, medium, creator, invoiceLines, invoiceLines.product, invoiceLines.uom, invoiceLines.taxes, invoiceLines.account, invoiceLines.currency, invoiceLines.companyCurrency, invoiceLines.partner, invoiceLines.creator, invoiceLines.journal, invoiceLines.company, invoiceLines.groupTax, invoiceLines.taxGroup, invoiceLines.payment, invoiceLines.taxRepartitionLine', required: false, example: 'partner,invoiceLines')]
    #[ResponseFromApiResource(MoveResource::class, Refund::class)]
    #[Response(status: 404, description: 'Refund not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function show(string $id)
    {
        $refund = QueryBuilder::for(Refund::where('id', $id)->where('move_type', MoveType::IN_REFUND))
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

        return new MoveResource($refund);
    }

    #[Endpoint('Update refund', 'Update an existing refund')]
    #[UrlParam('id', 'integer', 'The refund ID', required: true, example: 1)]
    #[ResponseFromApiResource(MoveResource::class, Refund::class, additional: ['message' => 'Refund updated successfully.'])]
    #[Response(status: 404, description: 'Refund not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 422, description: 'Validation error', content: '{"message": "The given data was invalid.", "errors": {"state": ["Cannot update a posted refund."]}}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function update(BillRequest $request, string $id)
    {
        $refund = Refund::where('move_type', MoveType::IN_REFUND)->findOrFail($id);

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

            return (new MoveResource($refund))
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
        $refund = Refund::where('move_type', MoveType::IN_REFUND)->findOrFail($id);

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

    #[Endpoint('Confirm refund', 'Confirm a draft refund and move it to posted state')]
    #[UrlParam('id', 'integer', 'The refund ID', required: true, example: 1)]
    #[ResponseFromApiResource(MoveResource::class, Refund::class, additional: ['message' => 'Refund confirmed successfully.'])]
    #[Response(status: 422, description: 'Invalid state transition', content: '{"message": "Only draft refunds can be confirmed."}')]
    #[Response(status: 404, description: 'Refund not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function confirm(string $id)
    {
        $refund = Refund::where('move_type', MoveType::IN_REFUND)->findOrFail($id);

        Gate::authorize('update', $refund);

        if ($refund->state !== MoveState::DRAFT) {
            return response()->json([
                'message' => 'Only draft refunds can be confirmed.',
            ], 422);
        }

        $refund->checked = (bool) $refund->journal?->auto_check_on_post;

        try {
            $refund = AccountFacade::confirmMove($refund);
        } catch (Exception $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 422);
        }

        return (new MoveResource($refund->refresh()))
            ->additional(['message' => 'Refund confirmed successfully.']);
    }

    #[Endpoint('Cancel refund', 'Cancel a draft refund')]
    #[UrlParam('id', 'integer', 'The refund ID', required: true, example: 1)]
    #[ResponseFromApiResource(MoveResource::class, Refund::class, additional: ['message' => 'Refund cancelled successfully.'])]
    #[Response(status: 422, description: 'Invalid state transition', content: '{"message": "Only draft refunds can be cancelled."}')]
    #[Response(status: 404, description: 'Refund not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function cancel(string $id)
    {
        $refund = Refund::where('move_type', MoveType::IN_REFUND)->findOrFail($id);

        Gate::authorize('update', $refund);

        if ($refund->state !== MoveState::DRAFT) {
            return response()->json([
                'message' => 'Only draft refunds can be cancelled.',
            ], 422);
        }

        $refund = AccountFacade::cancelMove($refund);

        return (new MoveResource($refund->refresh()))
            ->additional(['message' => 'Refund cancelled successfully.']);
    }

    #[Endpoint('Pay refund', 'Register payment for a posted refund')]
    #[UrlParam('id', 'integer', 'The refund ID', required: true, example: 1)]
    #[ResponseFromApiResource(MoveResource::class, Refund::class, additional: ['message' => 'Refund payment registered successfully.'])]
    #[Response(status: 422, description: 'Invalid payment state', content: '{"message": "Only posted refunds with pending payment can be paid."}')]
    #[Response(status: 404, description: 'Refund not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function pay(MovePaymentRequest $request, string $id)
    {
        $refund = Refund::where('move_type', MoveType::IN_REFUND)->findOrFail($id);

        Gate::authorize('update', $refund);

        if (
            $refund->state !== MoveState::POSTED
            || ! in_array($refund->payment_state, [PaymentState::NOT_PAID, PaymentState::PARTIAL, PaymentState::IN_PAYMENT], true)
        ) {
            return response()->json([
                'message' => 'Only posted refunds with pending payment can be paid.',
            ], 422);
        }

        $lineIds = $refund->paymentTermLines
            ->filter(fn ($line) => ! $line->reconciled)
            ->pluck('id')
            ->toArray();

        if (empty($lineIds)) {
            return response()->json([
                'message' => 'No outstanding refund payment lines found.',
            ], 422);
        }

        try {
            $paymentData = $this->preparePaymentData($refund, $request->validated());
        } catch (Exception $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 422);
        }

        if (empty($paymentData['journal_id']) || empty($paymentData['payment_method_line_id'])) {
            return response()->json([
                'message' => 'Unable to determine payment journal or payment method line.',
            ], 422);
        }

        try {
            DB::transaction(function () use ($paymentData, $lineIds) {
                $paymentRegister = PaymentRegister::create($paymentData);

                $paymentRegister->lines()->sync($lineIds);

                $paymentRegister->refresh();

                $paymentRegister->computeFromLines();

                $paymentRegister->save();

                AccountFacade::createPayments($paymentRegister);
            });
        } catch (Exception $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 422);
        }

        return (new MoveResource($refund->refresh()))
            ->additional(['message' => 'Refund payment registered successfully.']);
    }

    #[Endpoint('Reset refund to draft', 'Reset a posted or cancelled refund to draft')]
    #[UrlParam('id', 'integer', 'The refund ID', required: true, example: 1)]
    #[ResponseFromApiResource(MoveResource::class, Refund::class, additional: ['message' => 'Refund reset to draft successfully.'])]
    #[Response(status: 422, description: 'Invalid state transition', content: '{"message": "Only posted or cancelled refunds can be reset to draft."}')]
    #[Response(status: 404, description: 'Refund not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function resetToDraft(string $id)
    {
        $refund = Refund::where('move_type', MoveType::IN_REFUND)->findOrFail($id);

        Gate::authorize('update', $refund);

        if (! in_array($refund->state, [MoveState::POSTED, MoveState::CANCEL], true)) {
            return response()->json([
                'message' => 'Only posted or cancelled refunds can be reset to draft.',
            ], 422);
        }

        try {
            $refund = AccountFacade::resetToDraftMove($refund);
        } catch (Exception $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 422);
        }

        return (new MoveResource($refund->refresh()))
            ->additional(['message' => 'Refund reset to draft successfully.']);
    }

    #[Endpoint('Set refund as checked', 'Mark a refund as checked')]
    #[UrlParam('id', 'integer', 'The refund ID', required: true, example: 1)]
    #[ResponseFromApiResource(MoveResource::class, Refund::class, additional: ['message' => 'Refund marked as checked successfully.'])]
    #[Response(status: 422, description: 'Invalid state transition', content: '{"message": "Only non-draft and unchecked refunds can be marked as checked."}')]
    #[Response(status: 404, description: 'Refund not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function setAsChecked(string $id)
    {
        $refund = Refund::where('move_type', MoveType::IN_REFUND)->findOrFail($id);

        Gate::authorize('update', $refund);

        if ($refund->state === MoveState::DRAFT || $refund->checked) {
            return response()->json([
                'message' => 'Only non-draft and unchecked refunds can be marked as checked.',
            ], 422);
        }

        $refund = AccountFacade::setAsCheckedMove($refund);

        return (new MoveResource($refund->refresh()))
            ->additional(['message' => 'Refund marked as checked successfully.']);
    }

    protected function preparePaymentData(Refund $refund, array $data): array
    {
        $paymentRegister = new PaymentRegister;
        $paymentRegister->lines = $refund->lines;
        $paymentRegister->company = $refund->company;
        $paymentRegister->currency = $refund->currency;
        $paymentRegister->currency_id = $data['currency_id'] ?? $refund->currency_id;
        $paymentRegister->payment_date = $data['payment_date'] ?? now()->toDateString();
        $paymentRegister->payment_type = $refund->isInbound(true) ? PaymentType::RECEIVE : PaymentType::SEND;

        $paymentRegister->computeBatches();
        $paymentRegister->computeAvailableJournalIds();

        $journalId = $data['journal_id'] ?? ($paymentRegister->available_journal_ids[0] ?? null);
        $journal = $journalId ? Journal::find($journalId) : null;

        if ($journal && empty($data['currency_id']) && $journal->currency_id) {
            $paymentRegister->currency_id = $journal->currency_id;
        }

        $paymentRegister->journal_id = $journalId;
        $paymentRegister->journal = $journal;
        $paymentRegister->computePaymentMethodLineId();

        $amountsToPay = $paymentRegister->getTotalAmountsToPay($paymentRegister->batches);
        $paymentRegister->amount = $data['amount'] ?? $amountsToPay['amount_by_default'];
        $paymentRegister->computeInstallmentsMode();

        return [
            'currency_id'             => $paymentRegister->currency_id,
            'journal_id'              => $journalId,
            'partner_bank_id'         => $data['partner_bank_id'] ?? null,
            'payment_method_line_id'  => $data['payment_method_line_id'] ?? $paymentRegister->payment_method_line_id,
            'communication'           => $data['communication'] ?? $refund->name,
            'installments_mode'       => $data['installments_mode'] ?? $paymentRegister->installments_mode,
            'payment_date'            => $paymentRegister->payment_date,
            'amount'                  => $paymentRegister->amount,
        ];
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
