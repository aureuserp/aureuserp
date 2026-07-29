<?php

use Webkul\Inventory\Enums\LocationType;
use Webkul\Inventory\Enums\DeliveryStep;
use Webkul\Inventory\Enums\MoveState;
use Webkul\Inventory\Enums\OperationState;
use Webkul\Inventory\Enums\PackageUse;
use Webkul\Inventory\Enums\ProcureMethod;
use Webkul\Inventory\Enums\ReceptionStep;
use Webkul\Inventory\Facades\Inventory;
use Webkul\Inventory\Models\Operation;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../Helpers/InventoryHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('inventories');

    InventoryHelper::actingAsAdmin();

    $this->warehouse = InventoryHelper::warehouse(ReceptionStep::ONE_STEP, DeliveryStep::TWO_STEPS);
    $this->product = InventoryHelper::product();
    $this->stock = $this->warehouse->lotStockLocation;
    $this->output = $this->warehouse->outputStockLocation;

    $this->shelfA = InventoryHelper::sublocation($this->stock, 'Shelf A');
    $this->shelfB = InventoryHelper::sublocation($this->stock, 'Shelf B');
});

function shipOperation($warehouse): ?Operation
{
    return Operation::query()
        ->where('operation_type_id', $warehouse->out_type_id)
        ->whereNull('back_order_id')
        ->first();
}

function confirmedPick($warehouse, $product, float $stock, float $demand): Operation
{
    if ($stock > 0) {
        InventoryHelper::stockUp($product, $warehouse->lotStockLocation, $stock);
    }

    $operation = InventoryHelper::delivery($warehouse, [[$product, $demand]]);

    Inventory::confirmTransfer($operation);

    return $operation->refresh();
}

function validatedTwoStepPick($warehouse, $product, float $stock, float $demand, ?float $picked = null): Operation
{
    $operation = confirmedPick($warehouse, $product, $stock, $demand);

    if ($picked !== null) {
        InventoryHelper::pick($operation->moves->first(), $picked);
    }

    Inventory::doneTransfer($operation->refresh());

    return $operation->refresh();
}

it('routes the pick leg from stock into the output location', function () {
    $operation = InventoryHelper::delivery($this->warehouse, [[$this->product, 10]]);

    expect($operation->operation_type_id)->toBe($this->warehouse->pick_type_id)
        ->and($operation->source_location_id)->toBe($this->stock->id)
        ->and($operation->destination_location_id)->toBe($this->output->id);
});

it('creates no ship operation before the pick leg is validated', function () {
    confirmedPick($this->warehouse, $this->product, 10, 10);

    expect(shipOperation($this->warehouse))->toBeNull();
});

it('reserves the pick leg against stock on confirm', function () {
    $operation = confirmedPick($this->warehouse, $this->product, 10, 10);

    $move = $operation->moves->first();

    expect($operation->state)->toBe(OperationState::ASSIGNED)
        ->and($move->state)->toBe(MoveState::ASSIGNED)
        ->and((float) $move->quantity)->toBe(10.0);

    expect(InventoryHelper::reserved($this->product, $this->stock))->toBe(10.0);
});

it('leaves the pick leg confirmed when nothing is available', function () {
    $operation = confirmedPick($this->warehouse, $this->product, 0, 10);

    $move = $operation->moves->first();

    expect($operation->state)->toBe(OperationState::CONFIRMED)
        ->and($move->state)->toBe(MoveState::CONFIRMED)
        ->and($move->lines)->toHaveCount(0);
});

it('partially assigns the pick leg when demand exceeds availability', function () {
    $operation = confirmedPick($this->warehouse, $this->product, 4, 10);

    $move = $operation->moves->first();

    expect($move->state)->toBe(MoveState::PARTIALLY_ASSIGNED)
        ->and((float) $move->quantity)->toBe(4.0);

    expect(InventoryHelper::reserved($this->product, $this->stock))->toBe(4.0);
});

