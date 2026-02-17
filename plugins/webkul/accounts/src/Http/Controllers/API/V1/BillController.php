<?php

namespace Webkul\Account\Http\Controllers\API\V1;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
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
use Webkul\Account\Http\Requests\BillRequest;
use Webkul\Account\Http\Requests\MovePaymentRequest;
use Webkul\Account\Http\Resources\V1\MoveResource;
use Webkul\Account\Models\Bill;
use Webkul\Account\Models\MoveReversal;
use Webkul\Account\Models\PaymentRegister;
use Webkul\Accounting\Models\Journal;

#[Group('Account API Management')]
#[Subgroup('Bills', 'Manage vendor bills')]
#[Authenticated]
class BillController extends Controller
{
    #[Endpoint('List bills', 'Retrieve a paginated list of vendor bills with filtering and sorting')]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> partner, currency, journal, company, invoicePaymentTerm, fiscalPosition, invoiceUser, partnerShipping, partnerBank, invoiceIncoterm, invoiceCashRounding, paymentMethodLine, campaign, source, medium, creator, invoiceLines, invoiceLines.product, invoiceLines.uom, invoiceLines.taxes, invoiceLines.account, invoiceLines.currency, invoiceLines.companyCurrency, invoiceLines.partner, invoiceLines.creator, invoiceLines.journal, invoiceLines.company, invoiceLines.groupTax, invoiceLines.taxGroup, invoiceLines.payment, invoiceLines.taxRepartitionLine', required: false, example: 'partner,invoiceLines.product')]
    #[QueryParam('filter[id]', 'string', 'Comma-separated list of IDs to filter by', required: false, example: 'No-example')]
    #[QueryParam('filter[name]', 'string', 'Filter by bill number (partial match)', required: false, example: 'No-example')]
    #[QueryParam('filter[partner_id]', 'string', 'Comma-separated list of partner IDs to filter by', required: false, example: 'No-example')]
    #[QueryParam('filter[state]', 'string', 'Filter by state (draft, posted, cancel)', required: false, example: 'No-example')]
    #[QueryParam('filter[payment_state]', 'string', 'Filter by payment state', required: false, example: 'No-example')]
    #[QueryParam('sort', 'string', 'Sort field', example: 'invoice_date')]
    #[QueryParam('page', 'int', 'Page number', example: 1)]
    #[ResponseFromApiResource(MoveResource::class, Bill::class, collection: true, paginate: 10)]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function index()
    {
        Gate::authorize('viewAny', Bill::class);

        $invoices = QueryBuilder::for(Bill::class)
            ->where('move_type', MoveType::IN_INVOICE)
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

        return MoveResource::collection($invoices);
    }

    #[Endpoint('Create bill', 'Create a new vendor bill')]
    #[ResponseFromApiResource(MoveResource::class, Bill::class, status: 201, additional: ['message' => 'Bill created successfully.'])]
    #[Response(status: 422, description: 'Validation error', content: '{"message": "The given data was invalid.", "errors": {"partner_id": ["The partner id field is required."]}}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function store(BillRequest $request)
    {
        Gate::authorize('create', Bill::class);

        $data = $request->validated();

        return DB::transaction(function () use ($data) {
            $invoiceLines = $data['invoice_lines'];
            unset($data['invoice_lines']);

            $data["move_type"] = MoveType::IN_INVOICE;
            $data['state'] = MoveState::DRAFT;

            $invoice = Bill::create($data);

            foreach ($invoiceLines as $lineData) {
                $taxes = $lineData['taxes'] ?? [];
                unset($lineData['taxes']);

                $moveLine = $invoice->invoiceLines()->create($lineData);

                if (! empty($taxes)) {
                    $moveLine->taxes()->sync($taxes);
                }
            }

            $invoice = AccountFacade::computeAccountMove($invoice);

            $invoice->load(['invoiceLines.product', 'invoiceLines.uom', 'invoiceLines.taxes']);

            return (new MoveResource($invoice))
                ->additional(['message' => 'Bill created successfully.'])
                ->response()
                ->setStatusCode(201);
        });
    }

