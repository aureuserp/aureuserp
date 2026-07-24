<?php

use Webkul\Manufacturing\Enums\ManufacturingOrderState;

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

it('consumes components from stock and stores the finished product in stock', function () {
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

it('over-produces when more than the ordered quantity is produced', function () {
    $bom = ManufacturingHelper::bom($this->finished, [[$this->componentA, 2]]);

    InventoryHelper::stockUp($this->componentA, $this->stock, 20);

    $order = ManufacturingHelper::order($this->warehouse, $this->finished, $bom, 5);

    ManufacturingHelper::confirm($order);
    ManufacturingHelper::produce($order, 6);

    expect($order->refresh()->state)->toBe(ManufacturingOrderState::DONE)
        ->and((float) $order->quantity_produced)->toBe(6.0)
        ->and(InventoryHelper::onHand($this->finished, $this->stock))->toBe(6.0);
});

it('under-produces and closes the order for the produced quantity only', function () {
    $bom = ManufacturingHelper::bom($this->finished, [[$this->componentA, 2]]);

    InventoryHelper::stockUp($this->componentA, $this->stock, 10);

    $order = ManufacturingHelper::order($this->warehouse, $this->finished, $bom, 5);

    ManufacturingHelper::confirm($order);
    ManufacturingHelper::produce($order, 3);

    expect($order->refresh()->state)->toBe(ManufacturingOrderState::DONE)
        ->and((float) $order->quantity_produced)->toBe(3.0)
        ->and(InventoryHelper::onHand($this->finished, $this->stock))->toBe(3.0);
});