it('splits the pick leg across two source locations', function () {
    InventoryHelper::stockUp($this->product, $this->shelfA, 5);
    InventoryHelper::stockUp($this->product, $this->shelfB, 5);

    $operation = InventoryHelper::delivery($this->warehouse, [[$this->product, 10]]);

    Inventory::confirmTransfer($operation);

    $move = $operation->refresh()->moves->first()->refresh();

    expect($move->state)->toBe(MoveState::ASSIGNED)
        ->and($move->lines)->toHaveCount(2)
        ->and(InventoryHelper::lineQuantities($move))->toBe([5.0, 5.0]);

    expect(InventoryHelper::reservedUnder($this->product, $this->stock))->toBe(10.0);
});

it('lands the picked quantity in output and not at the customer', function () {
    $operation = validatedTwoStepPick($this->warehouse, $this->product, 10, 10);

    expect($operation->state)->toBe(OperationState::DONE)
        ->and(InventoryHelper::onHand($this->product, $this->stock))->toBe(0.0)
        ->and(InventoryHelper::onHand($this->product, $this->output))->toBe(10.0);
});

it('pushes a ship operation from output to the customer when the pick leg is validated', function () {
    validatedTwoStepPick($this->warehouse, $this->product, 10, 10);

    $ship = shipOperation($this->warehouse);

    expect($ship)->not->toBeNull()
        ->and($ship->moves)->toHaveCount(1);

    $move = $ship->moves->first();

    expect($move->operation_type_id)->toBe($this->warehouse->out_type_id)
        ->and($move->source_location_id)->toBe($this->output->id)
        ->and($move->destinationLocation->type)->toBe(LocationType::CUSTOMER)
        ->and($move->procure_method)->toBe(ProcureMethod::MAKE_TO_ORDER)
        ->and((float) $move->product_uom_qty)->toBe(10.0);
});

it('links the ship move back to the pick move as its origin', function () {
    $operation = validatedTwoStepPick($this->warehouse, $this->product, 10, 10);

    $pickMove = $operation->moves->first();
    $shipMove = shipOperation($this->warehouse)->moves->first();

    expect($pickMove->moveDestinations->pluck('id')->all())->toContain($shipMove->id)
        ->and($shipMove->moveOrigins->pluck('id')->all())->toContain($pickMove->id);
});

it('reserves the ship move against the output location', function () {
    validatedTwoStepPick($this->warehouse, $this->product, 10, 10);

    $move = shipOperation($this->warehouse)->moves->first();

    expect($move->state)->toBe(MoveState::ASSIGNED)
        ->and((float) $move->quantity)->toBe(10.0)
        ->and($move->lines)->toHaveCount(1);

    expect(InventoryHelper::reserved($this->product, $this->output))->toBe(10.0);
});

it('empties the output location when the ship leg is validated', function () {
    validatedTwoStepPick($this->warehouse, $this->product, 10, 10);

    $ship = shipOperation($this->warehouse);

    $customer = $ship->destinationLocation;

    Inventory::doneTransfer($ship->refresh());

    expect($ship->refresh()->state)->toBe(OperationState::DONE)
        ->and(InventoryHelper::onHand($this->product, $this->output))->toBe(0.0)
        ->and(InventoryHelper::reserved($this->product, $this->output))->toBe(0.0)
        ->and(InventoryHelper::onHand($this->product, $customer))->toBe(10.0);
});

it('stops pushing once the goods reach the customer', function () {
    validatedTwoStepPick($this->warehouse, $this->product, 10, 10);

    $ship = shipOperation($this->warehouse);

    Inventory::doneTransfer($ship->refresh());

    expect($ship->refresh()->moves->first()->moveDestinations)->toHaveCount(0)
        ->and(InventoryHelper::operationCount($this->warehouse))->toBe(2);
});

