<?php

namespace Webkul\Sale\Http\Controllers\API\V1;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Arr;
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
use Webkul\Product\Models\Product;
use Webkul\Sale\Enums\OrderState;
use Webkul\Sale\Facades\SaleOrder as SaleOrderFacade;
use Webkul\Sale\Http\Requests\OrderRequest;
use Webkul\Sale\Http\Resources\V1\OrderResource;
use Webkul\Sale\Models\Order;

#[Group('Sales API Management')]
#[Subgroup('Orders', 'Manage sales orders')]
#[Authenticated]
class OrderController extends Controller
{
    protected array $allowedIncludes = [
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
    ];

    #[Endpoint('List orders', 'Retrieve a paginated list of orders with filtering and sorting')]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> partner, partnerInvoice, partnerShipping, user, team, company, currency, paymentTerm, fiscalPosition, journal, campaign, utmSource, medium, warehouse, lines, lines.product, lines.linkedSaleOrderSale, lines.uom, lines.productPackaging, lines.currency, lines.orderPartner, lines.salesman, lines.warehouse, lines.route, lines.company', required: false, example: 'partner,lines')]
    #[QueryParam('filter[id]', 'string', 'Comma-separated list of IDs to filter by', required: false, example: 'No-example')]
    #[QueryParam('filter[state]', 'string', 'Filter by state', enum: OrderState::class, required: false, example: 'No-example')]
    #[QueryParam('filter[partner_id]', 'string', 'Comma-separated list of partner IDs to filter by', required: false, example: 'No-example')]
    #[QueryParam('filter[trashed]', 'string', 'Filter by trashed status. </br></br><b>Available options:</b> with, only', required: false, example: 'with')]
    #[QueryParam('sort', 'string', 'Sort field', example: '-created_at')]
    #[QueryParam('page', 'int', 'Page number', example: 1)]
    #[ResponseFromApiResource(OrderResource::class, Order::class, collection: true, paginate: 10, with: ['partner', 'lines'])]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function index()
    {
        Gate::authorize('viewAny', Order::class);

        $orders = QueryBuilder::for(Order::class)
            ->allowedFilters([
                AllowedFilter::exact('id'),
                AllowedFilter::exact('state'),
                AllowedFilter::exact('partner_id'),
                AllowedFilter::exact('user_id'),
                AllowedFilter::exact('team_id'),
                AllowedFilter::exact('company_id'),
                AllowedFilter::exact('currency_id'),
                AllowedFilter::exact('invoice_status'),
                AllowedFilter::trashed(),
            ])
            ->allowedSorts(['id', 'name', 'state', 'date_order', 'amount_total', 'created_at', 'updated_at'])
            ->allowedIncludes($this->allowedIncludes)
            ->paginate();

        return OrderResource::collection($orders);
    }

    #[Endpoint('Create order', 'Create a new order with line items')]
    #[ResponseFromApiResource(OrderResource::class, Order::class, status: 201, with: ['partner', 'lines'], additional: ['message' => 'Order created successfully.'])]
    #[Response(status: 422, description: 'Validation error', content: '{"message": "The given data was invalid.", "errors": {"partner_id": ["The partner id field is required."], "lines": ["The lines field is required."]}}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function store(OrderRequest $request)
    {
        Gate::authorize('create', Order::class);

        $data = $request->validated();

        return DB::transaction(function () use ($data) {
            $lines = $data['lines'];
            $tags = $data['sales_order_tags'] ?? [];

            unset($data['lines'], $data['sales_order_tags']);

            $order = Order::create($data);

            if (! empty($tags)) {
                $order->tags()->sync($tags);
            }

            $this->syncOrderLines($order, $lines);

            $order = SaleOrderFacade::computeSaleOrder($order->refresh());

            $order->load(['partner', 'paymentTerm', 'currency', 'lines.product', 'lines.uom']);

            return (new OrderResource($order))
                ->additional(['message' => 'Order created successfully.'])
                ->response()
                ->setStatusCode(201);
        });
    }

    #[Endpoint('Show order', 'Retrieve a specific order by its ID')]
    #[UrlParam('id', 'integer', 'The order ID', required: true, example: 1)]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> partner, partnerInvoice, partnerShipping, user, team, company, currency, paymentTerm, fiscalPosition, journal, campaign, utmSource, medium, warehouse, lines, lines.product, lines.linkedSaleOrderSale, lines.uom, lines.productPackaging, lines.currency, lines.orderPartner, lines.salesman, lines.warehouse, lines.route, lines.company', required: false, example: 'partner,lines')]
    #[ResponseFromApiResource(OrderResource::class, Order::class, with: ['partner', 'lines'])]
    #[Response(status: 404, description: 'Order not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function show(string $id)
    {
        $order = QueryBuilder::for(Order::where('id', $id))
            ->allowedIncludes($this->allowedIncludes)
            ->firstOrFail();

        Gate::authorize('view', $order);

        return new OrderResource($order);
    }

