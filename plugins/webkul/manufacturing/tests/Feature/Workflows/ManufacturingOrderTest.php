<?php

use Webkul\Inventory\Enums\MoveState;
use Webkul\Inventory\Enums\ProductTracking;
use Webkul\Manufacturing\Enums\BillOfMaterialConsumption;
use Webkul\Manufacturing\Enums\ManufacturingOrderState;
use Webkul\Manufacturing\Facades\Manufacturing;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../inventories/tests/Helpers/InventoryHelper.php';
require_once __DIR__.'/../../Helpers/ManufacturingHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('inventories');
    TestBootstrapHelper::ensurePluginInstalled('manufacturing');

    foreach (['inventories', 'manufacturing'] as $plugin) {
        Illuminate\Support\Facades\DB::table('plugins')->updateOrInsert(
            ['name' => $plugin],
            ['is_installed' => true, 'is_active' => true, 'updated_at' => now()],
        );
    }

    Webkul\PluginManager\Package::$plugins = Webkul\PluginManager\Models\Plugin::all()->keyBy('name');

    Illuminate\Support\Facades\URL::resolveMissingNamedRoutesUsing(fn () => '#');

    ManufacturingHelper::actingAsAdmin();

    $this->warehouse = InventoryHelper::warehouse();
    $this->stock = $this->warehouse->lotStockLocation;

    $this->finished = ManufacturingHelper::product();
    $this->componentA = ManufacturingHelper::product();
    $this->componentB = ManufacturingHelper::product();
});

it('explodes the bill of materials into raw and finished moves', function () {
    $bom = ManufacturingHelper::bom($this->finished, [[$this->componentA, 2], [$this->componentB, 3]]);

    $order = ManufacturingHelper::order($this->warehouse, $this->finished, $bom, 5);

    expect($order->state)->toBe(ManufacturingOrderState::DRAFT)
        ->and($order->rawMaterialMoves)->toHaveCount(2)
        ->and($order->finishedMoves)->toHaveCount(1);

    $rawA = $order->rawMaterialMoves->first(fn ($m) => $m->product_id === $this->componentA->id);
    $rawB = $order->rawMaterialMoves->first(fn ($m) => $m->product_id === $this->componentB->id);

    expect((float) $rawA->product_uom_qty)->toBe(10.0)
        ->and((float) $rawB->product_uom_qty)->toBe(15.0)
        ->and((float) $order->finishedMoves->first()->product_uom_qty)->toBe(5.0);
});

it('confirms the manufacturing order and its component and finished moves', function () {
    $bom = ManufacturingHelper::bom($this->finished, [[$this->componentA, 2]]);

    $order = ManufacturingHelper::order($this->warehouse, $this->finished, $bom, 5);

    ManufacturingHelper::confirm($order);

    expect($order->refresh()->state)->toBe(ManufacturingOrderState::CONFIRMED);
});

it('consumes components and produces the finished product when the order is marked done', function () {
    $bom = ManufacturingHelper::bom($this->finished, [[$this->componentA, 2], [$this->componentB, 3]]);

    InventoryHelper::stockUp($this->componentA, $this->stock, 10);
    InventoryHelper::stockUp($this->componentB, $this->stock, 15);

    $order = ManufacturingHelper::order($this->warehouse, $this->finished, $bom, 5);

    ManufacturingHelper::confirm($order);

    ManufacturingHelper::produce($order, 5);

    expect($order->refresh()->state)->toBe(ManufacturingOrderState::DONE)
        ->and((float) $order->quantity_produced)->toBe(5.0)
        ->and(InventoryHelper::onHand($this->finished, $this->stock))->toBe(5.0)
        ->and(InventoryHelper::onHand($this->componentA, $this->stock))->toBe(0.0)
        ->and(InventoryHelper::onHand($this->componentB, $this->stock))->toBe(0.0);
});

it('builds a manufacturing order with manually added components and no bill of materials', function () {
    InventoryHelper::stockUp($this->componentA, $this->stock, 4);

    $order = ManufacturingHelper::order($this->warehouse, $this->finished, null, 2);

    ManufacturingHelper::addComponent($order, $this->componentA, 4);

    expect($order->refresh()->rawMaterialMoves)->toHaveCount(1);

    ManufacturingHelper::confirm($order);

    ManufacturingHelper::produce($order, 2);

    expect($order->refresh()->state)->toBe(ManufacturingOrderState::DONE)
        ->and((float) $order->quantity_produced)->toBe(2.0)
        ->and(InventoryHelper::onHand($this->finished, $this->stock))->toBe(2.0)
        ->and(InventoryHelper::onHand($this->componentA, $this->stock))->toBe(0.0);
});

