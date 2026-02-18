<?php

namespace Webkul\Purchase\Http\Controllers\API\V1;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Webkul\Account\Enums\MoveState;
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
use Webkul\Account\Facades\Tax as TaxFacade;
use Webkul\PluginManager\Package;
use Webkul\Product\Models\Product;
use Webkul\Purchase\Enums\OrderState;
use Webkul\Purchase\Enums\QtyReceivedMethod;
use Webkul\Purchase\Facades\PurchaseOrder as PurchaseOrderFacade;
use Webkul\Purchase\Http\Requests\PurchaseOrderRequest;
use Webkul\Purchase\Http\Resources\V1\OrderResource;
use Webkul\Purchase\Models\PurchaseOrder;

#[Group('Purchase API Management')]
#[Subgroup('Purchase Orders', 'Manage purchase orders')]
#[Authenticated]
class PurchaseOrderController extends Controller
{
    protected array $allowedIncludes = [
        'partner',
        'currency',
        'fiscalPosition',
        'paymentTerm',
        'incoterm',
        'user',
        'company',
        'creator',
        'operationType',
        'requisition',
        'lines',
        'lines.uom',
        'lines.product',
        'lines.productPackaging',
        'lines.partner',
        'lines.currency',
        'lines.company',
        'lines.creator',
        'lines.finalLocation',
        'lines.taxes',
    ];

    #[Endpoint('List purchase orders', 'Retrieve a paginated list of purchase orders with filtering and sorting')]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> partner, currency, fiscalPosition, paymentTerm, incoterm, user, company, creator, operationType, requisition, lines, lines.uom, lines.product, lines.productPackaging, lines.partner, lines.currency, lines.company, lines.creator, lines.finalLocation, lines.taxes', required: false, example: 'partner,lines')]
    #[QueryParam('filter[id]', 'string', 'Comma-separated list of IDs to filter by', required: false, example: 'No-example')]
    #[QueryParam('filter[state]', 'string', 'Filter by order state', required: false, example: 'draft')]
    #[QueryParam('filter[partner_id]', 'string', 'Filter by vendor IDs', required: false, example: 'No-example')]
    #[QueryParam('filter[currency_id]', 'string', 'Filter by currency IDs', required: false, example: 'No-example')]
    #[QueryParam('sort', 'string', 'Sort field', example: '-created_at')]
    #[QueryParam('page', 'int', 'Page number', example: 1)]
    #[ResponseFromApiResource(OrderResource::class, PurchaseOrder::class, collection: true, paginate: 10)]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function index()
    {
        Gate::authorize('viewAny', PurchaseOrder::class);

        $orders = QueryBuilder::for(PurchaseOrder::class)
            ->allowedFilters([
                AllowedFilter::exact('id'),
                AllowedFilter::exact('state'),
                AllowedFilter::exact('partner_id'),
                AllowedFilter::exact('currency_id'),
                AllowedFilter::exact('user_id'),
                AllowedFilter::exact('company_id'),
                AllowedFilter::exact('requisition_id'),
                AllowedFilter::exact('invoice_status'),
                AllowedFilter::exact('receipt_status'),
            ])
            ->allowedSorts(['id', 'name', 'ordered_at', 'planned_at', 'untaxed_amount', 'total_amount', 'created_at'])
            ->allowedIncludes($this->allowedIncludes)
            ->paginate();

        return OrderResource::collection($orders);
    }

    #[Endpoint('Create purchase order', 'Create a new purchase order with lines')]
    #[ResponseFromApiResource(OrderResource::class, PurchaseOrder::class, status: 201, additional: ['message' => 'Purchase order created successfully.'])]
    #[Response(status: 422, description: 'Validation error', content: '{"message": "The given data was invalid.", "errors": {"partner_id": ["The partner id field is required."], "lines": ["The lines field is required."]}}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function store(PurchaseOrderRequest $request)
    {
        Gate::authorize('create', PurchaseOrder::class);

        $data = $request->validated();

        return DB::transaction(function () use ($data) {
            $lines = $data['lines'];
            unset($data['lines']);

            $order = PurchaseOrder::create($data);

            $this->syncOrderLines($order, $lines);

            $order->load($this->allowedIncludes);

            return (new OrderResource($order))
                ->additional(['message' => 'Purchase order created successfully.'])
                ->response()
                ->setStatusCode(201);
        });
    }

