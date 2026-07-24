<?php

use Webkul\Inventory\Enums\DeliveryStep;
use Webkul\Inventory\Enums\LocationType;
use Webkul\Inventory\Enums\MoveState;
use Webkul\Inventory\Enums\OperationState;
use Webkul\Inventory\Enums\ProductTracking;
use Webkul\Inventory\Enums\ReceptionStep;
use Webkul\Inventory\Facades\Inventory;
use Webkul\Inventory\Models\Move;
use Webkul\Sale\Enums\AdvancedPayment;
use Webkul\Sale\Enums\InvoiceStatus;
use Webkul\Sale\Enums\OrderDeliveryStatus;
use Webkul\Sale\Enums\OrderState;
use Webkul\Sale\Facades\SaleOrder as SaleOrderFacade;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../inventories/tests/Helpers/InventoryHelper.php';
require_once __DIR__.'/../../Helpers/SaleHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('inventories');
    TestBootstrapHelper::ensurePluginInstalled('sales');

    foreach (['inventories', 'sales'] as $plugin) {
        Illuminate\Support\Facades\DB::table('plugins')->updateOrInsert(
            ['name' => $plugin],
            ['is_installed' => true, 'is_active' => true, 'updated_at' => now()],
        );
    }

    Webkul\PluginManager\Package::$plugins = Webkul\PluginManager\Models\Plugin::all()->keyBy('name');

    Illuminate\Support\Facades\URL::resolveMissingNamedRoutesUsing(fn () => '#');

    SaleHelper::actingAsAdmin();

    $this->warehouse = InventoryHelper::warehouse(ReceptionStep::ONE_STEP, DeliveryStep::ONE_STEP);
    $this->product = SaleHelper::product();
    $this->stock = $this->warehouse->lotStockLocation;
});

it('creates a single delivery linked to the order line when the sale order is confirmed', function () {
    $order = SaleHelper::confirmedOrder($this->warehouse, $this->product, 10);

    expect($order->state)->toBe(OrderState::SALE)
        ->and($order->operations)->toHaveCount(1);

    $line = $order->lines->first();
    $move = SaleHelper::customerDelivery($order)->moves->first();

    expect($move->sale_order_line_id)->toBe($line->id)
        ->and((float) $move->product_uom_qty)->toBe(10.0)
        ->and($move->source_location_id)->toBe($this->warehouse->lot_stock_location_id)
        ->and($move->destinationLocation->type)->toBe(LocationType::CUSTOMER);
});

it('removes the delivered quantity from stock when the delivery is validated', function () {
    $order = SaleHelper::confirmedOrder($this->warehouse, $this->product, 10);

    SaleHelper::deliverChain($order);

    expect(SaleHelper::customerDelivery($order)->refresh()->state)->toBe(OperationState::DONE)
        ->and(InventoryHelper::onHand($this->product, $this->stock))->toBe(0.0);
});

it('writes the delivered quantity back to the order line after the single delivery is validated', function () {
    $order = SaleHelper::confirmedOrder($this->warehouse, $this->product, 10);

    SaleHelper::deliverChain($order);

    expect((float) $order->refresh()->lines->first()->qty_delivered)->toBe(10.0);
});

it('reflects an increased ordered quantity on the delivery', function () {
    $order = SaleHelper::confirmedOrder($this->warehouse, $this->product, 10);

    SaleHelper::setLineQty($order->lines->first(), 15);

    $order->refresh();

    $totalMoveQty = $order->operations
        ->flatMap->moves
        ->filter(fn ($move) => $move->state !== MoveState::CANCELED)
        ->sum(fn ($move) => (float) $move->product_uom_qty);

    expect((float) $totalMoveQty)->toBe(15.0);
});

it('cancels the delivery when the sale order is cancelled', function () {
    $order = SaleHelper::confirmedOrder($this->warehouse, $this->product, 10);

    SaleOrderFacade::cancelSaleOrder($order->refresh());

    expect(SaleHelper::customerDelivery($order)->refresh()->state)->toBe(OperationState::CANCELED);
});

it('decreases the delivered quantity when a customer return is validated', function () {
    $order = SaleHelper::confirmedOrder($this->warehouse, $this->product, 10);

    SaleHelper::deliverChain($order);

    $delivery = SaleHelper::customerDelivery($order);
    $deliveryMove = $delivery->refresh()->moves->first();

    $return = Inventory::returnTransfer($delivery, [$deliveryMove->id => 4]);

    Inventory::doneTransfer($return->refresh());

    expect((float) $order->refresh()->lines->first()->qty_delivered)->toBe(6.0);
});

