<?php

use Webkul\Inventory\Enums\DeliveryStep;
use Webkul\Inventory\Enums\LocationType;
use Webkul\Inventory\Enums\MoveState;
use Webkul\Inventory\Enums\OperationState;
use Webkul\Inventory\Enums\ReceptionStep;
use Webkul\Sale\Enums\OrderDeliveryStatus;
use Webkul\Sale\Enums\OrderState;

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

    $this->warehouse = InventoryHelper::warehouse(ReceptionStep::ONE_STEP, DeliveryStep::THREE_STEPS);
    $this->product = SaleHelper::product();
    $this->stock = $this->warehouse->lotStockLocation;
});

it('creates the pick leg from stock to packing linked to the order line on confirm', function () {
    $order = SaleHelper::confirmedOrder($this->warehouse, $this->product, 10);

    expect($order->state)->toBe(OrderState::SALE);

    $pick = $order->operations->first();
    $move = $pick->moves->first();

    expect($move->sale_order_line_id)->toBe($order->lines->first()->id)
        ->and($move->source_location_id)->toBe($this->warehouse->lot_stock_location_id)
        ->and($move->destination_location_id)->toBe($this->warehouse->pack_stock_location_id);
});

it('does not update delivered quantity after only the pick and pack legs are validated', function () {
    $order = SaleHelper::confirmedOrder($this->warehouse, $this->product, 10);

    SaleHelper::deliverNextLeg($order);
    SaleHelper::deliverNextLeg($order);

    expect((float) $order->refresh()->lines->first()->qty_delivered)->toBe(0.0)
        ->and($order->refresh()->delivery_status)->toBe(OrderDeliveryStatus::STARTED);
});

it('links all three legs to the sale order once the chain completes', function () {
    $order = SaleHelper::confirmedOrder($this->warehouse, $this->product, 10);

    SaleHelper::deliverChain($order);

    $order->refresh()->load('operations.moves');

    expect($order->operations->filter(fn ($op) => $op->state === OperationState::DONE))->toHaveCount(3)
        ->and(SaleHelper::customerDelivery($order)->moves->first()->destinationLocation->type)->toBe(LocationType::CUSTOMER);
});

it('updates delivered quantity and empties stock only after the full three-step chain', function () {
    $order = SaleHelper::confirmedOrder($this->warehouse, $this->product, 10);

    SaleHelper::deliverChain($order);

    expect((float) $order->refresh()->lines->first()->qty_delivered)->toBe(10.0)
        ->and($order->refresh()->delivery_status)->toBe(OrderDeliveryStatus::FULL)
        ->and(InventoryHelper::onHand($this->product, $this->stock))->toBe(0.0);
});

it('propagates a decreased ordered quantity to the pick leg before validation', function () {
    $order = SaleHelper::confirmedOrder($this->warehouse, $this->product, 10);

    expect((float) $order->operations->first()->moves->first()->product_uom_qty)->toBe(10.0);

    SaleHelper::setLineQty($order->lines->first(), 6);

    $order->refresh()->load('operations.moves');

    $activeQty = $order->operations
        ->flatMap->moves
        ->filter(fn ($move) => $move->state !== MoveState::CANCELED)
        ->sum(fn ($move) => (float) $move->product_uom_qty);

    expect((float) $activeQty)->toBe(6.0);
});