it('backorders the pick leg and pushes only the picked quantity', function () {
    $operation = validatedTwoStepPick($this->warehouse, $this->product, 10, 10, 4);

    $backorder = InventoryHelper::backorderOf($operation);

    expect($backorder)->not->toBeNull()
        ->and($backorder->operation_type_id)->toBe($this->warehouse->pick_type_id)
        ->and((float) $backorder->moves->first()->product_uom_qty)->toBe(6.0)
        ->and($backorder->moves->first()->additional)->toBeFalse();

    expect((float) shipOperation($this->warehouse)->moves->first()->product_uom_qty)->toBe(4.0)
        ->and(InventoryHelper::onHand($this->product, $this->output))->toBe(4.0)
        ->and(InventoryHelper::onHand($this->product, $this->stock))->toBe(6.0);
});

it('merges the second push into the existing ship move when the pick backorder is validated', function () {
    $operation = validatedTwoStepPick($this->warehouse, $this->product, 10, 10, 4);

    $backorder = InventoryHelper::backorderOf($operation);

    Inventory::doneTransfer($backorder->refresh());

    $ship = shipOperation($this->warehouse);

    expect($ship->refresh()->moves)->toHaveCount(1)
        ->and((float) $ship->moves->first()->product_uom_qty)->toBe(10.0);

    expect(InventoryHelper::onHand($this->product, $this->output))->toBe(10.0)
        ->and(InventoryHelper::reserved($this->product, $this->output))->toBe(10.0);
});

it('creates no pick backorder when validating with cancelBackOrder', function () {
    $operation = confirmedPick($this->warehouse, $this->product, 10, 10);

    InventoryHelper::pick($operation->moves->first(), 4);

    Inventory::doneTransfer($operation->refresh(), cancelBackOrder: true);

    expect(InventoryHelper::backorderOf($operation->refresh()))->toBeNull()
        ->and((float) shipOperation($this->warehouse)->moves->first()->product_uom_qty)->toBe(4.0);

    expect(InventoryHelper::reserved($this->product, $this->stock))->toBe(0.0);
});

it('backorders the ship leg when it is partially validated', function () {
    validatedTwoStepPick($this->warehouse, $this->product, 10, 10);

    $ship = shipOperation($this->warehouse);

    InventoryHelper::pick($ship->refresh()->moves->first(), 4);

    Inventory::doneTransfer($ship->refresh());

    $backorder = InventoryHelper::backorderOf($ship->refresh());

    expect($backorder)->not->toBeNull()
        ->and($backorder->operation_type_id)->toBe($this->warehouse->out_type_id)
        ->and((float) $backorder->moves->first()->product_uom_qty)->toBe(6.0);

    expect(InventoryHelper::onHand($this->product, $this->output))->toBe(6.0);
});

it('refuses to validate the pick leg with no picked quantity', function () {
    $operation = confirmedPick($this->warehouse, $this->product, 10, 10);

    InventoryHelper::pick($operation->moves->first(), 0);

    expect(fn () => Inventory::doneTransfer($operation->refresh()))
        ->toThrow(Exception::class, __('inventories::filament/clusters/operations/actions/validate.notification.warning.no-quantities-reserved.body'));
});

it('cancels the pick leg and pushes nothing', function () {
    $operation = confirmedPick($this->warehouse, $this->product, 10, 10);

    Inventory::cancelTransfer($operation);

    $operation->refresh();

    expect($operation->state)->toBe(OperationState::CANCELED)
        ->and($operation->moves->first()->state)->toBe(MoveState::CANCELED)
        ->and($operation->moves->first()->lines)->toHaveCount(0)
        ->and(shipOperation($this->warehouse))->toBeNull();

    expect(InventoryHelper::reserved($this->product, $this->stock))->toBe(0.0)
        ->and(InventoryHelper::onHand($this->product, $this->stock))->toBe(10.0);
});