it('marks the delivery status pending after confirmation and full after validation', function () {
    $order = SaleHelper::confirmedOrder($this->warehouse, $this->product, 10);

    expect($order->refresh()->delivery_status)->toBe(OrderDeliveryStatus::PENDING);

    SaleHelper::deliverChain($order);

    expect($order->refresh()->delivery_status)->toBe(OrderDeliveryStatus::FULL);
});

it('marks the order to-invoice after delivery and invoiced after invoicing', function () {
    $order = SaleHelper::confirmedOrder($this->warehouse, $this->product, 10);

    SaleHelper::deliverChain($order);

    expect($order->refresh()->invoice_status)->toBe(InvoiceStatus::TO_INVOICE);

    SaleOrderFacade::createInvoice($order->refresh(), [
        'advance_payment_method' => AdvancedPayment::DELIVERED->value,
    ]);

    expect($order->refresh()->invoice_status)->toBe(InvoiceStatus::INVOICED);
});

it('reflects a decreased ordered quantity on the open delivery', function () {
    $order = SaleHelper::confirmedOrder($this->warehouse, $this->product, 10);

    SaleHelper::setLineQty($order->lines->first(), 6);

    $order->refresh()->load('operations.moves');

    $activeQty = $order->operations
        ->flatMap->moves
        ->filter(fn ($move) => $move->state !== MoveState::CANCELED)
        ->sum(fn ($move) => (float) $move->product_uom_qty);

    expect((float) $activeQty)->toBe(6.0);
});

it('cancels the delivery move when the ordered quantity is decreased to zero', function () {
    $order = SaleHelper::confirmedOrder($this->warehouse, $this->product, 10);

    SaleHelper::setLineQty($order->lines->first(), 0);

    $order->refresh()->load('operations.moves');

    $activeQty = $order->operations
        ->flatMap->moves
        ->filter(fn ($move) => $move->state !== MoveState::CANCELED)
        ->sum(fn ($move) => (float) $move->product_uom_qty);

    expect((float) $activeQty)->toBe(0.0);
});

it('creates a fresh delivery for the extra quantity after the first delivery is validated', function () {
    $order = SaleHelper::confirmedOrder($this->warehouse, $this->product, 10);

    SaleHelper::deliverChain($order);

    InventoryHelper::stockUp($this->product, $this->stock, 5);

    SaleHelper::setLineQty($order->refresh()->lines->first(), 15);

    $order->refresh()->load('operations.moves');

    $open = $order->operations->filter(fn ($op) => $op->state !== OperationState::DONE);

    expect($order->operations)->toHaveCount(2)
        ->and($open)->toHaveCount(1)
        ->and((float) $open->first()->moves->sum(fn ($m) => (float) $m->product_uom_qty))->toBe(5.0);
});

it('adds a move for a new order line appended to a confirmed sale order', function () {
    $order = SaleHelper::confirmedOrder($this->warehouse, $this->product, 10);

    $product2 = SaleHelper::product();
    InventoryHelper::stockUp($product2, $this->stock, 4);

    $line2 = SaleHelper::line($order->refresh(), $product2, 4, 100);

    $move = Move::where('sale_order_line_id', $line2->id)->first();

    expect($move)->not->toBeNull()
        ->and((float) $move->product_uom_qty)->toBe(4.0)
        ->and($order->refresh()->operations->pluck('id'))->toContain($move->operation_id);
});

it('backorders the undelivered remainder and marks delivery partial', function () {
    $order = SaleHelper::confirmedOrder($this->warehouse, $this->product, 10);

    SaleHelper::partialDeliver($order, 6);

    $order->refresh()->load('operations.moves');

    expect((float) $order->lines->first()->qty_delivered)->toBe(6.0)
        ->and($order->delivery_status)->toBe(OrderDeliveryStatus::PARTIAL);

    $backorder = $order->operations->first(fn ($op) => $op->state !== OperationState::DONE);

    expect($backorder)->not->toBeNull()
        ->and((float) $backorder->moves->sum(fn ($m) => (float) $m->product_uom_qty))->toBe(4.0);
});

it('keeps the delivered quantity when a customer return is physical only', function () {
    $order = SaleHelper::confirmedOrder($this->warehouse, $this->product, 10);

    SaleHelper::deliverChain($order);

    $delivery = SaleHelper::customerDelivery($order);
    $move = $delivery->refresh()->moves->first();

    $return = Inventory::returnTransfer($delivery, [
        $move->id => ['quantity' => 4, 'to_refund' => false],
    ]);

    Inventory::doneTransfer($return->refresh());

    expect((float) $order->refresh()->lines->first()->qty_delivered)->toBe(10.0);
});

