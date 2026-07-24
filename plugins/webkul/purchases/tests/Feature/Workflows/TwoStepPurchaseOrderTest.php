<?php

use Webkul\Inventory\Enums\DeliveryStep;
use Webkul\Inventory\Enums\LocationType;
use Webkul\Inventory\Enums\MoveState;
use Webkul\Inventory\Enums\OperationState;
use Webkul\Inventory\Enums\ReceptionStep;
use Webkul\Inventory\Facades\Inventory;
use Webkul\Purchase\Enums\OrderReceiptStatus;
use Webkul\Purchase\Enums\OrderState;

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

    Illuminate\Support\Facades\URL::resolveMissingNamedRoutesUsing(fn () => '#');

    PurchaseHelper::actingAsAdmin();

    $this->warehouse = InventoryHelper::warehouse(ReceptionStep::TWO_STEPS, DeliveryStep::ONE_STEP);
    $this->product = PurchaseHelper::product();
    $this->stock = $this->warehouse->lotStockLocation;
    $this->input = $this->warehouse->inputStockLocation;
});

it('links only the vendor receipt to the purchase order and routes it into the input zone', function () {
    $order = PurchaseHelper::confirmedOrder($this->warehouse, $this->product, 10);

    expect($order->state)->toBe(OrderState::PURCHASE)
        ->and($order->operations)->toHaveCount(1);

    $move = PurchaseHelper::vendorReceipt($order)->moves->first();

    expect($move->purchase_order_line_id)->toBe($order->lines->first()->id)
        ->and($move->sourceLocation->type)->toBe(LocationType::SUPPLIER)
        ->and($move->destination_location_id)->toBe($this->warehouse->input_stock_location_id);
});

it('marks the receipt full and writes the received quantity when the vendor receipt is validated', function () {
    $order = PurchaseHelper::confirmedOrder($this->warehouse, $this->product, 10);

    Inventory::doneTransfer(PurchaseHelper::vendorReceipt($order)->refresh());

    expect((float) $order->refresh()->lines->first()->qty_received)->toBe(10.0)
        ->and($order->refresh()->receipt_status)->toBe(OrderReceiptStatus::FULL);
});

it('merges a decreased quantity into a single open receipt move', function () {
    $order = PurchaseHelper::confirmedOrder($this->warehouse, $this->product, 10);

    $order->lines->first()->update(['product_qty' => 7]);

    $order->refresh()->load('operations.moves');

    $openMoves = $order->operations
        ->flatMap->moves
        ->filter(fn ($move) => $move->state !== MoveState::CANCELED);

    expect($openMoves)->toHaveCount(1)
        ->and((float) $openMoves->first()->product_uom_qty)->toBe(7.0);
});

it('creates a fresh receipt for the extra quantity after the first receipt is validated', function () {
    $order = PurchaseHelper::confirmedOrder($this->warehouse, $this->product, 10);

    Inventory::doneTransfer(PurchaseHelper::vendorReceipt($order)->refresh());

    $order->refresh()->lines->first()->update(['product_qty' => 15]);

    $order->refresh()->load('operations.moves');

    $open = $order->operations->filter(fn ($op) => $op->state !== OperationState::DONE);

    expect($open)->toHaveCount(1)
        ->and((float) $open->first()->moves->sum(fn ($m) => (float) $m->product_uom_qty))->toBe(5.0);
});

it('backorders the unreceived remainder in the two-step receipt', function () {
    $order = PurchaseHelper::confirmedOrder($this->warehouse, $this->product, 10);

    PurchaseHelper::partialReceive($order, 6);

    $order->refresh()->load('operations.moves');

    expect((float) $order->lines->first()->qty_received)->toBe(6.0);

    $backorder = $order->operations->first(fn ($op) => $op->state !== OperationState::DONE);

    expect($backorder)->not->toBeNull()
        ->and((float) $backorder->moves->sum(fn ($m) => (float) $m->product_uom_qty))->toBe(4.0);
});
