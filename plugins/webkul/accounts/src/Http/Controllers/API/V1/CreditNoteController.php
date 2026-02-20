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
use Webkul\Account\Http\Requests\InvoiceRequest;
use Webkul\Account\Http\Resources\V1\MoveResource;
use Webkul\Account\Models\PaymentRegister;
use Webkul\Account\Models\Refund;
use Webkul\Accounting\Models\Journal;

#[Group('Account API Management')]
#[Subgroup('Credit Notes', 'Manage customer credit notes')]
#[Authenticated]
class CreditNoteController extends Controller
{
    #[Endpoint('List credit notes', 'Retrieve a paginated list of customer credit notes with filtering and sorting')]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> partner, currency, journal, company, invoicePaymentTerm, fiscalPosition, invoiceUser, partnerShipping, partnerBank, invoiceIncoterm, invoiceCashRounding, paymentMethodLine, campaign, source, medium, creator, invoiceLines, invoiceLines.product, invoiceLines.uom, invoiceLines.taxes, invoiceLines.account, invoiceLines.currency, invoiceLines.companyCurrency, invoiceLines.partner, invoiceLines.creator, invoiceLines.journal, invoiceLines.company, invoiceLines.groupTax, invoiceLines.taxGroup, invoiceLines.payment, invoiceLines.taxRepartitionLine', required: false, example: 'partner,invoiceLines.product')]
    #[QueryParam('filter[id]', 'string', 'Comma-separated list of IDs to filter by', required: false, example: 'No-example')]
    #[QueryParam('filter[name]', 'string', 'Filter by credit note number (partial match)', required: false, example: 'No-example')]
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