    #[Endpoint('Show purchase order', 'Retrieve a specific purchase order by its ID')]
    #[UrlParam('id', 'integer', 'The purchase order ID', required: true, example: 1)]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> partner, currency, fiscalPosition, paymentTerm, incoterm, user, company, creator, operationType, requisition, lines, lines.uom, lines.product, lines.productPackaging, lines.partner, lines.currency, lines.company, lines.creator, lines.finalLocation, lines.taxes', required: false, example: 'partner,lines')]
    #[ResponseFromApiResource(OrderResource::class, PurchaseOrder::class)]
    #[Response(status: 404, description: 'Purchase order not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function show(string $id)
    {
        $order = QueryBuilder::for(PurchaseOrder::where('id', $id))
            ->allowedIncludes($this->allowedIncludes)
            ->firstOrFail();

        Gate::authorize('view', $order);

        return new OrderResource($order);
    }

    #[Endpoint('Update purchase order', 'Update an existing purchase order and sync lines')]
    #[UrlParam('id', 'integer', 'The purchase order ID', required: true, example: 1)]
    #[ResponseFromApiResource(OrderResource::class, PurchaseOrder::class, additional: ['message' => 'Purchase order updated successfully.'])]
    #[Response(status: 404, description: 'Purchase order not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 422, description: 'Validation error', content: '{"message": "The given data was invalid.", "errors": {"partner_id": ["The partner id field must be an integer."]}}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function update(PurchaseOrderRequest $request, string $id)
    {
        $order = PurchaseOrder::findOrFail($id);

        Gate::authorize('update', $order);

        $data = $request->validated();

        return DB::transaction(function () use ($order, $data) {
            $lines = $data['lines'] ?? null;
            unset($data['lines']);

            $order->update($data);

            if ($lines !== null) {
                $this->syncOrderLines($order, $lines);
            }

            $order->load($this->allowedIncludes);

            return (new OrderResource($order))
                ->additional(['message' => 'Purchase order updated successfully.']);
        });
    }

    #[Endpoint('Delete purchase order', 'Delete a purchase order')]
    #[UrlParam('id', 'integer', 'The purchase order ID', required: true, example: 1)]
    #[Response(status: 200, description: 'Purchase order deleted successfully', content: '{"message": "Purchase order deleted successfully."}')]
    #[Response(status: 404, description: 'Purchase order not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function destroy(string $id)
    {
        $order = PurchaseOrder::findOrFail($id);

        Gate::authorize('delete', $order);

        $order->delete();

        return response()->json([
            'message' => 'Purchase order deleted successfully.',
        ]);
    }

    #[Endpoint('Confirm purchase order', 'Confirm a request for quotation into a purchase order')]
    #[UrlParam('id', 'integer', 'The purchase order ID', required: true, example: 1)]
    #[ResponseFromApiResource(OrderResource::class, PurchaseOrder::class, additional: ['message' => 'Purchase order confirmed successfully.'])]
    #[Response(status: 422, description: 'Invalid state transition', content: '{"message": "Only draft or sent purchase orders can be confirmed."}')]
    #[Response(status: 404, description: 'Purchase order not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function confirm(string $id)
    {
        $order = PurchaseOrder::findOrFail($id);

        Gate::authorize('update', $order);

        if (! in_array($order->state, [OrderState::DRAFT, OrderState::SENT], true)) {
            return response()->json([
                'message' => 'Only draft or sent purchase orders can be confirmed.',
            ], 422);
        }

        $order = PurchaseOrderFacade::confirmPurchaseOrder($order);

        return (new OrderResource($order->load($this->allowedIncludes)))
            ->additional(['message' => 'Purchase order confirmed successfully.']);
    }

