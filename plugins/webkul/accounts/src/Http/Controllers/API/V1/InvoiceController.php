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
use Webkul\Account\Http\Requests\InvoiceRequest;
use Webkul\Account\Http\Resources\V1\InvoiceResource;
use Webkul\Account\Models\Invoice;

#[Group('Account API Management')]
#[Subgroup('Invoices', 'Manage customer invoices')]
#[Authenticated]
class InvoiceController extends Controller
{
    #[Endpoint('List invoices', 'Retrieve a paginated list of customer invoices with filtering and sorting')]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> partner, currency, journal, company, invoicePaymentTerm, fiscalPosition, invoiceUser, partnerShipping, partnerBank, invoiceIncoterm, invoiceCashRounding, paymentMethodLine, campaign, source, medium, creator, invoiceLines, invoiceLines.product, invoiceLines.uom, invoiceLines.taxes, invoiceLines.account, invoiceLines.currency, invoiceLines.companyCurrency, invoiceLines.partner, invoiceLines.creator, invoiceLines.journal, invoiceLines.company, invoiceLines.groupTax, invoiceLines.taxGroup, invoiceLines.payment, invoiceLines.taxRepartitionLine', required: false, example: 'partner,invoiceLines.product')]
    #[QueryParam('filter[id]', 'string', 'Comma-separated list of IDs to filter by', required: false, example: 'No-example')]
    #[QueryParam('filter[name]', 'string', 'Filter by invoice number (partial match)', required: false, example: 'No-example')]
    #[QueryParam('filter[partner_id]', 'string', 'Comma-separated list of partner IDs to filter by', required: false, example: 'No-example')]
    #[QueryParam('filter[state]', 'string', 'Filter by state (draft, posted, cancel)', required: false, example: 'No-example')]
    #[QueryParam('filter[payment_state]', 'string', 'Filter by payment state', required: false, example: 'No-example')]
    #[QueryParam('sort', 'string', 'Sort field', example: 'invoice_date')]
    #[QueryParam('page', 'int', 'Page number', example: 1)]
    #[ResponseFromApiResource(InvoiceResource::class, Invoice::class, collection: true, paginate: 10)]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function index()
    {
        Gate::authorize('viewAny', Invoice::class);

        $invoices = QueryBuilder::for(Invoice::class)
            ->where('move_type', MoveType::OUT_INVOICE)
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

        return InvoiceResource::collection($invoices);
    }

    #[Endpoint('Create invoice', 'Create a new customer invoice')]
    #[ResponseFromApiResource(InvoiceResource::class, Invoice::class, status: 201, additional: ['message' => 'Invoice created successfully.'])]
    #[Response(status: 422, description: 'Validation error', content: '{"message": "The given data was invalid.", "errors": {"partner_id": ["The partner id field is required."]}}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function store(InvoiceRequest $request)
    {
        Gate::authorize('create', Invoice::class);

        $data = $request->validated();

        return DB::transaction(function () use ($data) {
            $invoiceLines = $data['invoice_lines'];
            unset($data['invoice_lines']);

            $data['move_type'] = MoveType::OUT_INVOICE;
            $data['state'] = MoveState::DRAFT;

            $invoice = Invoice::create($data);

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

            return (new InvoiceResource($invoice))
                ->additional(['message' => 'Invoice created successfully.'])
                ->response()
                ->setStatusCode(201);
        });
    }

    #[Endpoint('Show invoice', 'Retrieve a specific invoice by its ID')]
    #[UrlParam('id', 'integer', 'The invoice ID', required: true, example: 1)]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> partner, currency, journal, company, invoicePaymentTerm, fiscalPosition, invoiceUser, partnerShipping, partnerBank, invoiceIncoterm, invoiceCashRounding, paymentMethodLine, campaign, source, medium, creator, invoiceLines, invoiceLines.product, invoiceLines.uom, invoiceLines.taxes, invoiceLines.account, invoiceLines.currency, invoiceLines.companyCurrency, invoiceLines.partner, invoiceLines.creator, invoiceLines.journal, invoiceLines.company, invoiceLines.groupTax, invoiceLines.taxGroup, invoiceLines.payment, invoiceLines.taxRepartitionLine', required: false, example: 'partner,invoiceLines')]
    #[ResponseFromApiResource(InvoiceResource::class, Invoice::class)]
    #[Response(status: 404, description: 'Invoice not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function show(string $id)
    {
        $invoice = QueryBuilder::for(Invoice::where('id', $id)->where('move_type', MoveType::OUT_INVOICE))
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

        return new InvoiceResource($invoice);
    }

    #[Endpoint('Update invoice', 'Update an existing invoice')]
    #[UrlParam('id', 'integer', 'The invoice ID', required: true, example: 1)]
    #[ResponseFromApiResource(InvoiceResource::class, Invoice::class, additional: ['message' => 'Invoice updated successfully.'])]
    #[Response(status: 404, description: 'Invoice not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 422, description: 'Validation error', content: '{"message": "The given data was invalid.", "errors": {"state": ["Cannot update a posted invoice."]}}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function update(InvoiceRequest $request, string $id)
    {
        $invoice = Invoice::where('move_type', MoveType::OUT_INVOICE)->findOrFail($id);

        Gate::authorize('update', $invoice);

        if ($invoice->state === MoveState::POSTED) {
            return response()->json([
                'message' => 'Cannot update a posted invoice.',
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

            return (new InvoiceResource($invoice))
                ->additional(['message' => 'Invoice updated successfully.']);
        });
    }

    #[Endpoint('Delete invoice', 'Delete an invoice (only draft invoices)')]
    #[UrlParam('id', 'integer', 'The invoice ID', required: true, example: 1)]
    #[Response(status: 200, description: 'Invoice deleted', content: '{"message": "Invoice deleted successfully."}')]
    #[Response(status: 404, description: 'Invoice not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 422, description: 'Cannot delete', content: '{"message": "Cannot delete a posted invoice."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function destroy(string $id)
    {
        $invoice = Invoice::where('move_type', MoveType::OUT_INVOICE)->findOrFail($id);

        Gate::authorize('delete', $invoice);

        if ($invoice->state !== MoveState::DRAFT) {
            return response()->json([
                'message' => 'Cannot delete a posted or cancelled invoice.',
            ], 422);
        }

        $invoice->delete();

        return response()->json([
            'message' => 'Invoice deleted successfully.',
        ]);
    }

    /**
     * Sync invoice lines with ID-based approach
     */
    protected function syncInvoiceLines(Invoice $invoice, array $linesData): void
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
