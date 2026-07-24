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

    $this->warehouse = InventoryHelper::warehouse(ReceptionStep::ONE_STEP, DeliveryStep::THREE_STEPS);
    $this->product = InventoryHelper::product();
    $this->stock = $this->warehouse->lotStockLocation;
    $this->packing = $this->warehouse->packStockLocation;
    $this->output = $this->warehouse->outputStockLocation;

    $this->shelfA = InventoryHelper::sublocation($this->stock, 'Shelf A');
    $this->shelfB = InventoryHelper::sublocation($this->stock, 'Shelf B');
});

function packOperation($warehouse): ?Operation
{
    return Operation::query()
        ->where('operation_type_id', $warehouse->pack_type_id)
        ->whereNull('back_order_id')
        ->first();
}

function threeStepShipOperation($warehouse): ?Operation
{
    return Operation::query()
        ->where('operation_type_id', $warehouse->out_type_id)
        ->whereNull('back_order_id')
        ->first();
}

function confirmedThreeStepPick($warehouse, $product, float $stock, float $demand): Operation
{
    if ($stock > 0) {
        InventoryHelper::stockUp($product, $warehouse->lotStockLocation, $stock);
    }

    $operation = InventoryHelper::delivery($warehouse, [[$product, $demand]]);

    Inventory::confirmTransfer($operation);

    return $operation->refresh();
}

function validatedThreeStepPick($warehouse, $product, float $stock, float $demand, ?float $picked = null): Operation
{
    $operation = confirmedThreeStepPick($warehouse, $product, $stock, $demand);

    if ($picked !== null) {
        InventoryHelper::pick($operation->moves->first(), $picked);
    }

    Inventory::doneTransfer($operation->refresh());

    return $operation->refresh();
}

function validatedThreeStepPack($warehouse, $product, float $stock, float $demand, ?float $picked = null): Operation
{
    validatedThreeStepPick($warehouse, $product, $stock, $demand);

    $pack = packOperation($warehouse);

    if ($picked !== null) {
        InventoryHelper::pick($pack->refresh()->moves->first(), $picked);
    }

    Inventory::doneTransfer($pack->refresh());

    return $pack->refresh();
}

it('routes the pick leg from stock into the packing zone', function () {
    $operation = InventoryHelper::delivery($this->warehouse, [[$this->product, 10]]);

    expect($operation->operation_type_id)->toBe($this->warehouse->pick_type_id)
        ->and($operation->source_location_id)->toBe($this->stock->id)
        ->and($operation->destination_location_id)->toBe($this->packing->id);
});

it('creates no pack operation before the pick leg is validated', function () {
    confirmedThreeStepPick($this->warehouse, $this->product, 10, 10);

    expect(packOperation($this->warehouse))->toBeNull();
});

it('reserves the pick leg against stock on confirm', function () {
    $operation = confirmedThreeStepPick($this->warehouse, $this->product, 10, 10);

    $move = $operation->moves->first();

    expect($operation->state)->toBe(OperationState::ASSIGNED)
        ->and($move->state)->toBe(MoveState::ASSIGNED)
        ->and((float) $move->quantity)->toBe(10.0);

    expect(InventoryHelper::reserved($this->product, $this->stock))->toBe(10.0);
});

it('partially assigns the pick leg when demand exceeds availability', function () {
    $operation = confirmedThreeStepPick($this->warehouse, $this->product, 4, 10);

    expect($operation->moves->first()->state)->toBe(MoveState::PARTIALLY_ASSIGNED)
        ->and((float) $operation->moves->first()->quantity)->toBe(4.0);
});

it('splits the pick leg across two source locations', function () {
    InventoryHelper::stockUp($this->product, $this->shelfA, 5);
    InventoryHelper::stockUp($this->product, $this->shelfB, 5);

    $operation = InventoryHelper::delivery($this->warehouse, [[$this->product, 10]]);

    Inventory::confirmTransfer($operation);

    $move = $operation->refresh()->moves->first()->refresh();

    expect($move->lines)->toHaveCount(2)
        ->and(InventoryHelper::lineQuantities($move))->toBe([5.0, 5.0]);

    expect(InventoryHelper::reservedUnder($this->product, $this->stock))->toBe(10.0);
});