it('cancels the ship leg and releases the output reservation', function () {
    validatedTwoStepPick($this->warehouse, $this->product, 10, 10);

    $ship = shipOperation($this->warehouse);

    Inventory::cancelTransfer($ship->refresh());

    expect($ship->refresh()->state)->toBe(OperationState::CANCELED)
        ->and($ship->moves->first()->state)->toBe(MoveState::CANCELED);

    expect(InventoryHelper::onHand($this->product, $this->output))->toBe(10.0)
        ->and(InventoryHelper::reserved($this->product, $this->output))->toBe(0.0);
});

it('refuses to cancel the ship leg once it is done', function () {
    validatedTwoStepPick($this->warehouse, $this->product, 10, 10);

    $ship = shipOperation($this->warehouse);

    Inventory::doneTransfer($ship->refresh());

    expect(fn () => Inventory::cancelTransfer($ship->refresh()))
        ->toThrow(Exception::class, __('inventories::system.inventory-manager.cancel-move.already-done'));
});

it('unreserves the pick move and drops it back to confirmed', function () {
    $operation = confirmedPick($this->warehouse, $this->product, 10, 10);

    Inventory::unreserveMoves($operation->moves);

    $move = $operation->refresh()->moves->first();

    expect($move->state)->toBe(MoveState::CONFIRMED)
        ->and((float) $move->quantity)->toBe(0.0)
        ->and($move->lines)->toHaveCount(0);

    expect(InventoryHelper::reserved($this->product, $this->stock))->toBe(0.0);
});

it('unreserves the ship move and drops it back to confirmed', function () {
    validatedTwoStepPick($this->warehouse, $this->product, 10, 10);

    $ship = shipOperation($this->warehouse);

    Inventory::unreserveMoves($ship->refresh()->moves);

    $move = $ship->refresh()->moves->first();

    expect($move->state)->toBe(MoveState::CONFIRMED)
        ->and((float) $move->quantity)->toBe(0.0)
        ->and($move->lines)->toHaveCount(0);

    expect(InventoryHelper::reserved($this->product, $this->output))->toBe(0.0)
        ->and(InventoryHelper::onHand($this->product, $this->output))->toBe(10.0);
});

it('re-reserves the ship move when availability is checked again', function () {
    validatedTwoStepPick($this->warehouse, $this->product, 10, 10);

    $ship = shipOperation($this->warehouse);

    Inventory::unreserveMoves($ship->refresh()->moves);

    Inventory::assignTransfer($ship->refresh());

    $move = $ship->refresh()->moves->first();

    expect($move->state)->toBe(MoveState::ASSIGNED)
        ->and((float) $move->quantity)->toBe(10.0);

    expect(InventoryHelper::reserved($this->product, $this->output))->toBe(10.0);
});

it('returns the pick leg from output back into stock and unreserves the ship move', function () {
    $operation = validatedTwoStepPick($this->warehouse, $this->product, 10, 10);

    $shipMove = shipOperation($this->warehouse)->moves->first();

    expect($shipMove->state)->toBe(MoveState::ASSIGNED);

    $return = Inventory::returnTransfer($operation, [$operation->moves->first()->id => 4]);

    expect($return->return_id)->toBe($operation->id)
        ->and($return->source_location_id)->toBe($this->output->id)
        ->and($return->destination_location_id)->toBe($this->stock->id);

    expect($shipMove->refresh()->state)->not->toBe(MoveState::ASSIGNED)
        ->and((float) $shipMove->quantity)->toBe(0.0);

    Inventory::doneTransfer($return->refresh());

    expect(InventoryHelper::onHand($this->product, $this->output))->toBe(6.0)
        ->and(InventoryHelper::onHand($this->product, $this->stock))->toBe(4.0);
});

it('returns the ship leg from the customer back into output', function () {
    validatedTwoStepPick($this->warehouse, $this->product, 10, 10);

    $ship = shipOperation($this->warehouse);

    Inventory::doneTransfer($ship->refresh());

    $ship->refresh();

    $return = Inventory::returnTransfer($ship, [$ship->moves->first()->id => 3]);

    expect($return->return_id)->toBe($ship->id)
        ->and($return->source_location_id)->toBe($ship->destination_location_id)
        ->and($return->destination_location_id)->toBe($this->output->id);

    Inventory::doneTransfer($return->refresh());

    expect(InventoryHelper::onHand($this->product, $this->output))->toBe(3.0);
});

