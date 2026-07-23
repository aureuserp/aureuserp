<?php

use Illuminate\Support\Facades\URL;
use Webkul\Account\Enums\AmountType;
use Webkul\Account\Enums\TaxIncludeOverride;
use Webkul\Inventory\Enums\MoveState;
use Webkul\Inventory\Enums\OperationState;
use Webkul\Inventory\Enums\ProductTracking;
use Webkul\Inventory\Facades\Inventory;
use Webkul\Inventory\Models\Move;
use Webkul\Purchase\Enums\OrderInvoiceStatus;
use Webkul\Purchase\Enums\OrderReceiptStatus;
use Webkul\Purchase\Enums\OrderState;
use Webkul\Purchase\Enums\QtyReceivedMethod;
use Webkul\Purchase\Facades\PurchaseOrder as PurchaseOrderFacade;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../inventories/tests/Helpers/InventoryHelper.php';
require_once __DIR__.'/../../Helpers/PurchaseHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('inventories');
    TestBootstrapHelper::ensurePluginInstalled('purchases');

    foreach (['inventories', 'purchases'] as $plugin) {
        Illuminate\Support\Facades\DB::table('plugins')->updateOrInsert(
            ['name' => $plugin],
            ['is_installed' => true, 'is_active' => true, 'updated_at' => now()],
        );
    }

    Webkul\PluginManager\Package::$plugins = Webkul\PluginManager\Models\Plugin::all()->keyBy('name');

    URL::resolveMissingNamedRoutesUsing(fn () => '#');

    PurchaseHelper::actingAsAdmin();

    $this->warehouse = InventoryHelper::warehouse();
    $this->product = PurchaseHelper::product();
    $this->stock = $this->warehouse->lotStockLocation;
});

function confirmedPurchaseOrder($warehouse, $product, float $qty, float $price = 100)
{
    $order = PurchaseHelper::order(['operation_type_id' => $warehouse->in_type_id]);

    PurchaseHelper::line($order, $product, $qty, $price);

    return PurchaseOrderFacade::confirmPurchaseOrder($order->refresh())->load('operations', 'lines');
}

/*
|--------------------------------------------------------------------------
| Pricing: taxes, discount, rounding
|--------------------------------------------------------------------------
*/

it('computes a line subtotal from unit price and quantity with no tax or discount', function () {
    $order = PurchaseHelper::order();
    $line = PurchaseHelper::line($order, $this->product, qty: 2, priceUnit: 100);

    PurchaseHelper::compute($order);

    $line->refresh();

    expect((float) $line->price_subtotal)->toBe(200.0)
        ->and((float) $line->price_tax)->toBe(0.0)
        ->and((float) $line->price_total)->toBe(200.0);
});

it('adds an exclusive percent tax on top of the subtotal', function () {
    $order = PurchaseHelper::order();
    $tax = PurchaseHelper::tax(10, AmountType::PERCENT, TaxIncludeOverride::TAX_EXCLUDED);
    $line = PurchaseHelper::line($order, $this->product, qty: 2, priceUnit: 100, taxes: [$tax]);

    PurchaseHelper::compute($order);

    $line->refresh();

    expect((float) $line->price_subtotal)->toBe(200.0)
        ->and((float) $line->price_tax)->toBe(20.0)
        ->and((float) $line->price_total)->toBe(220.0);
});

it('strips an inclusive percent tax out of the unit price', function () {
    $order = PurchaseHelper::order();
    $tax = PurchaseHelper::tax(10, AmountType::PERCENT, TaxIncludeOverride::TAX_INCLUDED);
    $line = PurchaseHelper::line($order, $this->product, qty: 1, priceUnit: 110, taxes: [$tax]);

    PurchaseHelper::compute($order);

    $line->refresh();

    expect((float) $line->price_subtotal)->toBe(100.0)
        ->and((float) $line->price_tax)->toBe(10.0)
        ->and((float) $line->price_total)->toBe(110.0);
});

it('applies a percent discount to the subtotal before tax', function () {
    $order = PurchaseHelper::order();
    $line = PurchaseHelper::line($order, $this->product, qty: 2, priceUnit: 100, discount: 10);

    PurchaseHelper::compute($order);

    $line->refresh();

    expect((float) $line->price_subtotal)->toBe(180.0)
        ->and((float) $line->price_total)->toBe(180.0);
});