it('lands the picked quantity in the packing zone', function () {
    validatedThreeStepPick($this->warehouse, $this->product, 10, 10);

    expect(InventoryHelper::onHand($this->product, $this->stock))->toBe(0.0)
        ->and(InventoryHelper::onHand($this->product, $this->packing))->toBe(10.0)
        ->and(InventoryHelper::onHand($this->product, $this->output))->toBe(0.0);
});

it('pushes a pack operation from the packing zone to output when the pick leg is validated', function () {
    validatedThreeStepPick($this->warehouse, $this->product, 10, 10);

    $pack = packOperation($this->warehouse);

    expect($pack)->not->toBeNull()
        ->and($pack->moves)->toHaveCount(1);

    $move = $pack->moves->first();

    expect($move->operation_type_id)->toBe($this->warehouse->pack_type_id)
        ->and($move->source_location_id)->toBe($this->packing->id)
        ->and($move->destination_location_id)->toBe($this->output->id)
        ->and($move->procure_method)->toBe(ProcureMethod::MAKE_TO_ORDER)
        ->and($move->state)->toBe(MoveState::ASSIGNED)
        ->and((float) $move->product_uom_qty)->toBe(10.0);

    expect(InventoryHelper::reserved($this->product, $this->packing))->toBe(10.0);
});

it('does not push the ship operation until packing is validated', function () {
    validatedThreeStepPick($this->warehouse, $this->product, 10, 10);

    expect(threeStepShipOperation($this->warehouse))->toBeNull();
});

it('parks the quantity in output after packing is validated', function () {
    validatedThreeStepPack($this->warehouse, $this->product, 10, 10);

    expect(InventoryHelper::onHand($this->product, $this->packing))->toBe(0.0)
        ->and(InventoryHelper::onHand($this->product, $this->output))->toBe(10.0);
});

it('pushes a ship operation from output to the customer when packing is validated', function () {
    validatedThreeStepPack($this->warehouse, $this->product, 10, 10);

    $ship = threeStepShipOperation($this->warehouse);

    expect($ship)->not->toBeNull()
        ->and($ship->moves)->toHaveCount(1);

    $move = $ship->moves->first();

    expect($move->operation_type_id)->toBe($this->warehouse->out_type_id)
        ->and($move->source_location_id)->toBe($this->output->id)
        ->and($move->destinationLocation->type)->toBe(LocationType::CUSTOMER)
        ->and($move->procure_method)->toBe(ProcureMethod::MAKE_TO_ORDER)
        ->and($move->state)->toBe(MoveState::ASSIGNED);

    expect(InventoryHelper::reserved($this->product, $this->output))->toBe(10.0);
});

it('links the pack move back to the pick move as its origin', function () {
    $pick = validatedThreeStepPick($this->warehouse, $this->product, 10, 10);

    $pickMove = $pick->moves->first();
    $packMove = packOperation($this->warehouse)->moves->first();

    expect($pickMove->moveDestinations->pluck('id')->all())->toContain($packMove->id)
        ->and($packMove->moveOrigins->pluck('id')->all())->toContain($pickMove->id);
});

it('links the ship move back to the pack move as its origin', function () {
    $pack = validatedThreeStepPack($this->warehouse, $this->product, 10, 10);

    $packMove = $pack->moves->first();
    $shipMove = threeStepShipOperation($this->warehouse)->moves->first();

    expect($packMove->moveDestinations->pluck('id')->all())->toContain($shipMove->id)
        ->and($shipMove->moveOrigins->pluck('id')->all())->toContain($packMove->id);
});

it('delivers to the customer only after all three legs are validated', function () {
    validatedThreeStepPack($this->warehouse, $this->product, 10, 10);

    $ship = threeStepShipOperation($this->warehouse);

    $customer = $ship->destinationLocation;

    Inventory::doneTransfer($ship->refresh());

    expect($ship->refresh()->state)->toBe(OperationState::DONE)
        ->and(InventoryHelper::onHand($this->product, $this->stock))->toBe(0.0)
        ->and(InventoryHelper::onHand($this->product, $this->packing))->toBe(0.0)
        ->and(InventoryHelper::onHand($this->product, $this->output))->toBe(0.0)
        ->and(InventoryHelper::onHand($this->product, $customer))->toBe(10.0);
});