it('links the customer return move back to the delivery move and order line', function () {
    $order = SaleHelper::confirmedOrder($this->warehouse, $this->product, 10);

    SaleHelper::deliverChain($order);

    $delivery = SaleHelper::customerDelivery($order);
    $move = $delivery->refresh()->moves->first();

    $return = Inventory::returnTransfer($delivery, [$move->id => 4]);

    $returnMove = $return->refresh()->moves->first();

    expect($returnMove->origin_returned_move_id)->toBe($move->id)
        ->and($returnMove->sale_order_line_id)->toBe($order->lines->first()->id)
        ->and($returnMove->destinationLocation->type)->toBe(LocationType::INTERNAL);
});

it('cancels the sale order cleanly after a delivery has been returned', function () {
    $order = SaleHelper::confirmedOrder($this->warehouse, $this->product, 10);

    SaleHelper::deliverChain($order);

    $delivery = SaleHelper::customerDelivery($order);
    $return = Inventory::returnTransfer($delivery, [$delivery->refresh()->moves->first()->id => 4]);

    Inventory::doneTransfer($return->refresh());

    SaleOrderFacade::cancelSaleOrder($order->refresh());

    expect($order->refresh()->state)->toBe(OrderState::CANCEL);
});

it('creates a second delivery and cancels the first when re-confirmed after cancellation', function () {
    $order = SaleHelper::confirmedOrder($this->warehouse, $this->product, 10);

    SaleOrderFacade::cancelSaleOrder($order->refresh());
    SaleOrderFacade::backToQuotation($order->refresh());
    SaleOrderFacade::confirmSaleOrder($order->refresh());

    $order->refresh()->load('operations');

    $active = $order->operations->filter(fn ($op) => $op->state !== OperationState::CANCELED);

    expect($order->operations->count())->toBeGreaterThanOrEqual(2)
        ->and($active)->toHaveCount(1);
});

it('invoices on the ordered quantity as soon as the order is confirmed under the order policy', function () {
    $product = SaleHelper::product(['invoice_policy' => 'order']);

    $order = SaleHelper::confirmedOrder($this->warehouse, $product, 10);

    $line = $order->refresh()->lines->first();

    expect($order->invoice_status)->toBe(InvoiceStatus::TO_INVOICE)
        ->and((float) $line->qty_to_invoice)->toBe(10.0);
});

it('withholds invoicing until delivery under the delivery policy', function () {
    $product = SaleHelper::product(['invoice_policy' => 'delivery']);

    $order = SaleHelper::confirmedOrder($this->warehouse, $product, 10);

    expect($order->refresh()->invoice_status)->toBe(InvoiceStatus::NO)
        ->and((float) $order->lines->first()->qty_to_invoice)->toBe(0.0);

    SaleHelper::deliverChain($order);

    $line = $order->refresh()->lines->first();

    expect($order->invoice_status)->toBe(InvoiceStatus::TO_INVOICE)
        ->and((float) $line->qty_to_invoice)->toBe(10.0);
});

it('invoices only the delivered quantity under the delivery policy', function () {
    $product = SaleHelper::product(['invoice_policy' => 'delivery']);

    $order = SaleHelper::confirmedOrder($this->warehouse, $product, 10);

    SaleHelper::partialDeliver($order, 6);

    $line = $order->refresh()->lines->first();

    expect((float) $line->qty_delivered)->toBe(6.0)
        ->and((float) $line->qty_to_invoice)->toBe(6.0);
});

it('flags up-selling when the delivered quantity exceeds the ordered quantity under the order policy', function () {
    $product = SaleHelper::product(['invoice_policy' => 'order']);

    InventoryHelper::stockUp($product, $this->stock, 2);

    $order = SaleHelper::confirmedOrder($this->warehouse, $product, 10);

    SaleHelper::partialDeliver($order, 12);

    expect((float) $order->refresh()->lines->first()->qty_delivered)->toBe(12.0)
        ->and($order->refresh()->invoice_status)->toBe(InvoiceStatus::UP_SELLING);
});

it('reserves and delivers a specific lot for a lot-tracked sale', function () {
    $product = SaleHelper::product(['tracking' => ProductTracking::LOT]);

    $lot = InventoryHelper::lot($product, 'LOT-A');

    InventoryHelper::stockUp($product, $this->stock, 10, $lot->id);

    $order = SaleHelper::order(['warehouse_id' => $this->warehouse->id]);
    SaleHelper::line($order, $product, 10, 100);
    $order = SaleOrderFacade::confirmSaleOrder($order->refresh())->load('operations.moves', 'lines');

    SaleHelper::deliverChain($order);

    $moveLine = SaleHelper::customerDelivery($order)->moves->first()->lines->first();

    expect((float) $order->refresh()->lines->first()->qty_delivered)->toBe(10.0)
        ->and($moveLine->lot_id)->toBe($lot->id)
        ->and(InventoryHelper::onHand($product, $this->stock))->toBe(0.0);
});
