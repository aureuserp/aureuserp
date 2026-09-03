<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Webkul\Inventory\Enums\ManufactureStep;
use Webkul\Inventory\Enums\OperationState;
use Webkul\Inventory\Facades\Inventory as InventoryFacade;
use Webkul\Inventory\Models\Location;
use Webkul\Inventory\Models\Operation;
use Webkul\Manufacturing\Enums\ManufacturingOrderState;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Package;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../inventories/tests/Helpers/InventoryHelper.php';
require_once __DIR__.'/../../Helpers/ManufacturingHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('inventories');
    TestBootstrapHelper::ensurePluginInstalled('manufacturing');

    foreach (['inventories', 'manufacturing'] as $plugin) {
        DB::table('plugins')->updateOrInsert(
            ['name' => $plugin],
            ['is_installed' => true, 'is_active' => true, 'updated_at' => now()],
        );
    }

    Package::$plugins = Plugin::all()->keyBy('name');

    URL::resolveMissingNamedRoutesUsing(fn () => '#');

    ManufacturingHelper::actingAsAdmin();

    $this->warehouse = ManufacturingHelper::multiStepWarehouse(ManufactureStep::THREE_STEPS);
    $this->stock = $this->warehouse->lotStockLocation;
    $this->preProduction = Location::findOrFail($this->warehouse->pbm_loc_id);
    $this->postProduction = Location::findOrFail($this->warehouse->sam_loc_id);

    $this->finished = ManufacturingHelper::product();
    $this->componentA = ManufacturingHelper::product();
});

it('routes components through pre-production and finished goods through post-production', function () {
    $bom = ManufacturingHelper::bom($this->finished, [[$this->componentA, 2]]);

    $order = ManufacturingHelper::order($this->warehouse, $this->finished, $bom, 5);

    expect($order->rawMaterialMoves->first()->source_location_id)->toBe($this->warehouse->pbm_loc_id)
        ->and($order->finishedMoves->first()->destination_location_id)->toBe($this->warehouse->sam_loc_id);
});

it('picks components, manufactures and lands the finished product in post-production', function () {
    $bom = ManufacturingHelper::bom($this->finished, [[$this->componentA, 2]]);

    InventoryHelper::stockUp($this->componentA, $this->preProduction, 10);

    $order = ManufacturingHelper::order($this->warehouse, $this->finished, $bom, 5);

    ManufacturingHelper::confirm($order);
    ManufacturingHelper::produce($order, 5);

    expect($order->refresh()->state)->toBe(ManufacturingOrderState::DONE)
        ->and((float) $order->quantity_produced)->toBe(5.0)
        ->and(InventoryHelper::onHand($this->finished, $this->postProduction))->toBe(5.0)
        ->and(InventoryHelper::onHand($this->componentA, $this->preProduction))->toBe(0.0);
});

it('stores the finished product from post-production into stock', function () {
    $bom = ManufacturingHelper::bom($this->finished, [[$this->componentA, 2]]);

    InventoryHelper::stockUp($this->componentA, $this->preProduction, 10);

    $order = ManufacturingHelper::order($this->warehouse, $this->finished, $bom, 5);

    ManufacturingHelper::confirm($order);
    ManufacturingHelper::produce($order, 5);

    $storeTransfer = Operation::query()
        ->where('operation_type_id', $this->warehouse->sam_type_id)
        ->latest('id')
        ->first();

    expect($storeTransfer)->not->toBeNull()
        ->and($storeTransfer->source_location_id)->toBe($this->warehouse->sam_loc_id)
        ->and($storeTransfer->destination_location_id)->toBe($this->warehouse->lot_stock_location_id);

    InventoryFacade::completeTransfer($storeTransfer->refresh());

    expect($storeTransfer->refresh()->state)->toBe(OperationState::DONE)
        ->and(InventoryHelper::onHand($this->finished, $this->stock))->toBe(5.0)
        ->and(InventoryHelper::onHand($this->finished, $this->postProduction))->toBe(0.0);
});