it('includes an extra manually added component alongside the bill of materials', function () {
    $bom = ManufacturingHelper::bom($this->finished, [[$this->componentA, 2]]);

    $extra = ManufacturingHelper::product();

    InventoryHelper::stockUp($this->componentA, $this->stock, 10);
    InventoryHelper::stockUp($extra, $this->stock, 3);

    $order = ManufacturingHelper::order($this->warehouse, $this->finished, $bom, 5);

    ManufacturingHelper::addComponent($order, $extra, 3);

    expect($order->refresh()->rawMaterialMoves)->toHaveCount(2);

    ManufacturingHelper::confirm($order);
    ManufacturingHelper::produce($order, 5);

    expect($order->refresh()->state)->toBe(ManufacturingOrderState::DONE)
        ->and(InventoryHelper::onHand($this->componentA, $this->stock))->toBe(0.0)
        ->and(InventoryHelper::onHand($extra, $this->stock))->toBe(0.0);
});

it('flags a consumption issue when a strict bill of materials is under-consumed', function () {
    $bom = ManufacturingHelper::bom($this->finished, [[$this->componentA, 2]], ['consumption' => BillOfMaterialConsumption::STRICT]);

    $order = ManufacturingHelper::order($this->warehouse, $this->finished, $bom, 5);

    $order->update(['quantity_producing' => 5]);

    $order->refresh()->rawMaterialMoves->first()->update(['quantity' => 8, 'is_picked' => true]);

    expect($order->refresh()->getConsumptionIssues())->not->toBeEmpty();
});

it('flags a consumption issue when a strict bill of materials is over-consumed', function () {
    $bom = ManufacturingHelper::bom($this->finished, [[$this->componentA, 2]], ['consumption' => BillOfMaterialConsumption::STRICT]);

    $order = ManufacturingHelper::order($this->warehouse, $this->finished, $bom, 5);

    $order->update(['quantity_producing' => 5]);

    $order->refresh()->rawMaterialMoves->first()->update(['quantity' => 12, 'is_picked' => true]);

    expect($order->refresh()->getConsumptionIssues())->not->toBeEmpty();
});

it('raises no consumption issue for a flexible bill of materials', function () {
    $bom = ManufacturingHelper::bom($this->finished, [[$this->componentA, 2]], ['consumption' => BillOfMaterialConsumption::FLEXIBLE]);

    $order = ManufacturingHelper::order($this->warehouse, $this->finished, $bom, 5);

    $order->update(['quantity_producing' => 5]);

    $order->refresh()->rawMaterialMoves->first()->update(['quantity' => 8, 'is_picked' => true]);

    expect($order->refresh()->getConsumptionIssues())->toBeEmpty();
});

it('cancels a manufacturing order and its moves', function () {
    $bom = ManufacturingHelper::bom($this->finished, [[$this->componentA, 2]]);

    $order = ManufacturingHelper::order($this->warehouse, $this->finished, $bom, 5);

    ManufacturingHelper::confirm($order);

    Manufacturing::cancelManufacturingOrder($order->refresh());

    expect($order->refresh()->state)->toBe(ManufacturingOrderState::CANCEL);
});

it('refuses to unplan a manufacturing order whose work order has already started', function () {
    $bom = ManufacturingHelper::bom($this->finished, [[$this->componentA, 2]]);
    $workCenter = ManufacturingHelper::workCenter();
    $operation = ManufacturingHelper::operation($bom, $workCenter);

    InventoryHelper::stockUp($this->componentA, $this->stock, 10);

    $order = ManufacturingHelper::order($this->warehouse, $this->finished, $bom, 5);
    $workOrder = ManufacturingHelper::workOrder($order, $workCenter, $operation);

    ManufacturingHelper::confirm($order);
    ManufacturingHelper::startWorkOrder($workOrder);

    expect(fn () => Manufacturing::unplanManufacturingOrder($order->refresh()))
        ->toThrow(Exception::class, __('manufacturing::system.manufacturing-manager.unplan-order.work-orders-already-started'));
});

