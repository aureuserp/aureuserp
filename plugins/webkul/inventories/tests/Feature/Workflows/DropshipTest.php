<?php

use Webkul\Inventory\Enums\LocationType;
use Webkul\Inventory\Enums\MoveState;
use Webkul\Inventory\Enums\OperationState;
use Webkul\Inventory\Facades\Inventory;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../Helpers/InventoryHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('inventories');

    InventoryHelper::actingAsAdmin();

    $this->product = InventoryHelper::product();
});

it('routes a dropship straight from the supplier to the customer', function () {
    $operation = InventoryHelper::dropship([[$this->product, 10]]);

    expect($operation->sourceLocation->type)->toBe(LocationType::SUPPLIER)
        ->and($operation->destinationLocation->type)->toBe(LocationType::CUSTOMER)
        ->and($operation->moves)->toHaveCount(1);
});

it('assigns a dropship on confirm because both ends bypass reservation', function () {
    $operation = InventoryHelper::dropship([[$this->product, 10]]);

    Inventory::confirmTransfer($operation);

    $move = $operation->refresh()->moves->first();

    expect($operation->refresh()->state)->toBe(OperationState::ASSIGNED)
        ->and($move->state)->toBe(MoveState::ASSIGNED)
        ->and((float) $move->quantity)->toBe(10.0);
});

it('debits the supplier and credits the customer when the dropship is validated', function () {
    $operation = InventoryHelper::dropship([[$this->product, 10]]);

    $supplier = $operation->sourceLocation;
    $customer = $operation->destinationLocation;

    Inventory::confirmTransfer($operation);

    Inventory::doneTransfer($operation->refresh());

    expect($operation->refresh()->state)->toBe(OperationState::DONE)
        ->and(InventoryHelper::onHand($this->product, $supplier))->toBe(-10.0)
        ->and(InventoryHelper::onHand($this->product, $customer))->toBe(10.0);
});