it('stops pushing once the goods reach the customer', function () {
    validatedThreeStepPack($this->warehouse, $this->product, 10, 10);

    $ship = threeStepShipOperation($this->warehouse);

    Inventory::doneTransfer($ship->refresh());

    expect($ship->refresh()->moves->first()->moveDestinations)->toHaveCount(0)
        ->and(InventoryHelper::operationCount($this->warehouse))->toBe(3);
});

it('backorders the pick leg and pushes only the picked quantity into packing', function () {
    $operation = validatedThreeStepPick($this->warehouse, $this->product, 10, 10, 4);

    $backorder = InventoryHelper::backorderOf($operation);

    expect($backorder)->not->toBeNull()
        ->and($backorder->operation_type_id)->toBe($this->warehouse->pick_type_id)
        ->and((float) $backorder->moves->first()->product_uom_qty)->toBe(6.0);

    expect((float) packOperation($this->warehouse)->moves->first()->product_uom_qty)->toBe(4.0)
        ->and(InventoryHelper::onHand($this->product, $this->packing))->toBe(4.0)
        ->and(InventoryHelper::onHand($this->product, $this->stock))->toBe(6.0);
});

it('merges the second push into the existing pack move when the pick backorder is validated', function () {
    $operation = validatedThreeStepPick($this->warehouse, $this->product, 10, 10, 4);

    $backorder = InventoryHelper::backorderOf($operation);

    Inventory::doneTransfer($backorder->refresh());

    $pack = packOperation($this->warehouse);

    expect($pack->refresh()->moves)->toHaveCount(1)
        ->and((float) $pack->moves->first()->product_uom_qty)->toBe(10.0);

    expect(InventoryHelper::onHand($this->product, $this->packing))->toBe(10.0)
        ->and(InventoryHelper::reserved($this->product, $this->packing))->toBe(10.0);
});

it('backorders the pack leg and pushes only the validated quantity into shipping', function () {
    $pack = validatedThreeStepPack($this->warehouse, $this->product, 10, 10, 4);

    $backorder = InventoryHelper::backorderOf($pack);

    expect($backorder)->not->toBeNull()
        ->and($backorder->operation_type_id)->toBe($this->warehouse->pack_type_id)
        ->and((float) $backorder->moves->first()->product_uom_qty)->toBe(6.0);

    expect((float) threeStepShipOperation($this->warehouse)->moves->first()->product_uom_qty)->toBe(4.0);

    expect(InventoryHelper::onHand($this->product, $this->packing))->toBe(6.0)
        ->and(InventoryHelper::onHand($this->product, $this->output))->toBe(4.0);
});

it('backorders the ship leg when it is partially validated', function () {
    validatedThreeStepPack($this->warehouse, $this->product, 10, 10);

    $ship = threeStepShipOperation($this->warehouse);

    InventoryHelper::pick($ship->refresh()->moves->first(), 4);

    Inventory::doneTransfer($ship->refresh());

    $backorder = InventoryHelper::backorderOf($ship->refresh());

    expect($backorder)->not->toBeNull()
        ->and($backorder->operation_type_id)->toBe($this->warehouse->out_type_id)
        ->and((float) $backorder->moves->first()->product_uom_qty)->toBe(6.0);

    expect(InventoryHelper::onHand($this->product, $this->output))->toBe(6.0);
});

it('creates no pick backorder when validating with cancelBackOrder', function () {
    $operation = confirmedThreeStepPick($this->warehouse, $this->product, 10, 10);

    InventoryHelper::pick($operation->moves->first(), 4);

    Inventory::doneTransfer($operation->refresh(), cancelBackOrder: true);

    expect(InventoryHelper::backorderOf($operation->refresh()))->toBeNull()
        ->and((float) packOperation($this->warehouse)->moves->first()->product_uom_qty)->toBe(4.0);
});

it('refuses to validate the pick leg with no picked quantity', function () {
    $operation = confirmedThreeStepPick($this->warehouse, $this->product, 10, 10);

    InventoryHelper::pick($operation->moves->first(), 0);

    expect(fn () => Inventory::doneTransfer($operation->refresh()))
        ->toThrow(Exception::class, __('inventories::filament/clusters/operations/actions/validate.notification.warning.no-quantities-reserved.body'));
});