    #[Endpoint('Cancel purchase order', 'Cancel a purchase order')]
    #[UrlParam('id', 'integer', 'The purchase order ID', required: true, example: 1)]
    #[ResponseFromApiResource(OrderResource::class, PurchaseOrder::class, additional: ['message' => 'Purchase order canceled successfully.'])]
    #[Response(status: 422, description: 'Invalid state transition', content: '{"message": "Only non-locked and non-canceled purchase orders can be canceled."}')]
    #[Response(status: 404, description: 'Purchase order not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function cancel(string $id)
    {
        $order = PurchaseOrder::with(['lines', 'accountMoves'])->findOrFail($id);

        Gate::authorize('update', $order);

        if (in_array($order->state, [OrderState::DONE, OrderState::CANCELED], true)) {
            return response()->json([
                'message' => 'Only non-locked and non-canceled purchase orders can be canceled.',
            ], 422);
        }

        if ($order->lines->contains(fn ($line) => (float) $line->qty_received > 0)) {
            return response()->json([
                'message' => 'The order cannot be canceled since they have receipts that are already done.',
            ], 422);
        }

        if ($order->accountMoves->contains(fn ($move) => $move->state !== MoveState::CANCEL)) {
            return response()->json([
                'message' => 'The order cannot be canceled. You must first cancel their related vendor bills.',
            ], 422);
        }

        $order = PurchaseOrderFacade::cancelPurchaseOrder($order);

        return (new OrderResource($order->load($this->allowedIncludes)))
            ->additional(['message' => 'Purchase order canceled successfully.']);
    }

    #[Endpoint('Set purchase order to draft', 'Move a canceled purchase order back to draft')]
    #[UrlParam('id', 'integer', 'The purchase order ID', required: true, example: 1)]
    #[ResponseFromApiResource(OrderResource::class, PurchaseOrder::class, additional: ['message' => 'Purchase order set to draft successfully.'])]
    #[Response(status: 422, description: 'Invalid state transition', content: '{"message": "Only canceled purchase orders can be set to draft."}')]
    #[Response(status: 404, description: 'Purchase order not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function draft(string $id)
    {
        $order = PurchaseOrder::findOrFail($id);

        Gate::authorize('update', $order);

        if ($order->state !== OrderState::CANCELED) {
            return response()->json([
                'message' => 'Only canceled purchase orders can be set to draft.',
            ], 422);
        }

        $order = PurchaseOrderFacade::draftPurchaseOrder($order);

        return (new OrderResource($order->load($this->allowedIncludes)))
            ->additional(['message' => 'Purchase order set to draft successfully.']);
    }

    #[Endpoint('Toggle purchase order lock', 'Toggle lock state between purchase and done')]
    #[UrlParam('id', 'integer', 'The purchase order ID', required: true, example: 1)]
    #[ResponseFromApiResource(OrderResource::class, PurchaseOrder::class, additional: ['message' => 'Purchase order lock state updated successfully.'])]
    #[Response(status: 422, description: 'Invalid state transition', content: '{"message": "Only purchase or done orders can toggle lock state."}')]
    #[Response(status: 404, description: 'Purchase order not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function toggleLock(string $id)
    {
        $order = PurchaseOrder::findOrFail($id);

        Gate::authorize('update', $order);

        if (! in_array($order->state, [OrderState::PURCHASE, OrderState::DONE], true)) {
            return response()->json([
                'message' => 'Only purchase or done orders can toggle lock state.',
            ], 422);
        }

        $order = $order->state === OrderState::PURCHASE
            ? PurchaseOrderFacade::lockPurchaseOrder($order)
            : PurchaseOrderFacade::unlockPurchaseOrder($order);

        return (new OrderResource($order->load($this->allowedIncludes)))
            ->additional(['message' => 'Purchase order lock state updated successfully.']);
    }