it('applies the discount before an exclusive tax', function () {
    $order = PurchaseHelper::order();
    $tax = PurchaseHelper::tax(15, AmountType::PERCENT, TaxIncludeOverride::TAX_EXCLUDED);
    $line = PurchaseHelper::line($order, $this->product, qty: 2, priceUnit: 100, discount: 10, taxes: [$tax]);

    PurchaseHelper::compute($order);

    $line->refresh();

    expect((float) $line->price_subtotal)->toBe(180.0)
        ->and((float) $line->price_tax)->toBe(27.0)
        ->and((float) $line->price_total)->toBe(207.0);
});

it('sums two independent exclusive percent taxes on the same base', function () {
    $order = PurchaseHelper::order();
    $ten = PurchaseHelper::tax(10, AmountType::PERCENT, TaxIncludeOverride::TAX_EXCLUDED);
    $five = PurchaseHelper::tax(5, AmountType::PERCENT, TaxIncludeOverride::TAX_EXCLUDED);
    $line = PurchaseHelper::line($order, $this->product, qty: 2, priceUnit: 100, taxes: [$ten, $five]);

    PurchaseHelper::compute($order);

    $line->refresh();

    expect((float) $line->price_subtotal)->toBe(200.0)
        ->and((float) $line->price_tax)->toBe(30.0)
        ->and((float) $line->price_total)->toBe(230.0);
});

it('multiplies a fixed tax by the quantity', function () {
    $order = PurchaseHelper::order();
    $tax = PurchaseHelper::tax(5, AmountType::FIXED, TaxIncludeOverride::TAX_EXCLUDED);
    $line = PurchaseHelper::line($order, $this->product, qty: 2, priceUnit: 100, taxes: [$tax]);

    PurchaseHelper::compute($order);

    $line->refresh();

    expect((float) $line->price_subtotal)->toBe(200.0)
        ->and((float) $line->price_tax)->toBe(10.0)
        ->and((float) $line->price_total)->toBe(210.0);
});

it('aggregates line totals into the order header amounts', function () {
    $order = PurchaseHelper::order();
    $tax = PurchaseHelper::tax(10, AmountType::PERCENT, TaxIncludeOverride::TAX_EXCLUDED);

    PurchaseHelper::line($order, $this->product, qty: 2, priceUnit: 100);
    PurchaseHelper::line($order, PurchaseHelper::product(), qty: 1, priceUnit: 50, taxes: [$tax]);

    PurchaseHelper::compute($order);

    $order->refresh();

    expect((float) $order->untaxed_amount)->toBe(250.0)
        ->and((float) $order->tax_amount)->toBe(5.0)
        ->and((float) $order->total_amount)->toBe(255.0);
});

it('keeps the line subtotal rounded to the stored precision', function () {
    $order = PurchaseHelper::order();
    $line = PurchaseHelper::line($order, $this->product, qty: 3, priceUnit: 33.33);

    PurchaseHelper::compute($order);

    $line->refresh();

    expect((float) $line->price_subtotal)->toBe(99.99);
});

it('cascades a base-affected tax on top of a base-including tax', function () {
    $order = PurchaseHelper::order();

    $base = PurchaseHelper::tax(10, AmountType::PERCENT, TaxIncludeOverride::TAX_EXCLUDED, includeBaseAmount: true, sort: 1);
    $onTop = PurchaseHelper::tax(5, AmountType::PERCENT, TaxIncludeOverride::TAX_EXCLUDED, isBaseAffected: true, sort: 2);

    $line = PurchaseHelper::line($order, $this->product, qty: 2, priceUnit: 100, taxes: [$base, $onTop]);

    PurchaseHelper::compute($order);

    $line->refresh();

    expect((float) $line->price_subtotal)->toBe(200.0)
        ->and((float) $line->price_tax)->toBe(31.0)
        ->and((float) $line->price_total)->toBe(231.0);
});

it('treats a default-include tax as exclusive because the tax setting defaults to excluded', function () {
    $order = PurchaseHelper::order();

    $tax = PurchaseHelper::tax(10, AmountType::PERCENT, TaxIncludeOverride::DEFAULT);

    $line = PurchaseHelper::line($order, $this->product, qty: 2, priceUnit: 100, taxes: [$tax]);

    PurchaseHelper::compute($order);

    $line->refresh();

    expect((float) $line->price_subtotal)->toBe(200.0)
        ->and((float) $line->price_tax)->toBe(20.0)
        ->and((float) $line->price_total)->toBe(220.0);
});

/*
|--------------------------------------------------------------------------
| Inventory integration: receipt lifecycle
|--------------------------------------------------------------------------
*/

