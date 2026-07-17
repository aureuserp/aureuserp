<?php

use Webkul\Inventory\Enums\ManufactureStep;
use Webkul\Inventory\Models\Location;
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

    $this->warehouse = ManufacturingHelper::multiStepWarehouse(ManufactureStep::TWO_STEPS);
    $this->stock = $this->warehouse->lotStockLocation;
    $this->preProduction = Location::findOrFail($this->warehouse->pbm_loc_id);

    $this->finished = ManufacturingHelper::product();
    $this->componentA = ManufacturingHelper::product();
});

it('routes the component moves through the pre-production area in the two-step flow', function () {
    $bom = ManufacturingHelper::bom($this->finished, [[$this->componentA, 2]]);

    $order = ManufacturingHelper::order($this->warehouse, $this->finished, $bom, 5);

    expect($order->rawMaterialMoves->first()->source_location_id)->toBe($this->warehouse->pbm_loc_id)
        ->and($order->finishedMoves->first()->destination_location_id)->toBe($this->warehouse->lot_stock_location_id);
});

it('consumes picked components from pre-production and stores the finished product in stock', function () {
    $bom = ManufacturingHelper::bom($this->finished, [[$this->componentA, 2]]);

    InventoryHelper::stockUp($this->componentA, $this->preProduction, 10);

    $order = ManufacturingHelper::order($this->warehouse, $this->finished, $bom, 5);

    ManufacturingHelper::confirm($order);
    ManufacturingHelper::produce($order, 5);

    expect($order->refresh()->state)->toBe(ManufacturingOrderState::DONE)
        ->and((float) $order->quantity_produced)->toBe(5.0)
        ->and(InventoryHelper::onHand($this->finished, $this->stock))->toBe(5.0)
        ->and(InventoryHelper::onHand($this->componentA, $this->preProduction))->toBe(0.0);
});