    #[Endpoint('Confirm purchase order receipt date', 'Mark purchase order receipt reminder as confirmed')]
    #[UrlParam('id', 'integer', 'The purchase order ID', required: true, example: 1)]
    #[ResponseFromApiResource(OrderResource::class, PurchaseOrder::class, additional: ['message' => 'Purchase order receipt date confirmed successfully.'])]
    #[Response(status: 422, description: 'Invalid state transition', content: '{"message": "Only unconfirmed purchase or done orders can confirm receipt date."}')]
    #[Response(status: 404, description: 'Purchase order not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function confirmReceiptDate(string $id)
    {
        $order = PurchaseOrder::findOrFail($id);

        Gate::authorize('update', $order);

        if ($order->mail_reminder_confirmed || ! in_array($order->state, [OrderState::PURCHASE, OrderState::DONE], true)) {
            return response()->json([
                'message' => 'Only unconfirmed purchase or done orders can confirm receipt date.',
            ], 422);
        }

        $order->update([
            'mail_reminder_confirmed' => true,
        ]);

        return (new OrderResource($order->load($this->allowedIncludes)))
            ->additional(['message' => 'Purchase order receipt date confirmed successfully.']);
    }

    protected function syncOrderLines(PurchaseOrder $order, array $linesData): void
    {
        $submittedIds = collect($linesData)
            ->pluck('id')
            ->filter()
            ->toArray();

        $order->lines()
            ->whereNotIn('id', $submittedIds)
            ->delete();

        foreach ($linesData as $lineData) {
            $taxes = $lineData['taxes'] ?? [];
            $lineId = $lineData['id'] ?? null;

            unset($lineData['taxes'], $lineData['id']);

            $lineData = Arr::only($lineData, [
                'product_id',
                'planned_at',
                'product_qty',
                'qty_received',
                'qty_received_manual',
                'uom_id',
                'product_packaging_qty',
                'product_packaging_id',
                'price_unit',
                'discount',
            ]);

            $product = isset($lineData['product_id'])
                ? Product::find($lineData['product_id'])
                : null;

            [$priceSubtotal, $priceTax, $priceTotal] = $this->computeLineAmounts($lineData['price_unit'] ?? 0, $lineData['product_qty'] ?? 0, $lineData['discount'] ?? 0, $taxes);

            $qtyReceivedMethod = Package::isPluginInstalled('inventories')
                ? QtyReceivedMethod::STOCK_MOVE
                : QtyReceivedMethod::MANUAL;

            $payload = array_merge([
                'name'                => $product?->name ?? 'Line Item',
                'state'               => $order->state?->value,
                'qty_received_method' => $qtyReceivedMethod,
                'uom_id'              => $lineData['uom_id'] ?? $product?->uom_id,
                'currency_id'         => $order->currency_id,
                'partner_id'          => $order->partner_id,
                'company_id'          => $order->company_id,
                'product_qty'         => $lineData['product_qty'] ?? 0,
                'product_uom_qty'     => $lineData['product_qty'] ?? 0,
                'price_unit'          => $lineData['price_unit'] ?? 0,
                'discount'            => $lineData['discount'] ?? 0,
                'price_subtotal'      => $priceSubtotal,
                'price_tax'           => $priceTax,
                'price_total'         => $priceTotal,
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

            $orderLine->taxes()->sync($taxes);
        }

        $this->syncOrderTotals($order);
    }

    protected function computeLineAmounts(float $priceUnit, float $quantity, float $discount, array $taxIds): array
    {
        $subTotal = $priceUnit * $quantity;

        if ($discount > 0) {
            $subTotal -= ($subTotal * ($discount / 100));
        }

        [$subTotalAfterTax, $taxAmount] = TaxFacade::collect($taxIds, $subTotal, $quantity);

        return [
            round($subTotalAfterTax, 4),
            round($taxAmount, 4),
            round($subTotalAfterTax + $taxAmount, 4),
        ];
    }

    protected function syncOrderTotals(PurchaseOrder $order): void
    {
        $order->loadMissing('lines');

        $untaxed = round((float) $order->lines->sum('price_subtotal'), 4);
        $tax = round((float) $order->lines->sum('price_tax'), 4);
        $total = round((float) $order->lines->sum('price_total'), 4);

        $order->update([
            'untaxed_amount' => $untaxed,
            'tax_amount'     => $tax,
            'total_amount'   => $total,
            'total_cc_amount'=> $total,
            'planned_at'     => $order->planned_at ?? $order->lines->max('planned_at'),
        ]);
    }
}