    #[Endpoint('Update order', 'Update an existing order and sync line items')]
    #[UrlParam('id', 'integer', 'The order ID', required: true, example: 1)]
    #[ResponseFromApiResource(OrderResource::class, Order::class, with: ['partner', 'lines'], additional: ['message' => 'Order updated successfully.'])]
    #[Response(status: 422, description: 'Validation error', content: '{"message": "The given data was invalid.", "errors": {"partner_id": ["The partner id field must be an integer."]}}')]
    #[Response(status: 404, description: 'Order not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function update(OrderRequest $request, string $id)
    {
        $order = Order::findOrFail($id);

        Gate::authorize('update', $order);

        $data = $request->validated();

        return DB::transaction(function () use ($order, $data) {
            $lines = $data['lines'] ?? null;
            $tags = $data['sales_order_tags'] ?? null;

            unset($data['lines'], $data['sales_order_tags']);

            $order->update($data);

            if ($tags !== null) {
                $order->tags()->sync($tags);
            }

            if ($lines !== null) {
                $this->syncOrderLines($order, $lines);
            }

            $order = SaleOrderFacade::computeSaleOrder($order->refresh());

            $order->load(['partner', 'paymentTerm', 'currency', 'lines.product', 'lines.uom']);

            return (new OrderResource($order))
                ->additional(['message' => 'Order updated successfully.']);
        });
    }

    #[Endpoint('Confirm order', 'Confirm a quotation and convert it to a sale order')]
    #[UrlParam('id', 'integer', 'The order ID', required: true, example: 1)]
    #[ResponseFromApiResource(OrderResource::class, Order::class, with: ['partner', 'lines'], additional: ['message' => 'Order confirmed successfully.'])]
    #[Response(status: 422, description: 'Invalid state transition', content: '{"message": "Only draft or sent orders can be confirmed."}')]
    #[Response(status: 404, description: 'Order not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function confirm(string $id)
    {
        $order = Order::findOrFail($id);

        Gate::authorize('update', $order);

        if (! in_array($order->state, [OrderState::DRAFT, OrderState::SENT], true)) {
            return response()->json([
                'message' => 'Only draft or sent orders can be confirmed.',
            ], 422);
        }

        try {
            $order = SaleOrderFacade::confirmSaleOrder($order);
        } catch (Exception $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 422);
        }