it('creates a receipt linked to the order line when the purchase order is confirmed', function () {
    $order = confirmedPurchaseOrder($this->warehouse, $this->product, 10);

    expect($order->state)->toBe(OrderState::PURCHASE)
        ->and($order->operations)->toHaveCount(1);

    $line = $order->lines->first();
    $move = $order->operations->first()->moves->first();

    expect($move->purchase_order_line_id)->toBe($line->id)
        ->and((float) $move->product_uom_qty)->toBe(10.0)
        ->and($move->destination_location_id)->toBe($this->warehouse->lot_stock_location_id);
});

it('lands the ordered quantity in stock when the receipt is validated', function () {
    $order = confirmedPurchaseOrder($this->warehouse, $this->product, 10);

    $receipt = $order->operations->first();

    Inventory::doneTransfer($receipt->refresh());

    expect($receipt->refresh()->state)->toBe(OperationState::DONE)
        ->and(InventoryHelper::onHand($this->product, $this->stock))->toBe(10.0);
});

it('writes the received quantity back to the order line when the receipt is validated', function () {
    $order = confirmedPurchaseOrder($this->warehouse, $this->product, 10);

    Inventory::doneTransfer($order->operations->first()->refresh());

    $line = $order->refresh()->lines->first();

    expect((float) $line->qty_received)->toBe(10.0);
});

it('pushes only the extra quantity onto the receipt when the ordered quantity is increased', function () {
    $order = confirmedPurchaseOrder($this->warehouse, $this->product, 10);

    $order->lines->first()->update(['product_qty' => 15]);

    $order->refresh();

    $totalMoveQty = $order->operations
        ->flatMap->moves
        ->filter(fn ($move) => $move->state !== MoveState::CANCELED)
        ->sum(fn ($move) => (float) $move->product_uom_qty);

    expect((float) $totalMoveQty)->toBe(15.0);
});

it('creates a fresh receipt for the extra quantity after the first receipt is validated', function () {
    $order = confirmedPurchaseOrder($this->warehouse, $this->product, 10);

    Inventory::doneTransfer($order->operations->first()->refresh());

    $order->refresh()->lines->first()->update(['product_qty' => 15]);

    $order->refresh();

    $openReceipts = $order->operations->filter(fn ($op) => $op->state !== OperationState::DONE);

    expect($openReceipts)->toHaveCount(1)
        ->and((float) $openReceipts->first()->moves->sum(fn ($m) => (float) $m->product_uom_qty))->toBe(5.0);
});

it('refuses to decrease the ordered quantity below the received quantity', function () {
    $order = confirmedPurchaseOrder($this->warehouse, $this->product, 10);

    Inventory::doneTransfer($order->operations->first()->refresh());

    $line = $order->refresh()->lines->first();

    expect(fn () => $line->update(['product_qty' => 4]))
        ->toThrow(Exception::class, 'cannot decrease the ordered quantity below the received');
});

it('cancels the receipt when the purchase order is cancelled', function () {
    $order = confirmedPurchaseOrder($this->warehouse, $this->product, 10);

    PurchaseOrderFacade::cancelPurchaseOrder($order->refresh());

    expect($order->refresh()->operations->first()->refresh()->state)->toBe(OperationState::CANCELED);
});

it('decreases the received quantity when a purchase return refunds by default', function () {
    $order = confirmedPurchaseOrder($this->warehouse, $this->product, 10);

    Inventory::doneTransfer($order->operations->first()->refresh());

    $receipt = $order->refresh()->operations->first();
    $receiptMove = $receipt->moves->first();

    $return = Inventory::returnTransfer($receipt, [$receiptMove->id => 4]);

    Inventory::doneTransfer($return->refresh());

    $line = $order->refresh()->lines->first();

    expect((float) $line->qty_received)->toBe(6.0)
        ->and(InventoryHelper::onHand($this->product, $this->stock))->toBe(6.0);
});

it('keeps the received quantity when a purchase return is physical only', function () {
    $order = confirmedPurchaseOrder($this->warehouse, $this->product, 10);

    Inventory::doneTransfer($order->operations->first()->refresh());

    $receipt = $order->refresh()->operations->first();
    $receiptMove = $receipt->moves->first();

    $return = Inventory::returnTransfer($receipt, [
        $receiptMove->id => ['quantity' => 4, 'to_refund' => false],
    ]);

    Inventory::doneTransfer($return->refresh());

    $line = $order->refresh()->lines->first();

    expect((float) $line->qty_received)->toBe(10.0)
        ->and(InventoryHelper::onHand($this->product, $this->stock))->toBe(6.0);
});

