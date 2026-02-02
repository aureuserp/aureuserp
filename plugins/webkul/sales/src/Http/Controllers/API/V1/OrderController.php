<?php

namespace Webkul\Sale\Http\Controllers\API\V1;

use Illuminate\Http\Request;
use Knuckles\Scribe\Attributes\BodyParam;
use Knuckles\Scribe\Attributes\Endpoint;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\QueryParam;
use Knuckles\Scribe\Attributes\ResponseFromApiResource;
use Knuckles\Scribe\Attributes\ResponseFromFile;
use Knuckles\Scribe\Attributes\Subgroup;
use Knuckles\Scribe\Attributes\UrlParam;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use Webkul\Sale\Enums\OrderState;
use Webkul\Sale\Http\Resources\V1\OrderResource;
use Webkul\Sale\Models\Order;

#[Group('Sales API management')]
#[Subgroup('Orders', 'Do stuff with orders')]
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

    /**
     * Store a newly created resource in storage.
     *
     * @bodyParam name string Name of the car. Example: Honda
     * @bodyParam model string Model of the car. Example: 2020
     * @bodyParam cars object[] List of car details. Example: [{"name": "Carpenter", "year": 2019}, {"name": "Electric", "year": 2020}]
     * @bodyParam cars[].name string Name of the car.
     * @bodyParam cars[].year int Example: 1997
     */
    public function store(Request $request)
    {
        dd($request->all());
    }

    #[Endpoint('Show order', 'Retrieve a specific order by its ID')]
    #[UrlParam('id', 'integer', 'The order ID', required: true, example: 1)]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> partner, partnerInvoice, partnerShipping, user, team, company, currency, paymentTerm, fiscalPosition, journal, campaign, utmSource, medium, warehouse, lines, lines.product, lines.linkedSaleOrderSale, lines.uom, lines.productPackaging, lines.currency, lines.orderPartner, lines.salesman, lines.warehouse, lines.route, lines.company', required: false, example: 'partner,lines')]
    #[ResponseFromApiResource(OrderResource::class, Order::class, with: ['partner', 'lines'])]
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

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