        return MoveResource::collection($refunds);
    }

    #[Endpoint('Create credit note', 'Create a new customer credit note')]
    #[ResponseFromApiResource(MoveResource::class, Refund::class, status: 201, additional: ['message' => 'Credit note created successfully.'])]
    #[Response(status: 422, description: 'Validation error', content: '{"message": "The given data was invalid.", "errors": {"partner_id": ["The partner id field is required."]}}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function store(InvoiceRequest $request)
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

            return (new MoveResource($refund))
                ->additional(['message' => 'Credit note created successfully.'])
                ->response()
                ->setStatusCode(201);
        });
    }

    #[Endpoint('Show credit note', 'Retrieve a specific credit note by its ID')]
    #[UrlParam('id', 'integer', 'The credit note ID', required: true, example: 1)]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> partner, currency, journal, company, invoicePaymentTerm, fiscalPosition, invoiceUser, partnerShipping, partnerBank, invoiceIncoterm, invoiceCashRounding, paymentMethodLine, campaign, source, medium, creator, invoiceLines, invoiceLines.product, invoiceLines.uom, invoiceLines.taxes, invoiceLines.account, invoiceLines.currency, invoiceLines.companyCurrency, invoiceLines.partner, invoiceLines.creator, invoiceLines.journal, invoiceLines.company, invoiceLines.groupTax, invoiceLines.taxGroup, invoiceLines.payment, invoiceLines.taxRepartitionLine', required: false, example: 'partner,invoiceLines')]
    #[ResponseFromApiResource(MoveResource::class, Refund::class)]
    #[Response(status: 404, description: 'Credit note not found', content: '{"message": "Resource not found."}')]
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

        return new MoveResource($refund);
    }

    #[Endpoint('Update credit note', 'Update an existing credit note')]
    #[UrlParam('id', 'integer', 'The credit note ID', required: true, example: 1)]
    #[ResponseFromApiResource(MoveResource::class, Refund::class, additional: ['message' => 'Credit note updated successfully.'])]
    #[Response(status: 404, description: 'Credit note not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 422, description: 'Validation error', content: '{"message": "The given data was invalid.", "errors": {"state": ["Cannot update a posted credit note."]}}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function update(InvoiceRequest $request, string $id)
    {
        $refund = Refund::where('move_type', MoveType::OUT_REFUND)->findOrFail($id);

        Gate::authorize('update', $refund);

        if ($refund->state === MoveState::POSTED) {
            return response()->json([
                'message' => 'Cannot update a posted credit note.',
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
                ->additional(['message' => 'Credit note updated successfully.']);
        });
    }

    #[Endpoint('Delete credit note', 'Delete a credit note (only draft credit notes)')]
    #[UrlParam('id', 'integer', 'The credit note ID', required: true, example: 1)]
    #[Response(status: 200, description: 'Credit note deleted', content: '{"message": "Credit note deleted successfully."}')]
    #[Response(status: 404, description: 'Credit note not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 422, description: 'Cannot delete', content: '{"message": "Cannot delete a posted credit note."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function destroy(string $id)
    {
        $refund = Refund::where('move_type', MoveType::OUT_REFUND)->findOrFail($id);

        Gate::authorize('delete', $refund);

        if ($refund->state !== MoveState::DRAFT) {
            return response()->json([
                'message' => 'Cannot delete a posted or cancelled credit note.',
            ], 422);
        }

        $refund->delete();

        return response()->json([
            'message' => 'Credit note deleted successfully.',
        ]);
    }

    #[Endpoint('Confirm credit note', 'Confirm a draft credit note and move it to posted state')]
    #[UrlParam('id', 'integer', 'The credit note ID', required: true, example: 1)]
    #[ResponseFromApiResource(MoveResource::class, Refund::class, additional: ['message' => 'Credit note confirmed successfully.'])]
    #[Response(status: 422, description: 'Invalid state transition', content: '{"message": "Only draft credit notes can be confirmed."}')]
    #[Response(status: 404, description: 'Credit note not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function confirm(string $id)
    {
        $refund = Refund::where('move_type', MoveType::OUT_REFUND)->findOrFail($id);

        Gate::authorize('update', $refund);

        if ($refund->state !== MoveState::DRAFT) {
            return response()->json([
                'message' => 'Only draft credit notes can be confirmed.',
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
            ->additional(['message' => 'Credit note confirmed successfully.']);
    }

    #[Endpoint('Cancel credit note', 'Cancel a draft credit note')]
    #[UrlParam('id', 'integer', 'The credit note ID', required: true, example: 1)]
    #[ResponseFromApiResource(MoveResource::class, Refund::class, additional: ['message' => 'Credit note cancelled successfully.'])]
    #[Response(status: 422, description: 'Invalid state transition', content: '{"message": "Only draft credit notes can be cancelled."}')]
    #[Response(status: 404, description: 'Credit note not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function cancel(string $id)
    {
        $refund = Refund::where('move_type', MoveType::OUT_REFUND)->findOrFail($id);

        Gate::authorize('update', $refund);

        if ($refund->state !== MoveState::DRAFT) {
            return response()->json([
                'message' => 'Only draft credit notes can be cancelled.',
            ], 422);
        }

        $refund = AccountFacade::cancelMove($refund);

        return (new MoveResource($refund->refresh()))
            ->additional(['message' => 'Credit note cancelled successfully.']);
    }

    #[Endpoint('Pay credit note', 'Register payment for a posted credit note')]
    #[UrlParam('id', 'integer', 'The credit note ID', required: true, example: 1)]
    #[ResponseFromApiResource(MoveResource::class, Refund::class, additional: ['message' => 'Credit note payment registered successfully.'])]
    #[Response(status: 422, description: 'Invalid payment state', content: '{"message": "Only posted credit notes with pending payment can be paid."}')]
    #[Response(status: 404, description: 'Credit note not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function pay(MovePaymentRequest $request, string $id)
    {
        $refund = Refund::where('move_type', MoveType::OUT_REFUND)->findOrFail($id);

        Gate::authorize('update', $refund);

        if (
            $refund->state !== MoveState::POSTED
            || ! in_array($refund->payment_state, [PaymentState::NOT_PAID, PaymentState::PARTIAL, PaymentState::IN_PAYMENT], true)
        ) {
            return response()->json([
                'message' => 'Only posted credit notes with pending payment can be paid.',
            ], 422);
        }

        $lineIds = $refund->paymentTermLines
            ->filter(fn ($line) => ! $line->reconciled)
            ->pluck('id')
            ->toArray();

        if (empty($lineIds)) {
            return response()->json([
                'message' => 'No outstanding credit note payment lines found.',
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
            ->additional(['message' => 'Credit note payment registered successfully.']);
    }

    #[Endpoint('Reset credit note to draft', 'Reset a posted or cancelled credit note to draft')]
    #[UrlParam('id', 'integer', 'The credit note ID', required: true, example: 1)]
    #[ResponseFromApiResource(MoveResource::class, Refund::class, additional: ['message' => 'Credit note reset to draft successfully.'])]
    #[Response(status: 422, description: 'Invalid state transition', content: '{"message": "Only posted or cancelled credit notes can be reset to draft."}')]
    #[Response(status: 404, description: 'Credit note not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function resetToDraft(string $id)
    {
        $refund = Refund::where('move_type', MoveType::OUT_REFUND)->findOrFail($id);

        Gate::authorize('update', $refund);

        if (! in_array($refund->state, [MoveState::POSTED, MoveState::CANCEL], true)) {
            return response()->json([
                'message' => 'Only posted or cancelled credit notes can be reset to draft.',
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
            ->additional(['message' => 'Credit note reset to draft successfully.']);
    }

    #[Endpoint('Set credit note as checked', 'Mark a credit note as checked')]
    #[UrlParam('id', 'integer', 'The credit note ID', required: true, example: 1)]
    #[ResponseFromApiResource(MoveResource::class, Refund::class, additional: ['message' => 'Credit note marked as checked successfully.'])]
    #[Response(status: 422, description: 'Invalid state transition', content: '{"message": "Only non-draft and unchecked credit notes can be marked as checked."}')]
    #[Response(status: 404, description: 'Credit note not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function setAsChecked(string $id)
    {
        $refund = Refund::where('move_type', MoveType::OUT_REFUND)->findOrFail($id);

        Gate::authorize('update', $refund);

        if ($refund->state === MoveState::DRAFT || $refund->checked) {
            return response()->json([
                'message' => 'Only non-draft and unchecked credit notes can be marked as checked.',
            ], 422);
        }

        $refund = AccountFacade::setAsCheckedMove($refund);

        return (new MoveResource($refund->refresh()))
            ->additional(['message' => 'Credit note marked as checked successfully.']);
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