it('creates one ship move per product on a multi product pick', function () {
    $other = InventoryHelper::product();

    InventoryHelper::stockUp($this->product, $this->stock, 10);
    InventoryHelper::stockUp($other, $this->stock, 4);

    $operation = InventoryHelper::delivery($this->warehouse, [
        [$this->product, 10],
        [$other, 4],
    ]);

    Inventory::confirmTransfer($operation);

    Inventory::doneTransfer($operation->refresh());

    $ship = shipOperation($this->warehouse);

    expect($ship->moves)->toHaveCount(2);

    $byProduct = $ship->moves->keyBy('product_id');

    expect((float) $byProduct[$this->product->id]->product_uom_qty)->toBe(10.0)
        ->and((float) $byProduct[$other->id]->product_uom_qty)->toBe(4.0);

    expect(InventoryHelper::onHand($this->product, $this->output))->toBe(10.0)
        ->and(InventoryHelper::onHand($other, $this->output))->toBe(4.0);
});

it('pushes a decimal picked quantity through to the ship leg', function () {
    validatedTwoStepPick($this->warehouse, $this->product, 2.5, 2.5, 1.25);

    expect((float) shipOperation($this->warehouse)->moves->first()->product_uom_qty)->toBe(1.25)
        ->and(InventoryHelper::onHand($this->product, $this->output))->toBe(1.25)
        ->and(InventoryHelper::onHand($this->product, $this->stock))->toBe(1.25);
});

it('reserves the pick leg from the lot held in stock', function () {
    InventoryHelper::trackLots($this->warehouse->pickType);

    $product = InventoryHelper::lotTrackedProduct();
    $lot = InventoryHelper::lot($product, 'LOT-A');

    InventoryHelper::stockUp($product, $this->stock, 10, $lot->id);

    $operation = InventoryHelper::delivery($this->warehouse, [[$product, 10]]);

    Inventory::confirmTransfer($operation);

    $move = $operation->refresh()->moves->first();

    expect($move->state)->toBe(MoveState::ASSIGNED)
        ->and($move->lines)->toHaveCount(1)
        ->and($move->lines->first()->lot_id)->toBe($lot->id);
});

it('carries the lot from stock through output to the customer', function () {
    InventoryHelper::trackLots($this->warehouse->pickType);

    $product = InventoryHelper::lotTrackedProduct();
    $lot = InventoryHelper::lot($product, 'LOT-A');

    InventoryHelper::stockUp($product, $this->stock, 10, $lot->id);

    validatedTwoStepPick($this->warehouse, $product, 0, 10, 10);

    $ship = shipOperation($this->warehouse);
    $shipLine = $ship->moves->first()->lines->first();

    expect($shipLine->lot_id)->toBe($lot->id);

    Inventory::doneTransfer($ship->refresh());

    $customer = $ship->refresh()->destinationLocation;

    expect((float) InventoryHelper::quantOf($product, $customer, $lot->id)->quantity)->toBe(10.0)
        ->and(InventoryHelper::onHand($product, $this->stock))->toBe(0.0);
});

it('creates one pick move line per serial number in stock', function () {
    InventoryHelper::trackLots($this->warehouse->pickType);

    $product = InventoryHelper::serialTrackedProduct();
    $first = InventoryHelper::lot($product, 'SN-1');
    $second = InventoryHelper::lot($product, 'SN-2');

    InventoryHelper::stockUp($product, $this->stock, 1, $first->id);
    InventoryHelper::stockUp($product, $this->stock, 1, $second->id);

    $operation = InventoryHelper::delivery($this->warehouse, [[$product, 2]]);

    Inventory::confirmTransfer($operation);

    $move = $operation->refresh()->moves->first();

    expect($move->lines)->toHaveCount(2)
        ->and($move->lines->pluck('lot_id')->all())->toBe([$first->id, $second->id])
        ->and(InventoryHelper::lineQuantities($move))->toBe([1.0, 1.0]);
});

