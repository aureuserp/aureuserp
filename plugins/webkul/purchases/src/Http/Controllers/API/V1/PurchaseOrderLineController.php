<?php

namespace Webkul\Purchase\Http\Controllers\API\V1;

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
use Webkul\Purchase\Http\Resources\V1\OrderLineResource;
use Webkul\Purchase\Models\OrderLine;
use Webkul\Purchase\Models\PurchaseOrder;

#[Group('Purchase API Management')]
#[Subgroup('Purchase Order Lines', 'Manage purchase order line items')]
#[Authenticated]
class PurchaseOrderLineController extends Controller
{
    protected array $allowedIncludes = [
        'uom',
        'product',
        'productPackaging',
        'order',
        'partner',
        'currency',
        'company',
        'creator',
        'finalLocation',
        'taxes',
    ];

    #[Endpoint('List purchase order lines', 'Retrieve a paginated list of line items for a specific purchase order')]
    #[UrlParam('purchase_order_id', 'integer', 'The purchase order ID', required: true, example: 1)]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> uom, product, productPackaging, order, partner, currency, company, creator, finalLocation, taxes', required: false, example: 'product,taxes')]
    #[QueryParam('filter[id]', 'string', 'Comma-separated list of line IDs to filter by', required: false, example: 'No-example')]
    #[QueryParam('filter[product_id]', 'string', 'Filter by product IDs', required: false, example: 'No-example')]
    #[QueryParam('sort', 'string', 'Sort field', example: '-id')]
    #[QueryParam('page', 'int', 'Page number', example: 1)]
    #[ResponseFromApiResource(OrderLineResource::class, OrderLine::class, collection: true, paginate: 10)]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function index(string $purchaseOrder)
    {
        $purchaseOrderModel = PurchaseOrder::findOrFail($purchaseOrder);

        Gate::authorize('view', $purchaseOrderModel);

        $lines = QueryBuilder::for(OrderLine::where('order_id', $purchaseOrderModel->id))
            ->allowedFilters([
                AllowedFilter::exact('id'),
                AllowedFilter::exact('product_id'),
            ])
            ->allowedSorts(['id', 'sort', 'planned_at', 'product_qty', 'price_unit', 'created_at'])
            ->allowedIncludes($this->allowedIncludes)
            ->paginate();

        return OrderLineResource::collection($lines);
    }

    #[Endpoint('Show purchase order line', 'Retrieve a specific line item from a purchase order')]
    #[UrlParam('purchase_order_id', 'integer', 'The purchase order ID', required: true, example: 1)]
    #[UrlParam('id', 'integer', 'The purchase order line ID', required: true, example: 1)]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> uom, product, productPackaging, order, partner, currency, company, creator, finalLocation, taxes', required: false, example: 'product,taxes')]
    #[ResponseFromApiResource(OrderLineResource::class, OrderLine::class)]
    #[Response(status: 404, description: 'Purchase order or line not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function show(string $purchaseOrder, string $line)
    {
        $purchaseOrderModel = PurchaseOrder::findOrFail($purchaseOrder);

        Gate::authorize('view', $purchaseOrderModel);

        $orderLine = QueryBuilder::for(OrderLine::where('order_id', $purchaseOrderModel->id)->where('id', $line))
            ->allowedIncludes($this->allowedIncludes)
            ->firstOrFail();

        return new OrderLineResource($orderLine);
    }
}
