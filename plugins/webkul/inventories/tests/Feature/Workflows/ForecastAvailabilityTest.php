<?php

use Webkul\Inventory\Enums\MoveState;
use Webkul\Inventory\Facades\Inventory;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../Helpers/InventoryHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('inventories');

    InventoryHelper::actingAsAdmin();

    InventoryHelper::enableLocations();

    $this->warehouse = InventoryHelper::warehouse();
    $this->product = InventoryHelper::product();
    $this->stock = $this->warehouse->lotStockLocation;
    $this->shelf = InventoryHelper::sublocation($this->stock, 'Shelf A');
});

it('treats an internal transfer as consuming', function () {
    $operation = InventoryHelper::internalTransfer($this->warehouse, $this->stock, $this->shelf, [[$this->product, 5]]);

    expect($operation->moves->first()->consumesStock())->toBeTrue();
});

it('treats a delivery as consuming', function () {
    $operation = InventoryHelper::delivery($this->warehouse, [[$this->product, 5]]);

    expect($operation->moves->first()->consumesStock())->toBeTrue();
});

it('does not treat a receipt as consuming', function () {
    $operation = InventoryHelper::receipt($this->warehouse, [[$this->product, 5]]);

    expect($operation->moves->first()->consumesStock())->toBeFalse();
});

it('computes a numeric forecast availability for a draft internal transfer', function () {
    InventoryHelper::stockUp($this->product, $this->stock, 10);

    $operation = InventoryHelper::internalTransfer($this->warehouse, $this->stock, $this->shelf, [[$this->product, 5]]);

    $forecast = $operation->moves->first()->forecast_availability;

    expect($forecast)->not->toBeFalse()
        ->and(is_numeric($forecast))->toBeTrue();
});

it('reports the full free quantity when a draft internal transfer is covered', function () {
    InventoryHelper::stockUp($this->product, $this->stock, 10);

    $operation = InventoryHelper::internalTransfer($this->warehouse, $this->stock, $this->shelf, [[$this->product, 5]]);

    expect((float) $operation->moves->first()->forecast_availability)->toBe(10.0);
});

it('reports the full free quantity when a draft internal transfer is exactly covered', function () {
    InventoryHelper::stockUp($this->product, $this->stock, 10);

    $operation = InventoryHelper::internalTransfer($this->warehouse, $this->stock, $this->shelf, [[$this->product, 10]]);

    expect((float) $operation->moves->first()->forecast_availability)->toBe(10.0);
});

it('reports the shortfall when a draft internal transfer exceeds stock', function () {
    InventoryHelper::stockUp($this->product, $this->stock, 10);

    $operation = InventoryHelper::internalTransfer($this->warehouse, $this->stock, $this->shelf, [[$this->product, 11]]);

    expect((float) $operation->moves->first()->forecast_availability)->toBe(-1.0);
});

it('reports the shortfall when a draft internal transfer has no stock at all', function () {
    $operation = InventoryHelper::internalTransfer($this->warehouse, $this->stock, $this->shelf, [[$this->product, 5]]);

    expect((float) $operation->moves->first()->forecast_availability)->toBe(-5.0);
});

it('keeps forecast availability positive exactly when the internal transfer is covered', function (float $onHand, float $demand, bool $covered) {
    if ($onHand > 0) {
        InventoryHelper::stockUp($this->product, $this->stock, $onHand);
    }

    $operation = InventoryHelper::internalTransfer($this->warehouse, $this->stock, $this->shelf, [[$this->product, $demand]]);

    expect($operation->moves->first()->forecast_availability > 0)->toBe($covered);
})->with([
    'well covered'    => [10.0, 5.0, true],
    'exactly covered' => [10.0, 10.0, true],
    'one short'       => [10.0, 11.0, false],
    'no stock'        => [0.0, 5.0, false],
]);

it('reports the shortfall when a draft delivery exceeds stock', function () {
    InventoryHelper::stockUp($this->product, $this->stock, 10);

    $operation = InventoryHelper::delivery($this->warehouse, [[$this->product, 11]]);

    expect((float) $operation->moves->first()->forecast_availability)->toBe(-1.0);
});

it('reports the full free quantity when a draft delivery is exactly covered', function () {
    InventoryHelper::stockUp($this->product, $this->stock, 10);

    $operation = InventoryHelper::delivery($this->warehouse, [[$this->product, 10]]);

    expect((float) $operation->moves->first()->forecast_availability)->toBe(10.0);
});

it('ignores stock held in another warehouse when forecasting an internal transfer', function () {
    $otherWarehouse = InventoryHelper::warehouse();

    InventoryHelper::stockUp($this->product, $otherWarehouse->lotStockLocation, 100);

    $operation = InventoryHelper::internalTransfer($this->warehouse, $this->stock, $this->shelf, [[$this->product, 5]]);

    expect((float) $operation->moves->first()->forecast_availability)->toBe(-5.0);
});

it('reports the demand as forecast availability for a non storable product', function () {
    $consumable = InventoryHelper::product(['is_storable' => false]);

    $operation = InventoryHelper::internalTransfer($this->warehouse, $this->stock, $this->shelf, [[$consumable, 7]]);

    expect((float) $operation->moves->first()->forecast_availability)->toBe(7.0);
});

it('reports the reserved quantity as forecast availability once an internal transfer is assigned', function () {
    InventoryHelper::stockUp($this->product, $this->stock, 10);

    $operation = InventoryHelper::internalTransfer($this->warehouse, $this->stock, $this->shelf, [[$this->product, 10]]);

    Inventory::confirmTransfer($operation);

    $move = $operation->refresh()->moves->first();

    expect($move->state)->toBe(MoveState::ASSIGNED)
        ->and((float) $move->forecast_availability)->toBe(10.0);
});
