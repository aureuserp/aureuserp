<?php

use Webkul\Inventory\Enums\MoveState;
use Webkul\Inventory\Enums\OperationState;
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

it('creates an internal transfer operation type when locations are enabled', function () {
    expect($this->warehouse->internal_type_id)->not->toBeNull();
});

it('reserves an internal transfer against the source location on confirm', function () {
    InventoryHelper::stockUp($this->product, $this->stock, 10);

    $operation = InventoryHelper::internalTransfer($this->warehouse, $this->stock, $this->shelf, [[$this->product, 10]]);

    Inventory::confirmTransfer($operation);

    $move = $operation->refresh()->moves->first();

    expect($move->state)->toBe(MoveState::ASSIGNED)
        ->and((float) $move->quantity)->toBe(10.0)
        ->and($move->lines)->toHaveCount(1);

    expect(InventoryHelper::reserved($this->product, $this->stock))->toBe(10.0);
});

it('moves the quantity from the source to the destination sublocation on validation', function () {
    InventoryHelper::stockUp($this->product, $this->stock, 10);

    $operation = InventoryHelper::internalTransfer($this->warehouse, $this->stock, $this->shelf, [[$this->product, 10]]);

    Inventory::confirmTransfer($operation);

    Inventory::doneTransfer($operation->refresh());

    expect($operation->refresh()->state)->toBe(OperationState::DONE)
        ->and(InventoryHelper::quantOf($this->product, $this->stock))->toBeNull()
        ->and((float) InventoryHelper::quantOf($this->product, $this->shelf)?->quantity)->toBe(10.0);
});

it('leaves the internal transfer confirmed when the source is empty', function () {
    $operation = InventoryHelper::internalTransfer($this->warehouse, $this->stock, $this->shelf, [[$this->product, 10]]);

    Inventory::confirmTransfer($operation);

    $move = $operation->refresh()->moves->first();

    expect($move->state)->toBe(MoveState::CONFIRMED)
        ->and($move->lines)->toHaveCount(0);
});

it('partially reserves an internal transfer when the source is short', function () {
    InventoryHelper::stockUp($this->product, $this->stock, 4);

    $operation = InventoryHelper::internalTransfer($this->warehouse, $this->stock, $this->shelf, [[$this->product, 10]]);

    Inventory::confirmTransfer($operation);

    $move = $operation->refresh()->moves->first();

    expect($move->state)->toBe(MoveState::PARTIALLY_ASSIGNED)
        ->and((float) $move->quantity)->toBe(4.0);

    expect(InventoryHelper::reserved($this->product, $this->stock))->toBe(4.0);
});