it('cancels the pick leg and pushes nothing', function () {
    $operation = confirmedThreeStepPick($this->warehouse, $this->product, 10, 10);

    Inventory::cancelTransfer($operation);

    expect($operation->refresh()->state)->toBe(OperationState::CANCELED)
        ->and(packOperation($this->warehouse))->toBeNull();

    expect(InventoryHelper::reserved($this->product, $this->stock))->toBe(0.0)
        ->and(InventoryHelper::onHand($this->product, $this->stock))->toBe(10.0);
});

it('cancels the pack leg and releases the packing reservation', function () {
    validatedThreeStepPick($this->warehouse, $this->product, 10, 10);

    $pack = packOperation($this->warehouse);

    Inventory::cancelTransfer($pack->refresh());

    expect($pack->refresh()->state)->toBe(OperationState::CANCELED)
        ->and(threeStepShipOperation($this->warehouse))->toBeNull();

    expect(InventoryHelper::onHand($this->product, $this->packing))->toBe(10.0)
        ->and(InventoryHelper::reserved($this->product, $this->packing))->toBe(0.0);
});

it('cancels the ship leg and releases the output reservation', function () {
    validatedThreeStepPack($this->warehouse, $this->product, 10, 10);

    $ship = threeStepShipOperation($this->warehouse);

    Inventory::cancelTransfer($ship->refresh());

    expect($ship->refresh()->state)->toBe(OperationState::CANCELED);

    expect(InventoryHelper::onHand($this->product, $this->output))->toBe(10.0)
        ->and(InventoryHelper::reserved($this->product, $this->output))->toBe(0.0);
});

it('unreserves the pack move and drops it back to confirmed', function () {
    validatedThreeStepPick($this->warehouse, $this->product, 10, 10);

    $pack = packOperation($this->warehouse);

    Inventory::unreserveMoves($pack->refresh()->moves);

    $move = $pack->refresh()->moves->first();

    expect($move->state)->toBe(MoveState::CONFIRMED)
        ->and($move->lines)->toHaveCount(0);

    expect(InventoryHelper::reserved($this->product, $this->packing))->toBe(0.0)
        ->and(InventoryHelper::onHand($this->product, $this->packing))->toBe(10.0);
});

it('unreserves the ship move and drops it back to confirmed', function () {
    validatedThreeStepPack($this->warehouse, $this->product, 10, 10);

    $ship = threeStepShipOperation($this->warehouse);

    Inventory::unreserveMoves($ship->refresh()->moves);

    $move = $ship->refresh()->moves->first();

    expect($move->state)->toBe(MoveState::CONFIRMED)
        ->and($move->lines)->toHaveCount(0);

    expect(InventoryHelper::reserved($this->product, $this->output))->toBe(0.0);
});

it('re-reserves the pack move when availability is checked again', function () {
    validatedThreeStepPick($this->warehouse, $this->product, 10, 10);

    $pack = packOperation($this->warehouse);

    Inventory::unreserveMoves($pack->refresh()->moves);

    Inventory::assignTransfer($pack->refresh());

    $move = $pack->refresh()->moves->first();

    expect($move->state)->toBe(MoveState::ASSIGNED)
        ->and((float) $move->quantity)->toBe(10.0);

    expect(InventoryHelper::reserved($this->product, $this->packing))->toBe(10.0);
});

it('returns the pick leg from packing back into stock and unreserves the pack move', function () {
    $operation = validatedThreeStepPick($this->warehouse, $this->product, 10, 10);

    $packMove = packOperation($this->warehouse)->moves->first();

    expect($packMove->state)->toBe(MoveState::ASSIGNED);

    $return = Inventory::returnTransfer($operation, [$operation->moves->first()->id => 4]);

    expect($return->return_id)->toBe($operation->id)
        ->and($return->source_location_id)->toBe($this->packing->id)
        ->and($return->destination_location_id)->toBe($this->stock->id);

    expect($packMove->refresh()->state)->not->toBe(MoveState::ASSIGNED)
        ->and((float) $packMove->quantity)->toBe(0.0);

    Inventory::doneTransfer($return->refresh());

    expect(InventoryHelper::onHand($this->product, $this->packing))->toBe(6.0)
        ->and(InventoryHelper::onHand($this->product, $this->stock))->toBe(4.0);
});