it('links the purchase return move back to the original receipt move and order line', function () {
    $order = confirmedPurchaseOrder($this->warehouse, $this->product, 10);

    Inventory::doneTransfer($order->operations->first()->refresh());

    $receipt = $order->refresh()->operations->first();
    $receiptMove = $receipt->moves->first();

    $return = Inventory::returnTransfer($receipt, [$receiptMove->id => 4]);

    $returnMove = $return->refresh()->moves->first();

    expect($returnMove->origin_returned_move_id)->toBe($receiptMove->id)
        ->and($returnMove->purchase_order_line_id)->toBe($order->lines->first()->id)
        ->and($returnMove->isPurchaseReturn())->toBeTrue();
});

/*
|--------------------------------------------------------------------------
| Received-quantity method + receipt status
|--------------------------------------------------------------------------
*/

it('uses the manually entered received quantity when the line method is manual', function () {
    $order = PurchaseHelper::order();
    $line = PurchaseHelper::line($order, $this->product, qty: 10, priceUnit: 100);

    $line->update([
        'qty_received_method' => QtyReceivedMethod::MANUAL,
        'qty_received_manual' => 6,
    ]);

    PurchaseHelper::compute($order);

    expect((float) $line->refresh()->qty_received)->toBe(6.0);
});

it('computes the quantity to invoice as received minus invoiced', function () {
    $order = PurchaseHelper::order();
    $line = PurchaseHelper::line($order, $this->product, qty: 10, priceUnit: 100);

    $line->update([
        'qty_received_method' => QtyReceivedMethod::MANUAL,
        'qty_received_manual' => 6,
    ]);

    PurchaseHelper::compute($order);

    expect((float) $line->refresh()->qty_to_invoice)->toBe(6.0);
});

it('marks the receipt status pending after confirmation and full after validation', function () {
    $order = confirmedPurchaseOrder($this->warehouse, $this->product, 10);

    expect($order->refresh()->receipt_status)->toBe(OrderReceiptStatus::PENDING);

    Inventory::doneTransfer($order->operations->first()->refresh());

    expect($order->refresh()->receipt_status)->toBe(OrderReceiptStatus::FULL);
});

it('marks the receipt status partial when only some receipts are done', function () {
    $order = confirmedPurchaseOrder($this->warehouse, $this->product, 10);

    Inventory::doneTransfer($order->operations->first()->refresh());

    $order->refresh()->lines->first()->update(['product_qty' => 15]);

    PurchaseHelper::compute($order);

    expect($order->refresh()->receipt_status)->toBe(OrderReceiptStatus::PARTIAL);
});

/*
|--------------------------------------------------------------------------
| Billing: qty_invoiced + invoice status
|--------------------------------------------------------------------------
*/

it('reports no invoice status while the order is still a draft', function () {
    $order = PurchaseHelper::order();
    PurchaseHelper::line($order, $this->product, qty: 10, priceUnit: 100);

    PurchaseHelper::compute($order);

    expect($order->refresh()->invoice_status)->toBe(OrderInvoiceStatus::NO);
});

it('marks the order to-invoice after receipt and invoiced after billing', function () {
    $order = confirmedPurchaseOrder($this->warehouse, $this->product, 10);

    Inventory::doneTransfer($order->operations->first()->refresh());

    expect($order->refresh()->invoice_status)->toBe(OrderInvoiceStatus::TO_INVOICED);

    PurchaseOrderFacade::createPurchaseOrderBill($order->refresh());

    $order->refresh();
    $line = $order->lines->first();

    expect($order->invoice_status)->toBe(OrderInvoiceStatus::INVOICED)
        ->and((float) $line->qty_invoiced)->toBe(10.0)
        ->and((float) $line->qty_to_invoice)->toBe(0.0);
});

it('links the created bill to the order and its lines to the order lines', function () {
    $order = confirmedPurchaseOrder($this->warehouse, $this->product, 10);

    Inventory::doneTransfer($order->operations->first()->refresh());

    PurchaseOrderFacade::createPurchaseOrderBill($order->refresh());

    $order->refresh();
    $line = $order->lines->first();

    expect($order->accountMoves)->toHaveCount(1)
        ->and($line->accountMoveLines)->toHaveCount(1)
        ->and((float) $line->accountMoveLines->first()->quantity)->toBe(10.0);
});

