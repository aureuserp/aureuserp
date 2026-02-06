<?php

namespace Webkul\Sale\Http\Controllers\API\V1;

use Illuminate\Http\Request;
use Knuckles\Scribe\Attributes\Authenticated;
use Knuckles\Scribe\Attributes\BodyParam;
use Knuckles\Scribe\Attributes\Endpoint;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\QueryParam;
use Knuckles\Scribe\Attributes\Response;
use Knuckles\Scribe\Attributes\ResponseFromApiResource;
use Knuckles\Scribe\Attributes\ResponseFromFile;
use Knuckles\Scribe\Attributes\Subgroup;
use Knuckles\Scribe\Attributes\UrlParam;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use Webkul\Sale\Enums\OrderState;
use Webkul\Sale\Http\Resources\V1\OrderResource;
use Webkul\Sale\Models\Order;

#[Group('Sales API Management')]
#[Subgroup('Orders', 'Do stuff with orders')]
#[Authenticated]
class OrderController extends Controller
{
    #[Endpoint('List orders', 'Retrieve a paginated list of orders with filtering and sorting')]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> partner, partnerInvoice, partnerShipping, user, team, company, currency, paymentTerm, fiscalPosition, journal, campaign, utmSource, medium, warehouse, lines, lines.product, lines.linkedSaleOrderSale, lines.uom, lines.productPackaging, lines.currency, lines.orderPartner, lines.salesman, lines.warehouse, lines.route, lines.company', required: false, example: 'partner,lines')]
    #[QueryParam('filter[id]', 'string', 'Comma-separated list of IDs to filter by', required: false, example: 'No-example')]
    #[QueryParam('filter[state]', 'string', 'Filter by state', enum: OrderState::class, required: false, example: 'No-example')]
    #[QueryParam('filter[partner_id]', 'string', 'Comma-separated list of partner IDs to filter by', required: false, example: 'No-example')]
    #[QueryParam('sort', 'string', 'Sort field', example: '-created_at')]
    #[QueryParam('page', 'int', 'Page number', example: 1)]
    #[ResponseFromApiResource(OrderResource::class, Order::class, collection: true, paginate: 10, with: ['partner', 'lines'])]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function index()
    {
        $orders = QueryBuilder::for(Order::class)
            ->allowedFilters([
                AllowedFilter::exact('id'),
                AllowedFilter::exact('state'),
                AllowedFilter::exact('partner_id'),
            ])
            ->allowedSorts(['id', 'state', 'created_at'])
            ->allowedIncludes([
                'partner',
                'partnerInvoice',
                'partnerShipping',
                'user',
                'team',
                'company',
                'currency',
                'paymentTerm',
                'fiscalPosition',
                'journal',
                'campaign',
                'utmSource',
                'medium',
                'warehouse',
                'lines',
                'lines.product',
                'lines.linkedSaleOrderSale',
                'lines.uom',
                'lines.productPackaging',
                'lines.currency',
                'lines.orderPartner',
                'lines.salesman',
                'lines.warehouse',
                'lines.route',
                'lines.company',
            ])
            ->paginate();

        return OrderResource::collection($orders);
    }

    #[Endpoint('Create order', 'Create a new order')]
    #[BodyParam('name', 'string', 'The name of the order', required: true, example: 'SO001')]
    #[BodyParam('partner_id', 'integer', 'The ID of the partner', required: true, example: 1)]
    #[BodyParam('message', 'string', 'Additional message or notes', required: false, example: 'Customer requested expedited shipping')]
    #[ResponseFromApiResource(OrderResource::class, Order::class, status: 201, with: ['partner', 'lines'], additional: ['message' => 'Order created successfully.'])]
    #[Response(status: 422, description: 'Validation error', content: '{"message": "The given data was invalid.", "errors": {"name": ["The name field is required."], "partner_id": ["The partner id field is required."]}}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function store(Request $request)
    {
        dd($request->all());
    }

    #[Endpoint('Show order', 'Retrieve a specific order by its ID')]
    #[UrlParam('id', 'integer', 'The order ID', required: true, example: 1)]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> partner, partnerInvoice, partnerShipping, user, team, company, currency, paymentTerm, fiscalPosition, journal, campaign, utmSource, medium, warehouse, lines, lines.product, lines.linkedSaleOrderSale, lines.uom, lines.productPackaging, lines.currency, lines.orderPartner, lines.salesman, lines.warehouse, lines.route, lines.company', required: false, example: 'partner,lines')]
    #[ResponseFromApiResource(OrderResource::class, Order::class, with: ['partner', 'lines'])]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function show(string $id)
    {
        $order = QueryBuilder::for(Order::where('id', $id))
            ->allowedIncludes([
                'partner',
                'partnerInvoice',
                'partnerShipping',
                'user',
                'team',
                'company',
                'currency',
                'paymentTerm',
                'fiscalPosition',
                'journal',
                'campaign',
                'utmSource',
                'medium',
                'warehouse',
                'lines',
                'lines.product',
                'lines.linkedSaleOrderSale',
                'lines.uom',
                'lines.productPackaging',
                'lines.currency',
                'lines.orderPartner',
                'lines.salesman',
                'lines.warehouse',
                'lines.route',
                'lines.company',
            ])
            ->firstOrFail();

        return new OrderResource($order);
    }

    #[Endpoint('Update order', 'Update an existing order')]
    #[UrlParam('id', 'integer', 'The order ID', required: true, example: 1)]
    #[BodyParam('name', 'string', 'The name of the order', required: false, example: 'SO001')]
    #[BodyParam('partner_id', 'integer', 'The ID of the partner', required: false, example: 1)]
    #[BodyParam('message', 'string', 'Additional message or notes', required: false, example: 'Customer requested expedited shipping')]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> partner, partnerInvoice, partnerShipping, user, team, company, currency, paymentTerm, fiscalPosition, journal, campaign, utmSource, medium, warehouse, lines, lines.product, lines.linkedSaleOrderSale, lines.uom, lines.productPackaging, lines.currency, lines.orderPartner, lines.salesman, lines.warehouse, lines.route, lines.company', required: false, example: 'partner,lines')]
    #[ResponseFromApiResource(OrderResource::class, Order::class, with: ['partner', 'lines'], additional: ['message' => 'Order updated successfully.'])]
    #[Response(status: 422, description: 'Validation error', content: '{"message": "The given data was invalid.", "errors": {"name": ["The name field must be a string."]}}')]
    #[Response(status: 404, description: 'Order not found', content: '{"message": "Order not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function update(Request $request, string $id)
    {
        //
    }

    #[Endpoint('Delete order', 'Delete an existing order')]
    #[UrlParam('id', 'integer', 'The order ID', required: true, example: 1)]
    #[Response(status: 200, description: 'Order deleted successfully', content: '{"message": "Order has been deleted successfully."}')]
    #[Response(status: 404, description: 'Order not found', content: '{"message": "Order not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function destroy(string $id)
    {
        //
    }
}