        return (new OrderResource($order->load(['partner', 'paymentTerm', 'currency', 'lines.product', 'lines.uom'])))
            ->additional(['message' => 'Order confirmed successfully.']);
    }

    #[Endpoint('Cancel order', 'Cancel a sale order')]
    #[UrlParam('id', 'integer', 'The order ID', required: true, example: 1)]
    #[ResponseFromApiResource(OrderResource::class, Order::class, with: ['partner', 'lines'], additional: ['message' => 'Order canceled successfully.'])]
    #[Response(status: 422, description: 'Invalid state transition', content: '{"message": "Only draft, sent, or sale orders can be canceled."}')]
    #[Response(status: 404, description: 'Order not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function cancel(Request $request, string $id)
    {
        $order = Order::findOrFail($id);

        Gate::authorize('update', $order);

        if (! in_array($order->state, [OrderState::DRAFT, OrderState::SENT, OrderState::SALE], true)) {
            return response()->json([
                'message' => 'Only draft, sent, or sale orders can be canceled.',
            ], 422);
        }

        $data = $request->validate([
            'partners' => ['nullable', 'array'],
            'partners.*' => ['integer', 'exists:partners_partners,id'],
            'subject' => ['required_with:partners_partners', 'string', 'max:255'],
            'description' => ['required_with:partners_partners', 'string'],
        ]);

        $order = SaleOrderFacade::cancelSaleOrder($order, ! empty($data['partners']) ? $data : []);

        return (new OrderResource($order->load(['partner', 'paymentTerm', 'currency', 'lines.product', 'lines.uom'])))
            ->additional(['message' => 'Order canceled successfully.']);
    }

    #[Endpoint('Set order as quotation', 'Set a canceled sale order back to quotation')]
    #[UrlParam('id', 'integer', 'The order ID', required: true, example: 1)]
    #[ResponseFromApiResource(OrderResource::class, Order::class, with: ['partner', 'lines'], additional: ['message' => 'Order set as quotation successfully.'])]
    #[Response(status: 422, description: 'Invalid state transition', content: '{"message": "Only canceled orders can be set as quotation."}')]
    #[Response(status: 404, description: 'Order not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function setAsQuotation(string $id)
    {
        $order = Order::findOrFail($id);

        Gate::authorize('update', $order);

        if ($order->state !== OrderState::CANCEL) {
            return response()->json([
                'message' => 'Only canceled orders can be set as quotation.',
            ], 422);
        }

        $order = SaleOrderFacade::backToQuotation($order);

        return (new OrderResource($order->load(['partner', 'paymentTerm', 'currency', 'lines.product', 'lines.uom'])))
            ->additional(['message' => 'Order set as quotation successfully.']);
    }

    #[Endpoint('Toggle sale order lock', 'Toggle lock status for a confirmed sale order')]
    #[UrlParam('id', 'integer', 'The order ID', required: true, example: 1)]
    #[ResponseFromApiResource(OrderResource::class, Order::class, with: ['partner', 'lines'], additional: ['message' => 'Order lock state updated successfully.'])]
    #[Response(status: 422, description: 'Invalid state transition', content: '{"message": "Only sale orders can toggle lock state."}')]
    #[Response(status: 404, description: 'Order not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function toggleLock(string $id)
    {
        $order = Order::findOrFail($id);

        Gate::authorize('update', $order);

        if ($order->state !== OrderState::SALE) {
            return response()->json([
                'message' => 'Only sale orders can toggle lock state.',
            ], 422);
        }

        $order = SaleOrderFacade::lockAndUnlock($order);

        return (new OrderResource($order->load(['partner', 'paymentTerm', 'currency', 'lines.product', 'lines.uom'])))
            ->additional(['message' => 'Order lock state updated successfully.']);
    }

    #[Endpoint('Delete order', 'Soft delete an order')]
    #[UrlParam('id', 'integer', 'The order ID', required: true, example: 1)]
    #[Response(status: 200, description: 'Order deleted successfully', content: '{"message": "Order deleted successfully."}')]
    #[Response(status: 404, description: 'Order not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function destroy(string $id)
    {
        $order = Order::findOrFail($id);

        Gate::authorize('delete', $order);

        $order->delete();

        return response()->json([
            'message' => 'Order deleted successfully.',
        ]);
    }

    #[Endpoint('Restore order', 'Restore a soft-deleted order')]
    #[UrlParam('id', 'integer', 'The order ID', required: true, example: 1)]
    #[ResponseFromApiResource(OrderResource::class, Order::class, with: ['partner', 'lines'], additional: ['message' => 'Order restored successfully.'])]
    #[Response(status: 404, description: 'Order not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function restore(string $id)
    {
        $order = Order::withTrashed()->findOrFail($id);

        Gate::authorize('restore', $order);

        $order->restore();

        return (new OrderResource($order->load(['partner', 'paymentTerm', 'currency', 'lines.product', 'lines.uom'])))
            ->additional(['message' => 'Order restored successfully.']);
    }

    #[Endpoint('Force delete order', 'Permanently delete an order')]
    #[UrlParam('id', 'integer', 'The order ID', required: true, example: 1)]
    #[Response(status: 200, description: 'Order permanently deleted', content: '{"message": "Order permanently deleted."}')]
    #[Response(status: 404, description: 'Order not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function forceDestroy(string $id)
    {
        $order = Order::withTrashed()->findOrFail($id);

        Gate::authorize('forceDelete', $order);

        $order->forceDelete();

        return response()->json([
            'message' => 'Order permanently deleted.',
        ]);
    }

    protected function syncOrderLines(Order $order, array $linesData): void
    {
        $submittedIds = collect($linesData)
            ->pluck('id')
            ->filter()
            ->toArray();

        $order->lines()
            ->whereNotIn('id', $submittedIds)
            ->delete();

        foreach ($linesData as $lineData) {
            $taxes = $lineData['taxes'] ?? null;
            $lineId = $lineData['id'] ?? null;

            unset($lineData['taxes'], $lineData['id']);

            $lineData = Arr::only($lineData, [
                'product_id',
                'product_qty',
                'qty_delivered',
                'product_uom_id',
                'customer_lead',
                'product_packaging_qty',
                'product_packaging_id',
                'price_unit',
                'discount',
                'warehouse_id',
            ]);

            $product = isset($lineData['product_id'])
                ? Product::withTrashed()->find($lineData['product_id'])
                : null;

            $payload = array_merge([
                'name' => $product?->name ?? 'Line Item',
                'state' => $order->state?->value,
                'product_uom_id' => $lineData['product_uom_id'] ?? $product?->uom_id,
                'company_id' => $order->company_id,
                'currency_id' => $order->currency_id,
                'order_partner_id' => $order->partner_id,
                'salesman_id' => $order->user_id,
                'product_qty' => $lineData['product_qty'] ?? 0,
                'product_uom_qty' => $lineData['product_qty'] ?? 0,
                'price_unit' => $lineData['price_unit'] ?? 0,
                'discount' => $lineData['discount'] ?? 0,
                'customer_lead' => $lineData['customer_lead'] ?? 0,
            ], $lineData);

            if ($lineId) {
                $orderLine = $order->lines()->find($lineId);

                if (! $orderLine) {
                    continue;
                }

                $orderLine->update($payload);
            } else {
                $orderLine = $order->lines()->create($payload);
            }

            if ($taxes !== null) {
                $orderLine->taxes()->sync($taxes);
            }
        }
    }
}