it('rescales the finished move when the manufacturing quantity is changed', function () {
    $bom = ManufacturingHelper::bom($this->finished, [[$this->componentA, 2]]);

    $order = ManufacturingHelper::order($this->warehouse, $this->finished, $bom, 5);

    expect((float) $order->finishedMoves->first()->product_uom_qty)->toBe(5.0);

    $order->update(['quantity' => 10]);

    $order->refresh()->load('finishedMoves');

    expect((float) $order->finishedMoves->first()->product_uom_qty)->toBe(10.0);
});

it('adds a raw material move for a component appended to a draft manufacturing order', function () {
    $bom = ManufacturingHelper::bom($this->finished, [[$this->componentA, 2]]);

    $extra = ManufacturingHelper::product();

    $order = ManufacturingHelper::order($this->warehouse, $this->finished, $bom, 5);

    expect($order->rawMaterialMoves)->toHaveCount(1);

    ManufacturingHelper::addComponent($order, $extra, 3);

    expect($order->refresh()->rawMaterialMoves)->toHaveCount(2)
        ->and($order->rawMaterialMoves->firstWhere('product_id', $extra->id))->not->toBeNull();
});

it('confirms and consumes a component added to an already confirmed manufacturing order', function () {
    $bom = ManufacturingHelper::bom($this->finished, [[$this->componentA, 2]]);

    InventoryHelper::stockUp($this->componentA, $this->stock, 10);

    $extra = ManufacturingHelper::product();
    InventoryHelper::stockUp($extra, $this->stock, 3);

    $order = ManufacturingHelper::order($this->warehouse, $this->finished, $bom, 5);

    ManufacturingHelper::confirm($order);

    $move = ManufacturingHelper::addComponent($order, $extra, 3);

    expect($move->refresh()->state)->not->toBe(MoveState::DRAFT)
        ->and($order->refresh()->rawMaterialMoves)->toHaveCount(2);

    ManufacturingHelper::produce($order, 5);

    expect($order->refresh()->state)->toBe(ManufacturingOrderState::DONE)
        ->and(InventoryHelper::onHand($this->finished, $this->stock))->toBe(5.0)
        ->and(InventoryHelper::onHand($this->componentA, $this->stock))->toBe(0.0)
        ->and(InventoryHelper::onHand($extra, $this->stock))->toBe(0.0);
});

it('produces a declared by-product into stock alongside the finished product', function () {
    $byproductProduct = ManufacturingHelper::product();

    $bom = ManufacturingHelper::bom($this->finished, [[$this->componentA, 2]]);
    ManufacturingHelper::byproduct($bom, $byproductProduct, 1);

    InventoryHelper::stockUp($this->componentA, $this->stock, 10);

    $order = ManufacturingHelper::order($this->warehouse, $this->finished, $bom, 5);

    ManufacturingHelper::confirm($order);
    ManufacturingHelper::produce($order, 5);

    expect($order->refresh()->state)->toBe(ManufacturingOrderState::DONE)
        ->and(InventoryHelper::onHand($this->finished, $this->stock))->toBe(5.0)
        ->and(InventoryHelper::onHand($byproductProduct, $this->stock))->toBe(5.0);
});

it('records the producing lot on a lot-tracked finished product', function () {
    $tracked = ManufacturingHelper::product(['tracking' => ProductTracking::LOT]);

    $bom = ManufacturingHelper::bom($tracked, [[$this->componentA, 2]]);

    InventoryHelper::stockUp($this->componentA, $this->stock, 10);

    $order = ManufacturingHelper::order($this->warehouse, $tracked, $bom, 5);

    $lot = InventoryHelper::lot($tracked, 'FIN-LOT');
    $order->update(['producing_lot_id' => $lot->id]);

    ManufacturingHelper::confirm($order);
    ManufacturingHelper::produce($order, 5);

    expect($order->refresh()->state)->toBe(ManufacturingOrderState::DONE)
        ->and((float) InventoryHelper::onHand($tracked, $this->stock))->toBe(5.0)
        ->and(InventoryHelper::quantOf($tracked, $this->stock, $lot->id))->not->toBeNull();
});