it('converts the company-currency total using the order currency rate', function () {
    $order = PurchaseHelper::order(['currency_rate' => 2.0]);
    PurchaseHelper::line($order, $this->product, qty: 2, priceUnit: 100);

    PurchaseHelper::compute($order);

    $order->refresh();

    expect((float) $order->total_amount)->toBe(200.0)
        ->and((float) $order->total_cc_amount)->toBe(100.0);
});

it('backorders the unreceived remainder and marks the receipt partial', function () {
    $order = confirmedPurchaseOrder($this->warehouse, $this->product, 10);

    PurchaseHelper::partialReceive($order, 6);

    $order->refresh()->load('operations.moves');

    expect((float) $order->lines->first()->qty_received)->toBe(6.0)
        ->and($order->refresh()->receipt_status)->toBe(OrderReceiptStatus::PARTIAL);

    $backorder = $order->operations->first(fn ($op) => $op->state !== OperationState::DONE);

    expect($backorder)->not->toBeNull()
        ->and((float) $backorder->moves->sum(fn ($m) => (float) $m->product_uom_qty))->toBe(4.0);
});

it('merges the change into a single open move when the quantity is decreased before receipt', function () {
    $order = confirmedPurchaseOrder($this->warehouse, $this->product, 10);

    $order->lines->first()->update(['product_qty' => 7]);

    $order->refresh()->load('operations.moves');

    $openMoves = $order->operations
        ->flatMap->moves
        ->filter(fn ($move) => $move->state !== MoveState::CANCELED);

    expect($openMoves)->toHaveCount(1)
        ->and((float) $openMoves->first()->product_uom_qty)->toBe(7.0);
});

it('adds a move for a new order line appended to a confirmed purchase order', function () {
    $order = confirmedPurchaseOrder($this->warehouse, $this->product, 10);

    $product2 = PurchaseHelper::product();

    $line2 = PurchaseHelper::line($order->refresh(), $product2, qty: 5, priceUnit: 100);

    $move = Move::where('purchase_order_line_id', $line2->id)->first();

    expect($move)->not->toBeNull()
        ->and((float) $move->product_uom_qty)->toBe(5.0)
        ->and($order->refresh()->operations->pluck('id'))->toContain($move->operation_id);
});

it('handles a quantity change after a receipt has been returned', function () {
    $order = confirmedPurchaseOrder($this->warehouse, $this->product, 10);

    Inventory::doneTransfer($order->operations->first()->refresh());

    $receipt = $order->refresh()->operations->first();
    $return = Inventory::returnTransfer($receipt, [$receipt->moves->first()->id => 4]);

    Inventory::doneTransfer($return->refresh());

    $order->refresh()->lines->first()->update(['product_qty' => 12]);

    expect((float) $order->refresh()->lines->first()->qty_received)->toBe(6.0);
});

it('creates the lot named on the receipt move when a lot-tracked purchase is validated', function () {
    InventoryHelper::trackLots($this->warehouse->inType);

    $product = PurchaseHelper::product(['tracking' => ProductTracking::LOT]);

    $order = confirmedPurchaseOrder($this->warehouse, $product, 10);

    InventoryHelper::nameLines($order->operations->first()->refresh()->moves->first(), ['LOT-A']);

    Inventory::doneTransfer($order->operations->first()->refresh());

    $moveLine = $order->operations->first()->refresh()->moves->first()->lines->first();

    expect((float) $order->refresh()->lines->first()->qty_received)->toBe(10.0)
        ->and($moveLine->lot_id)->not->toBeNull()
        ->and(InventoryHelper::onHand($product, $this->stock))->toBe(10.0);
});

it('assigns one serial number per unit on a serial-tracked receipt', function () {
    InventoryHelper::trackLots($this->warehouse->inType);

    $product = PurchaseHelper::product(['tracking' => ProductTracking::SERIAL]);

    $order = confirmedPurchaseOrder($this->warehouse, $product, 3);

    InventoryHelper::nameLines($order->operations->first()->refresh()->moves->first(), ['SN-1', 'SN-2', 'SN-3']);

    Inventory::doneTransfer($order->operations->first()->refresh());

    expect((float) $order->refresh()->lines->first()->qty_received)->toBe(3.0)
        ->and(InventoryHelper::lotsOf($product))->toBe(['SN-1', 'SN-2', 'SN-3'])
        ->and(InventoryHelper::onHand($product, $this->stock))->toBe(3.0);
});
