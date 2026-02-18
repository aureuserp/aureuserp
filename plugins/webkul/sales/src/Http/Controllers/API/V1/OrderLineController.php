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
use Webkul\Sale\Http\Resources\V1\OrderLineResource;
use Webkul\Sale\Models\Order;
use Webkul\Sale\Models\OrderLine;

#[Group('Sales API Management')]
#[Subgroup('Order Lines', 'Manage order line items')]
#[Authenticated]
class OrderLineController extends Controller
{
    protected array $allowedIncludes = [
        'product',
        'linkedSaleOrderSale',
        'uom',
        'productPackaging',
        'currency',
        'orderPartner',
        'salesman',
        'warehouse',
        'route',
        'company',
        'order',
    ];

    #[Endpoint('List order lines', 'Retrieve a paginated list of line items for a specific order')]
    #[UrlParam('order_id', 'integer', 'The order ID', required: true, example: 1)]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> product, linkedSaleOrderSale, uom, productPackaging, currency, orderPartner, salesman, warehouse, route, company, order', required: false, example: 'product')]
    #[QueryParam('filter[id]', 'string', 'Comma-separated list of line IDs to filter by', required: false, example: 'No-example')]
    #[QueryParam('filter[product_id]', 'string', 'Comma-separated list of product IDs to filter by', required: false, example: 'No-example')]
    #[QueryParam('sort', 'string', 'Sort field', example: '-id')]
    #[QueryParam('page', 'int', 'Page number', example: 1)]
    #[ResponseFromApiResource(OrderLineResource::class, OrderLine::class, collection: true, with: ['product'])]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function index(string $order)
    {
        $orderModel = Order::findOrFail($order);

        Gate::authorize('view', $orderModel);

        $lines = QueryBuilder::for(OrderLine::where('order_id', $orderModel->id))
            ->allowedFilters([
                AllowedFilter::exact('id'),
                AllowedFilter::exact('product_id'),
            ])
            ->allowedSorts(['id', 'sort', 'name', 'created_at'])
            ->allowedIncludes($this->allowedIncludes)
            ->paginate();

        return OrderLineResource::collection($lines);
    }

    #[Endpoint('Show order line', 'Retrieve a specific line item from an order')]
    #[UrlParam('order_id', 'integer', 'The order ID', required: true, example: 1)]
    #[UrlParam('id', 'integer', 'The order line ID', required: true, example: 1)]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> product, linkedSaleOrderSale, uom, productPackaging, currency, orderPartner, salesman, warehouse, route, company, order', required: false, example: 'product')]
    #[ResponseFromApiResource(OrderLineResource::class, OrderLine::class, with: ['product'])]
    #[Response(status: 404, description: 'Order or order line not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function show(string $order, string $line)
    {
        $orderModel = Order::findOrFail($order);

        Gate::authorize('view', $orderModel);

        $orderLine = QueryBuilder::for(OrderLine::where('order_id', $orderModel->id)->where('id', $line))
            ->allowedIncludes($this->allowedIncludes)
            ->firstOrFail();

        return new OrderLineResource($orderLine);
    }
}