    #[Endpoint('Show bill', 'Retrieve a specific bill by its ID')]
    #[UrlParam('id', 'integer', 'The bill ID', required: true, example: 1)]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> partner, currency, journal, company, invoicePaymentTerm, fiscalPosition, invoiceUser, partnerShipping, partnerBank, invoiceIncoterm, invoiceCashRounding, paymentMethodLine, campaign, source, medium, creator, invoiceLines, invoiceLines.product, invoiceLines.uom, invoiceLines.taxes, invoiceLines.account, invoiceLines.currency, invoiceLines.companyCurrency, invoiceLines.partner, invoiceLines.creator, invoiceLines.journal, invoiceLines.company, invoiceLines.groupTax, invoiceLines.taxGroup, invoiceLines.payment, invoiceLines.taxRepartitionLine', required: false, example: 'partner,invoiceLines')]
    #[ResponseFromApiResource(MoveResource::class, Bill::class)]
    #[Response(status: 404, description: 'Bill not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function show(string $id)
    {
        $invoice = QueryBuilder::for(Bill::where('id', $id)->where('move_type', MoveType::IN_INVOICE))
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

        Gate::authorize('view', $invoice);

        return new MoveResource($invoice);
    }

    #[Endpoint('Update bill', 'Update an existing bill')]
    #[UrlParam('id', 'integer', 'The bill ID', required: true, example: 1)]
    #[ResponseFromApiResource(MoveResource::class, Bill::class, additional: ['message' => 'Bill updated successfully.'])]
    #[Response(status: 404, description: 'Bill not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 422, description: 'Validation error', content: '{"message": "The given data was invalid.", "errors": {"state": ["Cannot update a posted bill."]}}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function update(BillRequest $request, string $id)
    {
        $invoice = Bill::where('move_type', MoveType::IN_INVOICE)->findOrFail($id);

        Gate::authorize('update', $invoice);

        if ($invoice->state === MoveState::POSTED) {
            return response()->json([
                'message' => 'Cannot update a posted bill.',
            ], 422);
        }

        $data = $request->validated();

        return DB::transaction(function () use ($invoice, $data) {
            $invoiceLines = $data['invoice_lines'] ?? null;
            unset($data['invoice_lines']);

            $invoice->update($data);

            if ($invoiceLines !== null) {
                $this->syncInvoiceLines($invoice, $invoiceLines);
            }

            $invoice = AccountFacade::computeAccountMove($invoice);

            $invoice->load(['invoiceLines.product', 'invoiceLines.uom', 'invoiceLines.taxes']);

            return (new MoveResource($invoice))
                ->additional(['message' => 'Bill updated successfully.']);
        });
    }

    #[Endpoint('Delete bill', 'Delete a bill (only draft bills)')]
    #[UrlParam('id', 'integer', 'The bill ID', required: true, example: 1)]
    #[Response(status: 200, description: 'Bill deleted', content: '{"message": "Bill deleted successfully."}')]
    #[Response(status: 404, description: 'Bill not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 422, description: 'Cannot delete', content: '{"message": "Cannot delete a posted bill."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function destroy(string $id)
    {
        $invoice = Bill::where('move_type', MoveType::IN_INVOICE)->findOrFail($id);

        Gate::authorize('delete', $invoice);

        if ($invoice->state !== MoveState::DRAFT) {
            return response()->json([
                'message' => 'Cannot delete a posted or cancelled bill.',
            ], 422);
        }

        $invoice->delete();

        return response()->json([
            'message' => 'Bill deleted successfully.',
        ]);
    }

    #[Endpoint('Confirm bill', 'Confirm a draft bill and move it to posted state')]
    #[UrlParam('id', 'integer', 'The bill ID', required: true, example: 1)]
    #[ResponseFromApiResource(MoveResource::class, Bill::class, additional: ['message' => 'Bill confirmed successfully.'])]
    #[Response(status: 422, description: 'Invalid state transition', content: '{"message": "Only draft bills can be confirmed."}')]
    #[Response(status: 404, description: 'Bill not found', content: '{"message": "Resource not found."}')]
    public function confirm(string $id)
    {
        $invoice = Bill::where('move_type', MoveType::IN_INVOICE)->findOrFail($id);

        Gate::authorize('update', $invoice);

        if ($invoice->state !== MoveState::DRAFT) {
            return response()->json([
                'message' => 'Only draft bills can be confirmed.',
            ], 422);
        }

        $invoice->checked = (bool) $invoice->journal?->auto_check_on_post;

        try {
            $invoice = AccountFacade::confirmMove($invoice);
        } catch (Exception $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 422);
        }

