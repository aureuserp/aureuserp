<?php

namespace Webkul\Sale\Http\Controllers\API\V1;

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
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Http\Resources\V1\MoveResource;
use Webkul\Account\Models\Move;
use Webkul\Sale\Models\Order;

#[Group('Sales API Management')]
#[Subgroup('Order Invoices', 'Manage sales order invoices')]
#[Authenticated]
class OrderInvoiceController extends Controller
{
    protected array $allowedIncludes = [
        'campaign',
        'journal',
        'company',
        'originPayment',
        'taxCashBasisOriginMove',
        'autoPostOrigin',
        'invoicePaymentTerm',
        'partner',
        'commercialPartner',
        'partnerShipping',
        'partnerBank',
        'fiscalPosition',
        'currency',
        'reversedEntry',
        'invoiceUser',
        'invoiceIncoterm',
        'invoiceCashRounding',
        'createdBy',
        'source',
        'medium',
        'paymentMethodLine',
    ];

    #[Endpoint('List order invoices', 'Retrieve a paginated list of invoices for a specific order')]
    #[UrlParam('order_id', 'integer', 'The order ID', required: true, example: 1)]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> campaign, journal, company, originPayment, taxCashBasisOriginMove, autoPostOrigin, invoicePaymentTerm, partner, commercialPartner, partnerShipping, partnerBank, fiscalPosition, currency, reversedEntry, invoiceUser, invoiceIncoterm, invoiceCashRounding, createdBy, source, medium, paymentMethodLine', required: false, example: 'journal,partner')]
    #[QueryParam('filter[id]', 'string', 'Comma-separated list of invoice IDs to filter by', required: false, example: 'No-example')]
    #[QueryParam('filter[state]', 'string', 'Filter by invoice state', required: false, example: 'posted')]
    #[QueryParam('filter[payment_state]', 'string', 'Filter by payment state', required: false, example: 'paid')]
    #[QueryParam('sort', 'string', 'Sort field', example: '-created_at')]
    #[QueryParam('page', 'int', 'Page number', example: 1)]
    #[ResponseFromApiResource(MoveResource::class, Move::class, collection: true, paginate: 10)]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function index(string $order)
    {
        $orderModel = Order::findOrFail($order);

        Gate::authorize('view', $orderModel);

        $invoices = QueryBuilder::for($orderModel->accountMoves()->where('move_type', MoveType::OUT_INVOICE))
            ->allowedFilters([
                AllowedFilter::exact('id'),
                AllowedFilter::exact('state'),
                AllowedFilter::exact('payment_state'),
            ])
            ->allowedSorts(['id', 'name', 'date', 'amount_total', 'state', 'payment_state', 'created_at'])
            ->allowedIncludes($this->allowedIncludes)
            ->paginate();

        return MoveResource::collection($invoices);
    }
}