it('returns the pack leg from output back into the packing zone', function () {
    $pack = validatedThreeStepPack($this->warehouse, $this->product, 10, 10);

    $return = Inventory::returnTransfer($pack, [$pack->moves->first()->id => 3]);

    expect($return->return_id)->toBe($pack->id)
        ->and($return->source_location_id)->toBe($this->output->id)
        ->and($return->destination_location_id)->toBe($this->packing->id);

    Inventory::doneTransfer($return->refresh());

    expect(InventoryHelper::onHand($this->product, $this->output))->toBe(7.0)
        ->and(InventoryHelper::onHand($this->product, $this->packing))->toBe(3.0);
});

it('returns the ship leg from the customer back into output', function () {
    validatedThreeStepPack($this->warehouse, $this->product, 10, 10);

    $ship = threeStepShipOperation($this->warehouse);

    Inventory::doneTransfer($ship->refresh());

    $ship->refresh();

    $return = Inventory::returnTransfer($ship, [$ship->moves->first()->id => 3]);

    expect($return->source_location_id)->toBe($ship->destination_location_id)
        ->and($return->destination_location_id)->toBe($this->output->id);

    Inventory::doneTransfer($return->refresh());

    expect(InventoryHelper::onHand($this->product, $this->output))->toBe(3.0);
});

it('creates one pack move per product on a multi product pick', function () {
    $other = InventoryHelper::product();

    InventoryHelper::stockUp($this->product, $this->stock, 10);
    InventoryHelper::stockUp($other, $this->stock, 4);

    $operation = InventoryHelper::delivery($this->warehouse, [
        [$this->product, 10],
        [$other, 4],
    ]);

    Inventory::confirmTransfer($operation);

    Inventory::doneTransfer($operation->refresh());

    $pack = packOperation($this->warehouse);

    expect($pack->moves)->toHaveCount(2);

    $byProduct = $pack->moves->keyBy('product_id');

    expect((float) $byProduct[$this->product->id]->product_uom_qty)->toBe(10.0)
        ->and((float) $byProduct[$other->id]->product_uom_qty)->toBe(4.0);
});

it('pushes a decimal picked quantity through the whole chain', function () {
    validatedThreeStepPack($this->warehouse, $this->product, 2.5, 2.5, 1.25);

    $ship = threeStepShipOperation($this->warehouse);

    expect((float) $ship->moves->first()->product_uom_qty)->toBe(1.25);

    Inventory::doneTransfer($ship->refresh());

    expect(InventoryHelper::onHand($this->product, $this->output))->toBe(0.0)
        ->and(InventoryHelper::onHand($this->product, $this->packing))->toBe(1.25)
        ->and(InventoryHelper::onHand($this->product, $this->stock))->toBe(0.0);
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

it('carries the lot from stock through packing and output to the customer', function () {
    InventoryHelper::trackLots($this->warehouse->pickType);

    $product = InventoryHelper::lotTrackedProduct();
    $lot = InventoryHelper::lot($product, 'LOT-A');

    InventoryHelper::stockUp($product, $this->stock, 10, $lot->id);

    validatedThreeStepPack($this->warehouse, $product, 0, 10, 10);

    $ship = threeStepShipOperation($this->warehouse);
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

it('releases the reservation when a reserved pick move line is deleted', function () {
    $operation = confirmedThreeStepPick($this->warehouse, $this->product, 10, 10);

    $move = $operation->moves->first();

    $move->lines->first()->delete();

    $move = $move->refresh();

    expect($move->lines)->toHaveCount(0)
        ->and($move->state)->toBe(MoveState::CONFIRMED);

    expect(InventoryHelper::reserved($this->product, $this->stock))->toBe(0.0);
});

it('deletes each intermediate quant as the goods move through pick pack and ship', function () {
    validatedThreeStepPick($this->warehouse, $this->product, 10, 10);

    expect(InventoryHelper::quantOf($this->product, $this->stock))->toBeNull()
        ->and((float) InventoryHelper::quantOf($this->product, $this->packing)?->quantity)->toBe(10.0);

    Inventory::doneTransfer(packOperation($this->warehouse)->refresh());

    expect(InventoryHelper::quantOf($this->product, $this->packing))->toBeNull()
        ->and((float) InventoryHelper::quantOf($this->product, $this->output)?->quantity)->toBe(10.0);

    Inventory::doneTransfer(threeStepShipOperation($this->warehouse)->refresh());

    expect(InventoryHelper::quantOf($this->product, $this->output))->toBeNull();
});