        return (new MoveResource($invoice->refresh()))
            ->additional(['message' => 'Bill confirmed successfully.']);
    }

    #[Endpoint('Cancel bill', 'Cancel a draft bill')]
    #[UrlParam('id', 'integer', 'The bill ID', required: true, example: 1)]
    #[ResponseFromApiResource(MoveResource::class, Bill::class, additional: ['message' => 'Bill cancelled successfully.'])]
    #[Response(status: 422, description: 'Invalid state transition', content: '{"message": "Only draft bills can be cancelled."}')]
    #[Response(status: 404, description: 'Bill not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function cancel(string $id)
    {
        $invoice = Bill::where('move_type', MoveType::IN_INVOICE)->findOrFail($id);

        Gate::authorize('update', $invoice);

        if ($invoice->state !== MoveState::DRAFT) {
            return response()->json([
                'message' => 'Only draft bills can be cancelled.',
            ], 422);
        }

        $invoice = AccountFacade::cancelMove($invoice);

        return (new MoveResource($invoice->refresh()))
            ->additional(['message' => 'Bill cancelled successfully.']);
    }

    #[Endpoint('Pay bill', 'Register payment for a posted bill')]
    #[UrlParam('id', 'integer', 'The bill ID', required: true, example: 1)]
    #[ResponseFromApiResource(MoveResource::class, Bill::class, additional: ['message' => 'Bill payment registered successfully.'])]
    #[Response(status: 422, description: 'Invalid payment state', content: '{"message": "Only posted bills with pending payment can be paid."}')]
    #[Response(status: 404, description: 'Bill not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function pay(MovePaymentRequest $request, string $id)
    {
        $invoice = Bill::where('move_type', MoveType::IN_INVOICE)->findOrFail($id);

        Gate::authorize('update', $invoice);

        if (
            $invoice->state !== MoveState::POSTED
            || ! in_array($invoice->payment_state, [PaymentState::NOT_PAID, PaymentState::PARTIAL, PaymentState::IN_PAYMENT], true)
        ) {
            return response()->json([
                'message' => 'Only posted bills with pending payment can be paid.',
            ], 422);
        }

        $lineIds = $invoice->paymentTermLines
            ->filter(fn ($line) => ! $line->reconciled)
            ->pluck('id')
            ->toArray();

        if (empty($lineIds)) {
            return response()->json([
                'message' => 'No outstanding bill payment lines found.',
            ], 422);
        }

        try {
            $paymentData = $this->preparePaymentData($invoice, $request->validated());
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

        return (new MoveResource($invoice->refresh()))
            ->additional(['message' => 'Bill payment registered successfully.']);
    }

    #[Endpoint('Reverse bill', 'Create a vendor refund by reversing a posted bill')]
    #[UrlParam('id', 'integer', 'The bill ID', required: true, example: 1)]
    #[ResponseFromApiResource(MoveResource::class, Bill::class, additional: ['message' => 'Bill reversed successfully.'])]
    #[Response(status: 422, description: 'Invalid state transition', content: '{"message": "Only posted bills can be reversed."}')]
    #[Response(status: 404, description: 'Bill not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function reverse(Request $request, string $id)
    {
        $invoice = Bill::where('move_type', MoveType::IN_INVOICE)->findOrFail($id);

        Gate::authorize('update', $invoice);

        if ($invoice->state !== MoveState::POSTED) {
            return response()->json([
                'message' => 'Only posted bills can be reversed.',
            ], 422);
        }

        $data = $request->validate([
            'reason'     => ['required', 'string', 'max:245'],
            'journal_id' => ['required', 'integer', 'exists:accounts_journals,id'],
            'date'       => ['required', 'date'],
        ]);

        $reversed = DB::transaction(function () use ($invoice, $data) {
            $moveReversal = MoveReversal::create([
                'journal_id' => $data['journal_id'],
                'date'       => $data['date'],
                'reason'     => $data['reason'],
            ]);

            $moveReversal->moves()->attach($invoice);

            $defaultValues = [
                'reference'         => Str::limit("Reversal of: {$invoice->name}, {$moveReversal->reason}", 250),
                'date'              => $moveReversal->date,
                'invoice_date_due'  => $moveReversal->date,
                'invoice_date'      => $invoice->isInvoice(true) ? $moveReversal->date : null,
                'journal_id'        => $moveReversal->journal_id,
                'invoice_user_id'   => $invoice->invoice_user_id,
                'auto_post'         => 0,
            ];

            $isCancelNeeded = ! $defaultValues['auto_post'] && $invoice->move_type == MoveType::ENTRY;

            $reversedMoves = AccountFacade::reverseMoves(
                collect([$invoice]),
                $defaultValues,
                $isCancelNeeded
            )->each(function ($move) use ($moveReversal) {
                $moveReversal->newMoves()->attach($move->id);
            });

            AccountFacade::computeAccountMove($invoice);

            return $reversedMoves->first();
        });

        return (new MoveResource($reversed->refresh()))
            ->additional(['message' => 'Bill reversed successfully.']);
    }

    #[Endpoint('Reset bill to draft', 'Reset a posted or cancelled bill to draft')]
    #[UrlParam('id', 'integer', 'The bill ID', required: true, example: 1)]
    #[ResponseFromApiResource(MoveResource::class, Bill::class, additional: ['message' => 'Bill reset to draft successfully.'])]
    #[Response(status: 422, description: 'Invalid state transition', content: '{"message": "Only posted or cancelled bills can be reset to draft."}')]
    #[Response(status: 404, description: 'Bill not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function resetToDraft(string $id)
    {
        $invoice = Bill::where('move_type', MoveType::IN_INVOICE)->findOrFail($id);

        Gate::authorize('update', $invoice);

        if (! in_array($invoice->state, [MoveState::POSTED, MoveState::CANCEL], true)) {
            return response()->json([
                'message' => 'Only posted or cancelled bills can be reset to draft.',
            ], 422);
        }

        try {
            $invoice = AccountFacade::resetToDraftMove($invoice);
        } catch (Exception $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 422);
        }

        return (new MoveResource($invoice->refresh()))
            ->additional(['message' => 'Bill reset to draft successfully.']);
    }

    #[Endpoint('Set bill as checked', 'Mark a bill as checked')]
    #[UrlParam('id', 'integer', 'The bill ID', required: true, example: 1)]
    #[ResponseFromApiResource(MoveResource::class, Bill::class, additional: ['message' => 'Bill marked as checked successfully.'])]
    #[Response(status: 422, description: 'Invalid state transition', content: '{"message": "Only non-draft and unchecked bills can be marked as checked."}')]
    #[Response(status: 404, description: 'Bill not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function setAsChecked(string $id)
    {
        $invoice = Bill::where('move_type', MoveType::IN_INVOICE)->findOrFail($id);

        Gate::authorize('update', $invoice);

        if ($invoice->state === MoveState::DRAFT || $invoice->checked) {
            return response()->json([
                'message' => 'Only non-draft and unchecked bills can be marked as checked.',
            ], 422);
        }

        $invoice = AccountFacade::setAsCheckedMove($invoice);

        return (new MoveResource($invoice->refresh()))
            ->additional(['message' => 'Bill marked as checked successfully.']);
    }

    protected function preparePaymentData(Bill $invoice, array $data): array
    {
        $paymentRegister = new PaymentRegister;
        $paymentRegister->lines = $invoice->lines;
        $paymentRegister->company = $invoice->company;
        $paymentRegister->currency = $invoice->currency;
        $paymentRegister->currency_id = $data['currency_id'] ?? $invoice->currency_id;
        $paymentRegister->payment_date = $data['payment_date'] ?? now()->toDateString();
        $paymentRegister->payment_type = $invoice->isInbound(true) ? PaymentType::RECEIVE : PaymentType::SEND;

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
            'communication'           => $data['communication'] ?? $invoice->name,
            'installments_mode'       => $data['installments_mode'] ?? $paymentRegister->installments_mode,
            'payment_date'            => $paymentRegister->payment_date,
            'amount'                  => $paymentRegister->amount,
        ];
    }

    /**
     * Sync invoice lines with ID-based approach
     */
    protected function syncInvoiceLines(Bill $invoice, array $linesData): void
    {
        $submittedIds = collect($linesData)
            ->pluck('id')
            ->filter()
            ->toArray();

        $invoice->invoiceLines()
            ->whereNotIn('id', $submittedIds)
            ->delete();

        foreach ($linesData as $lineData) {
            $taxes = $lineData['taxes'] ?? [];
            unset($lineData['taxes']);

            if (isset($lineData['id'])) {
                $moveLine = $invoice->invoiceLines()->find($lineData['id']);

                if ($moveLine) {
                    $moveLine->update($lineData);
                    $moveLine->taxes()->sync($taxes);
                }
            } else {
                $moveLine = $invoice->invoiceLines()->create($lineData);
                if (! empty($taxes)) {
                    $moveLine->taxes()->sync($taxes);
                }
            }
        }
    }
}