it('packs an entire disposable package into a package level on the pick leg', function () {
    $package = InventoryHelper::package(PackageUse::DISPOSABLE, $this->stock);

    InventoryHelper::stockUp($this->product, $this->stock, 10, null, $package->id);

    $operation = InventoryHelper::delivery($this->warehouse, [[$this->product, 10]]);

    Inventory::confirmTransfer($operation);

    $operation->refresh();
    $line = $operation->moves->first()->lines->first();

    expect($operation->packageLevels)->toHaveCount(1)
        ->and($line->package_id)->toBe($package->id)
        ->and($line->package_level_id)->not->toBeNull()
        ->and($line->result_package_id)->toBe($package->id);
});

it('does not pack a package when only part of it leaves stock on the pick leg', function () {
    $package = InventoryHelper::package(PackageUse::DISPOSABLE, $this->stock);

    InventoryHelper::stockUp($this->product, $this->stock, 10, null, $package->id);

    $operation = InventoryHelper::delivery($this->warehouse, [[$this->product, 4]]);

    Inventory::confirmTransfer($operation);

    $operation->refresh();
    $line = $operation->moves->first()->lines->first();

    expect($operation->packageLevels)->toHaveCount(0)
        ->and($line->package_id)->toBe($package->id)
        ->and($line->package_level_id)->toBeNull()
        ->and($line->result_package_id)->toBeNull();
});

it('packs an entire untyped package into a package level', function () {
    $package = InventoryHelper::package(PackageUse::DISPOSABLE, $this->stock, typed: false);

    InventoryHelper::stockUp($this->product, $this->stock, 10, null, $package->id);

    $operation = InventoryHelper::delivery($this->warehouse, [[$this->product, 10]]);

    Inventory::confirmTransfer($operation);

    $operation->refresh();
    $line = $operation->moves->first()->lines->first();

    expect($package->package_type_id)->toBeNull()
        ->and($operation->packageLevels)->toHaveCount(1)
        ->and($line->package_id)->toBe($package->id)
        ->and($line->package_level_id)->not->toBeNull()
        ->and($line->result_package_id)->toBe($package->id);
});

it('releases the freed reservation when the pick move line quantity is decreased', function () {
    $operation = confirmedPick($this->warehouse, $this->product, 10, 10);

    $operation->moves->first()->lines->first()->update(['qty' => 6]);

    $move = $operation->refresh()->moves->first()->refresh();

    expect((float) $move->quantity)->toBe(6.0)
        ->and($move->state)->toBe(MoveState::PARTIALLY_ASSIGNED);

    expect(InventoryHelper::reserved($this->product, $this->stock))->toBe(6.0);
});

it('releases the reservation when a reserved pick move line is deleted', function () {
    $operation = confirmedPick($this->warehouse, $this->product, 10, 10);

    $move = $operation->moves->first();

    $move->lines->first()->delete();

    $move = $move->refresh();

    expect($move->lines)->toHaveCount(0)
        ->and($move->state)->toBe(MoveState::CONFIRMED);

    expect(InventoryHelper::reserved($this->product, $this->stock))->toBe(0.0);
});

it('deletes the source quant when the pick leg empties stock and the output quant when shipped', function () {
    validatedTwoStepPick($this->warehouse, $this->product, 10, 10);

    expect(InventoryHelper::quantOf($this->product, $this->stock))->toBeNull()
        ->and((float) InventoryHelper::quantOf($this->product, $this->output)?->quantity)->toBe(10.0);

    Inventory::doneTransfer(shipOperation($this->warehouse)->refresh());

    expect(InventoryHelper::quantOf($this->product, $this->output))->toBeNull();
});
