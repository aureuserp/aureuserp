<?php

use Webkul\Inventory\Enums\LocationType;
use Webkul\Inventory\Enums\MoveState;
use Webkul\Inventory\Enums\OperationState;
use Webkul\Inventory\Enums\PackageUse;
use Webkul\Inventory\Enums\ProcureMethod;
use Webkul\Inventory\Enums\ReservationMethod;
use Webkul\Inventory\Facades\Inventory;
use Webkul\Inventory\Models\Operation;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../Helpers/InventoryHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('inventories');

    InventoryHelper::actingAsAdmin();

    $this->warehouse = InventoryHelper::warehouse();
    $this->product = InventoryHelper::product();
    $this->stock = $this->warehouse->lotStockLocation;

    $this->shelfA = InventoryHelper::sublocation($this->stock, 'Shelf A');
    $this->shelfB = InventoryHelper::sublocation($this->stock, 'Shelf B');
});

function confirmedOneStepDelivery($warehouse, $product, float $stock, float $demand): Operation
{
    if ($stock > 0) {
        InventoryHelper::stockUp($product, $warehouse->lotStockLocation, $stock);
    }

    $operation = InventoryHelper::delivery($warehouse, [[$product, $demand]]);

    Inventory::confirmTransfer($operation);

    return $operation->refresh();
}

function validatedOneStepDelivery($warehouse, $product, float $stock, float $demand, ?float $picked = null): Operation
{
    $operation = confirmedOneStepDelivery($warehouse, $product, $stock, $demand);

    if ($picked !== null) {
        InventoryHelper::pick($operation->moves->first(), $picked);
    }

    Inventory::doneTransfer($operation->refresh());

    return $operation->refresh();
}

it('creates a one step delivery in draft with a single draft move', function () {
    $operation = InventoryHelper::delivery($this->warehouse, [[$this->product, 10]]);

    expect($operation->state)->toBe(OperationState::DRAFT)
        ->and($operation->moves)->toHaveCount(1);

    $move = $operation->moves->first();

    expect($move->state)->toBe(MoveState::DRAFT)
        ->and((float) $move->product_uom_qty)->toBe(10.0)
        ->and((float) $move->quantity)->toBe(0.0)
        ->and($move->lines)->toHaveCount(0);
});

it('routes a one step delivery from stock straight to the customer location', function () {
    $operation = InventoryHelper::delivery($this->warehouse, [[$this->product, 10]]);

    expect($operation->operation_type_id)->toBe($this->warehouse->out_type_id)
        ->and($operation->source_location_id)->toBe($this->stock->id)
        ->and($operation->destinationLocation->type)->toBe(LocationType::CUSTOMER);
});

it('reserves the full demand from stock on confirm when quantity is available', function () {
    $operation = confirmedOneStepDelivery($this->warehouse, $this->product, 10, 10);

    $move = $operation->moves->first();

    expect($operation->state)->toBe(OperationState::ASSIGNED)
        ->and($move->state)->toBe(MoveState::ASSIGNED)
        ->and((float) $move->quantity)->toBe(10.0)
        ->and($move->lines)->toHaveCount(1);

    $line = $move->lines->first();

    expect($line->source_location_id)->toBe($this->stock->id)
        ->and((float) $line->qty)->toBe(10.0)
        ->and($line->state)->toBe(MoveState::ASSIGNED);

    expect(InventoryHelper::onHand($this->product, $this->stock))->toBe(10.0)
        ->and(InventoryHelper::reserved($this->product, $this->stock))->toBe(10.0);
});

it('leaves the move confirmed with no move lines when nothing is available', function () {
    $operation = confirmedOneStepDelivery($this->warehouse, $this->product, 0, 10);

    $move = $operation->moves->first();

    expect($operation->state)->toBe(OperationState::CONFIRMED)
        ->and($move->state)->toBe(MoveState::CONFIRMED)
        ->and((float) $move->quantity)->toBe(0.0)
        ->and($move->lines)->toHaveCount(0);
});

it('partially assigns the move when demand exceeds the available quantity', function () {
    $operation = confirmedOneStepDelivery($this->warehouse, $this->product, 4, 10);

    $move = $operation->moves->first();

    expect($move->state)->toBe(MoveState::PARTIALLY_ASSIGNED)
        ->and((float) $move->product_uom_qty)->toBe(10.0)
        ->and((float) $move->quantity)->toBe(4.0);

    expect(InventoryHelper::reserved($this->product, $this->stock))->toBe(4.0);
});

it('mirrors the move state onto its move lines when partially assigned', function () {
    $operation = confirmedOneStepDelivery($this->warehouse, $this->product, 4, 10);

    $move = $operation->moves->first();

    expect($move->lines->pluck('state')->unique()->all())->toBe([MoveState::PARTIALLY_ASSIGNED]);
});

it('creates one move line per source location when stock is split across two locations', function () {
    InventoryHelper::stockUp($this->product, $this->shelfA, 5);
    InventoryHelper::stockUp($this->product, $this->shelfB, 5);

    $operation = InventoryHelper::delivery($this->warehouse, [[$this->product, 10]]);

    Inventory::confirmTransfer($operation);

    $move = $operation->refresh()->moves->first()->refresh();

    expect($move->state)->toBe(MoveState::ASSIGNED)
        ->and((float) $move->quantity)->toBe(10.0)
        ->and($move->lines)->toHaveCount(2)
        ->and(InventoryHelper::lineQuantities($move))->toBe([5.0, 5.0]);

    expect(InventoryHelper::reservedUnder($this->product, $this->stock))->toBe(10.0)
        ->and(InventoryHelper::reserved($this->product, $this->shelfA))->toBe(5.0)
        ->and(InventoryHelper::reserved($this->product, $this->shelfB))->toBe(5.0);
});

it('splits a partial draw across two locations and stays fully assigned', function () {
    InventoryHelper::stockUp($this->product, $this->shelfA, 5);
    InventoryHelper::stockUp($this->product, $this->shelfB, 5);

    $operation = InventoryHelper::delivery($this->warehouse, [[$this->product, 8]]);

    Inventory::confirmTransfer($operation);

    $move = $operation->refresh()->moves->first()->refresh();

    expect($move->state)->toBe(MoveState::ASSIGNED)
        ->and((float) $move->quantity)->toBe(8.0)
        ->and($move->lines)->toHaveCount(2)
        ->and(InventoryHelper::lineQuantities($move))->toBe([3.0, 5.0]);

    expect(InventoryHelper::reservedUnder($this->product, $this->stock))->toBe(8.0)
        ->and(InventoryHelper::onHandUnder($this->product, $this->stock))->toBe(10.0);
});

it('keeps the reserved quantity per location consistent with the move lines', function () {
    InventoryHelper::stockUp($this->product, $this->shelfA, 5);
    InventoryHelper::stockUp($this->product, $this->shelfB, 5);

    $operation = InventoryHelper::delivery($this->warehouse, [[$this->product, 8]]);

    Inventory::confirmTransfer($operation);

    $move = $operation->refresh()->moves->first()->refresh();

    foreach ($move->lines as $line) {
        expect(InventoryHelper::reserved($this->product, $line->sourceLocation))
            ->toBe((float) $line->qty);
    }
});

it('gives the first delivery its full demand and only the remainder to the second', function () {
    InventoryHelper::stockUp($this->product, $this->shelfA, 5);
    InventoryHelper::stockUp($this->product, $this->shelfB, 5);

    $first = InventoryHelper::delivery($this->warehouse, [[$this->product, 8]]);

    Inventory::confirmTransfer($first);

    $second = InventoryHelper::delivery($this->warehouse, [[$this->product, 8]]);

    Inventory::confirmTransfer($second);

    $firstMove = $first->refresh()->moves->first()->refresh();
    $secondMove = $second->refresh()->moves->first()->refresh();

    expect($firstMove->state)->toBe(MoveState::ASSIGNED)
        ->and((float) $firstMove->quantity)->toBe(8.0);

    expect($secondMove->state)->toBe(MoveState::PARTIALLY_ASSIGNED)
        ->and((float) $secondMove->quantity)->toBe(2.0)
        ->and($secondMove->lines)->toHaveCount(1);

    expect(InventoryHelper::reservedUnder($this->product, $this->stock))->toBe(10.0);
});

it('validates a delivery drawn from two locations and clears both quants', function () {
    InventoryHelper::stockUp($this->product, $this->shelfA, 5);
    InventoryHelper::stockUp($this->product, $this->shelfB, 5);

    $operation = InventoryHelper::delivery($this->warehouse, [[$this->product, 10]]);

    Inventory::confirmTransfer($operation);

    Inventory::doneTransfer($operation->refresh());

    $move = $operation->refresh()->moves->first()->refresh();

    expect($operation->state)->toBe(OperationState::DONE)
        ->and($move->state)->toBe(MoveState::DONE)
        ->and($move->lines)->toHaveCount(2);

    expect(InventoryHelper::onHandUnder($this->product, $this->stock))->toBe(0.0)
        ->and(InventoryHelper::reservedUnder($this->product, $this->stock))->toBe(0.0);
});

it('releases the reservation and removes the stock when the delivery is validated', function () {
    $operation = validatedOneStepDelivery($this->warehouse, $this->product, 10, 10);

    $move = $operation->moves->first();

    expect($operation->state)->toBe(OperationState::DONE)
        ->and($move->state)->toBe(MoveState::DONE)
        ->and((float) $move->quantity)->toBe(10.0);

    expect(InventoryHelper::onHand($this->product, $this->stock))->toBe(0.0)
        ->and(InventoryHelper::reserved($this->product, $this->stock))->toBe(0.0);
});

it('credits the customer location when the delivery is validated', function () {
    $operation = confirmedOneStepDelivery($this->warehouse, $this->product, 10, 10);

    $customer = $operation->destinationLocation;

    Inventory::doneTransfer($operation);

    expect(InventoryHelper::onHand($this->product, $customer))->toBe(10.0);
});

it('leaves the remaining stock untouched when delivering part of it', function () {
    validatedOneStepDelivery($this->warehouse, $this->product, 25, 10);

    expect(InventoryHelper::onHand($this->product, $this->stock))->toBe(15.0)
        ->and(InventoryHelper::reserved($this->product, $this->stock))->toBe(0.0);
});

it('unreserves a delivery move and drops it back to confirmed', function () {
    $operation = confirmedOneStepDelivery($this->warehouse, $this->product, 10, 10);

    Inventory::unreserveMoves($operation->moves);

    $move = $operation->refresh()->moves->first();

    expect($move->state)->toBe(MoveState::CONFIRMED)
        ->and((float) $move->quantity)->toBe(0.0)
        ->and($move->lines)->toHaveCount(0);

    expect(InventoryHelper::reserved($this->product, $this->stock))->toBe(0.0)
        ->and(InventoryHelper::onHand($this->product, $this->stock))->toBe(10.0);
});

it('re-reserves the full demand when availability is checked again', function () {
    $operation = confirmedOneStepDelivery($this->warehouse, $this->product, 10, 10);

    Inventory::unreserveMoves($operation->moves);

    Inventory::assignTransfer($operation->refresh());

    $operation->refresh();
    $move = $operation->moves->first();

    expect($operation->state)->toBe(OperationState::ASSIGNED)
        ->and($move->state)->toBe(MoveState::ASSIGNED)
        ->and((float) $move->quantity)->toBe(10.0)
        ->and($move->lines)->toHaveCount(1);

    expect(InventoryHelper::reserved($this->product, $this->stock))->toBe(10.0);
});

it('re-reserves only what is still available after stock shrinks', function () {
    $operation = confirmedOneStepDelivery($this->warehouse, $this->product, 10, 10);

    Inventory::unreserveMoves($operation->moves);

    InventoryHelper::quantOf($this->product, $this->stock)->update(['quantity' => 4]);

    Inventory::assignTransfer($operation->refresh());

    $move = $operation->refresh()->moves->first();

    expect($move->state)->toBe(MoveState::PARTIALLY_ASSIGNED)
        ->and((float) $move->quantity)->toBe(4.0);

    expect(InventoryHelper::reserved($this->product, $this->stock))->toBe(4.0);
});

it('does not double reserve when availability is checked twice', function () {
    $operation = confirmedOneStepDelivery($this->warehouse, $this->product, 10, 10);

    Inventory::assignTransfer($operation);

    $move = $operation->refresh()->moves->first();

    expect($move->lines)->toHaveCount(1)
        ->and((float) $move->quantity)->toBe(10.0);

    expect(InventoryHelper::reserved($this->product, $this->stock))->toBe(10.0);
});

it('tops up a partially assigned move when stock arrives', function () {
    $operation = confirmedOneStepDelivery($this->warehouse, $this->product, 4, 10);

    expect($operation->moves->first()->state)->toBe(MoveState::PARTIALLY_ASSIGNED);

    InventoryHelper::quantOf($this->product, $this->stock)->update(['quantity' => 10]);

    Inventory::assignTransfer($operation->refresh());

    $move = $operation->refresh()->moves->first();

    expect($move->state)->toBe(MoveState::ASSIGNED)
        ->and((float) $move->quantity)->toBe(10.0);

    expect(InventoryHelper::reserved($this->product, $this->stock))->toBe(10.0);
});

it('frees the stock for another delivery once unreserved', function () {
    $first = confirmedOneStepDelivery($this->warehouse, $this->product, 10, 10);

    Inventory::unreserveMoves($first->moves);

    $second = InventoryHelper::delivery($this->warehouse, [[$this->product, 10]]);

    Inventory::confirmTransfer($second);

    expect($second->refresh()->moves->first()->state)->toBe(MoveState::ASSIGNED)
        ->and(InventoryHelper::reserved($this->product, $this->stock))->toBe(10.0);
});

it('silently skips a validated move because validation marks it picked', function () {
    $operation = validatedOneStepDelivery($this->warehouse, $this->product, 10, 10);

    $move = $operation->moves->first();

    expect($move->state)->toBe(MoveState::DONE)
        ->and($move->is_picked)->toBeTrue();

    Inventory::unreserveMoves($operation->moves);

    expect($operation->refresh()->moves->first()->state)->toBe(MoveState::DONE)
        ->and(InventoryHelper::onHand($this->product, $this->stock))->toBe(0.0);
});

it('refuses to unreserve a done move that was never picked', function () {
    $operation = confirmedOneStepDelivery($this->warehouse, $this->product, 10, 10);

    $operation->moves->first()
        ->forceFill(['state' => MoveState::DONE, 'is_picked' => false])
        ->saveQuietly();

    expect(fn () => Inventory::unreserveMoves($operation->refresh()->moves))
        ->toThrow(Exception::class, __('inventories::system.inventory-manager.unreserve-move.already-done'));
});

it('creates a backorder for the unpicked remainder', function () {
    $operation = validatedOneStepDelivery($this->warehouse, $this->product, 10, 10, 6);

    expect($operation->state)->toBe(OperationState::DONE)
        ->and($operation->moves)->toHaveCount(1);

    $backorder = InventoryHelper::backorderOf($operation);

    expect($backorder)->not->toBeNull()
        ->and($backorder->back_order_id)->toBe($operation->id)
        ->and($backorder->moves)->toHaveCount(1);

    $doneMove = $operation->moves->first();

    expect($doneMove->state)->toBe(MoveState::DONE)
        ->and((float) $doneMove->product_uom_qty)->toBe(6.0)
        ->and((float) $doneMove->quantity)->toBe(6.0);

    $backorderMove = $backorder->moves->first();

    expect((float) $backorderMove->product_uom_qty)->toBe(4.0)
        ->and($backorderMove->state)->toBe(MoveState::ASSIGNED)
        ->and((float) $backorderMove->quantity)->toBe(4.0)
        ->and($backorderMove->additional)->toBeFalse();

    expect(InventoryHelper::onHand($this->product, $this->stock))->toBe(4.0)
        ->and(InventoryHelper::reserved($this->product, $this->stock))->toBe(4.0);
});

it('creates a backorder of a backorder when the remainder is again partially picked', function () {
    $operation = validatedOneStepDelivery($this->warehouse, $this->product, 10, 10, 6);

    $backorder = InventoryHelper::backorderOf($operation);

    InventoryHelper::pick($backorder->moves->first(), 1);

    Inventory::doneTransfer($backorder->refresh());

    $second = InventoryHelper::backorderOf($backorder->refresh());

    expect($second)->not->toBeNull()
        ->and((float) $second->moves->first()->product_uom_qty)->toBe(3.0)
        ->and($second->moves->first()->additional)->toBeFalse();
});

it('creates a backorder on a partially available delivery', function () {
    $operation = confirmedOneStepDelivery($this->warehouse, $this->product, 4, 10);

    $move = $operation->moves->first();

    expect($move->state)->toBe(MoveState::PARTIALLY_ASSIGNED)
        ->and((float) $move->quantity)->toBe(4.0);

    Inventory::doneTransfer($operation);

    $backorder = InventoryHelper::backorderOf($operation->refresh());

    expect($backorder)->not->toBeNull()
        ->and((float) $backorder->moves->first()->product_uom_qty)->toBe(6.0);

    expect(InventoryHelper::onHand($this->product, $this->stock))->toBe(0.0);
});

it('creates no backorder when validating with cancelBackOrder', function () {
    $operation = confirmedOneStepDelivery($this->warehouse, $this->product, 10, 10);

    InventoryHelper::pick($operation->moves->first(), 6);

    Inventory::doneTransfer($operation->refresh(), cancelBackOrder: true);

    $operation->refresh();
    $move = $operation->moves->first();

    expect($operation->state)->toBe(OperationState::DONE)
        ->and(InventoryHelper::backorderOf($operation))->toBeNull()
        ->and($move->state)->toBe(MoveState::DONE)
        ->and((float) $move->product_uom_qty)->toBe(10.0)
        ->and((float) $move->quantity)->toBe(6.0);

    expect(InventoryHelper::onHand($this->product, $this->stock))->toBe(4.0)
        ->and(InventoryHelper::reserved($this->product, $this->stock))->toBe(0.0);
});

it('refuses to validate a delivery with no picked quantity', function () {
    $operation = confirmedOneStepDelivery($this->warehouse, $this->product, 10, 10);

    InventoryHelper::pick($operation->moves->first(), 0);

    expect(fn () => Inventory::doneTransfer($operation->refresh()))
        ->toThrow(Exception::class, __('inventories::filament/clusters/operations/actions/validate.notification.warning.no-quantities-reserved.body'));
});

it('cancels a draft delivery', function () {
    $operation = InventoryHelper::delivery($this->warehouse, [[$this->product, 10]]);

    Inventory::cancelTransfer($operation);

    $operation->refresh();

    expect($operation->state)->toBe(OperationState::CANCELED)
        ->and($operation->moves->first()->state)->toBe(MoveState::CANCELED);
});

it('cancels a confirmed delivery and releases its reservation', function () {
    $operation = confirmedOneStepDelivery($this->warehouse, $this->product, 10, 10);

    expect(InventoryHelper::reserved($this->product, $this->stock))->toBe(10.0)
        ->and($operation->moves->first()->lines)->toHaveCount(1);

    Inventory::cancelTransfer($operation);

    $operation->refresh();
    $move = $operation->moves->first();

    expect($operation->state)->toBe(OperationState::CANCELED)
        ->and($move->state)->toBe(MoveState::CANCELED)
        ->and($move->lines)->toHaveCount(0)
        ->and($move->is_picked)->toBeFalse();

    expect(InventoryHelper::reserved($this->product, $this->stock))->toBe(0.0)
        ->and(InventoryHelper::onHand($this->product, $this->stock))->toBe(10.0);
});

it('cancels every move of a multi product delivery', function () {
    $other = InventoryHelper::product();

    $operation = InventoryHelper::delivery($this->warehouse, [
        [$this->product, 10],
        [$other, 5],
    ]);

    Inventory::confirmTransfer($operation);

    Inventory::cancelTransfer($operation->refresh());

    $operation->refresh();

    expect($operation->state)->toBe(OperationState::CANCELED)
        ->and($operation->moves->pluck('state')->unique()->all())->toBe([MoveState::CANCELED]);
});

it('refuses to cancel a delivery that is already done', function () {
    $operation = validatedOneStepDelivery($this->warehouse, $this->product, 10, 10);

    expect(fn () => Inventory::cancelTransfer($operation->refresh()))
        ->toThrow(Exception::class, __('inventories::system.inventory-manager.cancel-move.already-done'));
});

it('leaves a cancelled delivery cancelled when cancelled again', function () {
    $operation = InventoryHelper::delivery($this->warehouse, [[$this->product, 10]]);

    Inventory::cancelTransfer($operation);

    Inventory::cancelTransfer($operation->refresh());

    expect($operation->refresh()->state)->toBe(OperationState::CANCELED);
});

it('returns a delivery back into stock', function () {
    $operation = validatedOneStepDelivery($this->warehouse, $this->product, 10, 10);

    expect(InventoryHelper::onHand($this->product, $this->stock))->toBe(0.0);

    $return = Inventory::returnTransfer($operation, [$operation->moves->first()->id => 3]);

    expect($return->return_id)->toBe($operation->id)
        ->and($return->source_location_id)->toBe($operation->destination_location_id)
        ->and($return->destination_location_id)->toBe($this->stock->id);

    Inventory::doneTransfer($return->refresh());

    expect($return->refresh()->state)->toBe(OperationState::DONE)
        ->and(InventoryHelper::onHand($this->product, $this->stock))->toBe(3.0);
});

it('leaves the original delivery untouched by the return', function () {
    $operation = validatedOneStepDelivery($this->warehouse, $this->product, 10, 10);

    Inventory::returnTransfer($operation, [$operation->moves->first()->id => 3]);

    $operation->refresh();

    expect($operation->state)->toBe(OperationState::DONE)
        ->and($operation->moves)->toHaveCount(1)
        ->and((float) $operation->moves->first()->product_uom_qty)->toBe(10.0);
});

it('schedules the returned move for today instead of inheriting the original schedule', function () {
    $operation = validatedOneStepDelivery($this->warehouse, $this->product, 10, 10);

    $sourceMove = $operation->refresh()->moves->first();

    $sourceMove->scheduled_at = now()->subDays(10);

    $sourceMove->saveQuietly();

    $return = Inventory::returnTransfer($operation->refresh(), [$sourceMove->id => 3]);

    $returnMove = $return->refresh()->moves->first();

    expect($returnMove->scheduled_at->isToday())->toBeTrue();
});

it('resets the returned move picking and procurement fields instead of inheriting them', function () {
    $operation = validatedOneStepDelivery($this->warehouse, $this->product, 10, 10);

    $sourceMove = $operation->refresh()->moves->first();

    expect($sourceMove->is_picked)->toBeTrue();

    $return = Inventory::returnTransfer($operation, [$sourceMove->id => 3]);

    $returnMove = $return->refresh()->moves->first();

    expect($returnMove->is_picked)->toBeFalse()
        ->and($returnMove->procure_method)->toBe(ProcureMethod::MAKE_TO_STOCK)
        ->and($returnMove->is_refund)->toBeTrue()
        ->and($returnMove->final_location_id)->toBeNull()
        ->and($returnMove->origin_returned_move_id)->toBe($sourceMove->id)
        ->and((float) $returnMove->product_uom_qty)->toBe(3.0)
        ->and((float) $returnMove->product_qty)->toBe(3.0);
});

it('resets the backorder move picking flag instead of inheriting it', function () {
    $operation = validatedOneStepDelivery($this->warehouse, $this->product, 10, 10, 4);

    $backorder = InventoryHelper::backorderOf($operation);

    expect($backorder)->not->toBeNull()
        ->and($backorder->moves->first()->is_picked)->toBeFalse()
        ->and($backorder->source_location_id)->toBe($operation->source_location_id)
        ->and($backorder->destination_location_id)->toBe($operation->destination_location_id);
});

it('reserves a decimal quantity from stock', function () {
    $operation = confirmedOneStepDelivery($this->warehouse, $this->product, 2.5, 1.25);

    $move = $operation->moves->first();

    expect($move->state)->toBe(MoveState::ASSIGNED)
        ->and((float) $move->quantity)->toBe(1.25)
        ->and((float) $move->lines->first()->qty)->toBe(1.25);

    expect(InventoryHelper::reserved($this->product, $this->stock))->toBe(1.25);
});

it('leaves the decimal remainder in stock after a decimal delivery', function () {
    validatedOneStepDelivery($this->warehouse, $this->product, 2.5, 1.25);

    expect(InventoryHelper::onHand($this->product, $this->stock))->toBe(1.25)
        ->and(InventoryHelper::reserved($this->product, $this->stock))->toBe(0.0);
});

it('assigns each delivery move independently against its own stock', function () {
    $other = InventoryHelper::product();

    InventoryHelper::stockUp($this->product, $this->stock, 10);

    $operation = InventoryHelper::delivery($this->warehouse, [
        [$this->product, 10],
        [$other, 5],
    ]);

    Inventory::confirmTransfer($operation);

    $byProduct = $operation->refresh()->moves->keyBy('product_id');

    expect($byProduct[$this->product->id]->state)->toBe(MoveState::ASSIGNED)
        ->and((float) $byProduct[$this->product->id]->quantity)->toBe(10.0)
        ->and($byProduct[$this->product->id]->lines)->toHaveCount(1);

    expect($byProduct[$other->id]->state)->toBe(MoveState::CONFIRMED)
        ->and((float) $byProduct[$other->id]->quantity)->toBe(0.0)
        ->and($byProduct[$other->id]->lines)->toHaveCount(0);

    expect($operation->refresh()->state)->toBe(OperationState::ASSIGNED);
});

it('reports the operation as confirmed when no move is available', function () {
    $other = InventoryHelper::product();

    $operation = InventoryHelper::delivery($this->warehouse, [
        [$this->product, 10],
        [$other, 5],
    ]);

    Inventory::confirmTransfer($operation);

    expect($operation->refresh()->state)->toBe(OperationState::CONFIRMED);
});

it('keeps per product reservations separate', function () {
    $other = InventoryHelper::product();

    InventoryHelper::stockUp($this->product, $this->stock, 10);
    InventoryHelper::stockUp($other, $this->stock, 3);

    $operation = InventoryHelper::delivery($this->warehouse, [
        [$this->product, 10],
        [$other, 5],
    ]);

    Inventory::confirmTransfer($operation);

    expect(InventoryHelper::reserved($this->product, $this->stock))->toBe(10.0)
        ->and(InventoryHelper::reserved($other, $this->stock))->toBe(3.0);

    $byProduct = $operation->refresh()->moves->keyBy('product_id');

    expect($byProduct[$other->id]->state)->toBe(MoveState::PARTIALLY_ASSIGNED);
});

it('reserves a delivery from the lot held in stock', function () {
    InventoryHelper::trackLots($this->warehouse->outType);

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

it('draws from two lots in receipt order when one lot is not enough', function () {
    InventoryHelper::trackLots($this->warehouse->outType);

    $product = InventoryHelper::lotTrackedProduct();
    $first = InventoryHelper::lot($product, 'LOT-A');
    $second = InventoryHelper::lot($product, 'LOT-B');

    InventoryHelper::stockUp($product, $this->stock, 5, $first->id);
    InventoryHelper::stockUp($product, $this->stock, 5, $second->id);

    $operation = InventoryHelper::delivery($this->warehouse, [[$product, 8]]);

    Inventory::confirmTransfer($operation);

    $move = $operation->refresh()->moves->first()->refresh();

    expect($move->state)->toBe(MoveState::ASSIGNED)
        ->and($move->lines)->toHaveCount(2)
        ->and($move->lines->pluck('lot_id')->all())->toBe([$first->id, $second->id])
        ->and($move->lines->pluck('qty')->map(fn ($qty) => (float) $qty)->all())->toBe([5.0, 3.0]);
});

it('removes only the delivered lot quantity from stock', function () {
    InventoryHelper::trackLots($this->warehouse->outType);

    $product = InventoryHelper::lotTrackedProduct();
    $lot = InventoryHelper::lot($product, 'LOT-A');

    InventoryHelper::stockUp($product, $this->stock, 10, $lot->id);

    $operation = InventoryHelper::delivery($this->warehouse, [[$product, 4]]);

    Inventory::confirmTransfer($operation);

    Inventory::doneTransfer($operation->refresh());

    $quant = InventoryHelper::quantOf($product, $this->stock, $lot->id);

    expect((float) $quant->quantity)->toBe(6.0)
        ->and((float) $quant->reserved_quantity)->toBe(0.0);
});

it('creates one move line per serial number on a delivery', function () {
    InventoryHelper::trackLots($this->warehouse->outType);

    $product = InventoryHelper::serialTrackedProduct();
    $first = InventoryHelper::lot($product, 'SN-1');
    $second = InventoryHelper::lot($product, 'SN-2');

    InventoryHelper::stockUp($product, $this->stock, 1, $first->id);
    InventoryHelper::stockUp($product, $this->stock, 1, $second->id);

    $operation = InventoryHelper::delivery($this->warehouse, [[$product, 2]]);

    Inventory::confirmTransfer($operation);

    $move = $operation->refresh()->moves->first()->refresh();

    expect($move->state)->toBe(MoveState::ASSIGNED)
        ->and($move->lines)->toHaveCount(2)
        ->and(InventoryHelper::lineQuantities($move))->toBe([1.0, 1.0])
        ->and($move->lines->pluck('lot_id')->all())->toBe([$first->id, $second->id]);
});

it('never reserves a fractional quantity of a serial tracked product', function () {
    InventoryHelper::trackLots($this->warehouse->outType);

    $product = InventoryHelper::serialTrackedProduct();
    $lot = InventoryHelper::lot($product, 'SN-1');

    InventoryHelper::stockUp($product, $this->stock, 1, $lot->id);

    $operation = InventoryHelper::delivery($this->warehouse, [[$product, 0.5]]);

    Inventory::confirmTransfer($operation);

    $move = $operation->refresh()->moves->first();

    expect((float) $move->quantity)->toBe(0.0)
        ->and($move->lines)->toHaveCount(0);
});

it('packs an entire disposable package into a package level on confirm', function () {
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

it('packs an entire reusable package without setting a result package', function () {
    $package = InventoryHelper::package(PackageUse::REUSABLE, $this->stock);

    InventoryHelper::stockUp($this->product, $this->stock, 10, null, $package->id);

    $operation = InventoryHelper::delivery($this->warehouse, [[$this->product, 10]]);

    Inventory::confirmTransfer($operation);

    $operation->refresh();
    $line = $operation->moves->first()->lines->first();

    expect($operation->packageLevels)->toHaveCount(1)
        ->and($line->package_level_id)->not->toBeNull()
        ->and($line->result_package_id)->toBeNull();
});

it('does not pack a package when only part of it is moved', function () {
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

it('takes the goods out of the package when a partial package is delivered', function () {
    $package = InventoryHelper::package(PackageUse::DISPOSABLE, $this->stock);

    InventoryHelper::stockUp($this->product, $this->stock, 10, null, $package->id);

    $operation = InventoryHelper::delivery($this->warehouse, [[$this->product, 4]]);

    Inventory::confirmTransfer($operation);

    Inventory::doneTransfer($operation->refresh());

    expect(InventoryHelper::onHand($this->product, $this->stock))->toBe(6.0);

    $customer = $operation->refresh()->destinationLocation;

    expect(InventoryHelper::onHand($this->product, $customer))->toBe(4.0);
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

it('releases the freed reservation when the delivery move line quantity is decreased', function () {
    $operation = confirmedOneStepDelivery($this->warehouse, $this->product, 10, 10);

    $operation->moves->first()->lines->first()->update(['qty' => 6]);

    $move = $operation->refresh()->moves->first()->refresh();

    expect((float) $move->quantity)->toBe(6.0)
        ->and($move->state)->toBe(MoveState::PARTIALLY_ASSIGNED);

    expect(InventoryHelper::reserved($this->product, $this->stock))->toBe(6.0);
});

it('reserves more when the delivery move line quantity is increased and stock is available', function () {
    $operation = confirmedOneStepDelivery($this->warehouse, $this->product, 4, 10);

    InventoryHelper::quantOf($this->product, $this->stock)->update(['quantity' => 10]);

    $operation->refresh()->moves->first()->lines->first()->update(['qty' => 10]);

    $move = $operation->refresh()->moves->first()->refresh();

    expect((float) $move->quantity)->toBe(10.0)
        ->and($move->state)->toBe(MoveState::ASSIGNED);

    expect(InventoryHelper::reserved($this->product, $this->stock))->toBe(10.0);
});

it('refuses to set a negative reserved quantity on the delivery move line', function () {
    $operation = confirmedOneStepDelivery($this->warehouse, $this->product, 10, 10);

    expect(fn () => $operation->moves->first()->lines->first()->update(['qty' => -1]))
        ->toThrow(Exception::class, __('inventories::system.move-line.negative-quantity-not-allowed'));
});

it('releases the reservation when a reserved delivery move line is deleted', function () {
    $operation = confirmedOneStepDelivery($this->warehouse, $this->product, 10, 10);

    $move = $operation->moves->first();

    $move->lines->first()->delete();

    $move = $move->refresh();

    expect($move->lines)->toHaveCount(0)
        ->and((float) $move->quantity)->toBe(0.0)
        ->and($move->state)->toBe(MoveState::CONFIRMED);

    expect(InventoryHelper::reserved($this->product, $this->stock))->toBe(0.0);
});

it('releases the reservation when a reserved delivery move is deleted', function () {
    $operation = confirmedOneStepDelivery($this->warehouse, $this->product, 10, 10);

    $operation->moves->first()->delete();

    expect($operation->refresh()->moves)->toHaveCount(0);

    expect(InventoryHelper::reserved($this->product, $this->stock))->toBe(0.0);
});

it('releases a fractional reservation when a delivery move line is deleted', function () {
    $operation = confirmedOneStepDelivery($this->warehouse, $this->product, 0.5, 0.5);

    expect(InventoryHelper::reserved($this->product, $this->stock))->toBe(0.5);

    $operation->moves->first()->lines->first()->delete();

    expect(InventoryHelper::reserved($this->product, $this->stock))->toBe(0.0);
});

it('deletes the source quant record when the delivery empties the location', function () {
    $operation = validatedOneStepDelivery($this->warehouse, $this->product, 10, 10);

    expect($operation->state)->toBe(OperationState::DONE)
        ->and(InventoryHelper::quantOf($this->product, $this->stock))->toBeNull();
});

it('keeps the source quant with the remainder after a partial delivery', function () {
    InventoryHelper::stockUp($this->product, $this->stock, 10);

    $operation = InventoryHelper::delivery($this->warehouse, [[$this->product, 4]]);

    Inventory::confirmTransfer($operation);

    Inventory::doneTransfer($operation->refresh());

    $quant = InventoryHelper::quantOf($this->product, $this->stock);

    expect($quant)->not->toBeNull()
        ->and((float) $quant->quantity)->toBe(6.0);
});

it('does not resync reservation when a done move line quantity is edited', function () {
    $operation = validatedOneStepDelivery($this->warehouse, $this->product, 10, 10);

    expect(InventoryHelper::reserved($this->product, $this->stock))->toBe(0.0);

    $line = $operation->moves->first()->lines->first();

    $line->update(['qty' => 4]);

    expect($operation->refresh()->moves->first()->state)->toBe(MoveState::DONE)
        ->and(InventoryHelper::reserved($this->product, $this->stock))->toBe(0.0);
});

it('does not auto reserve a delivery when the operation type uses manual reservation', function () {
    InventoryHelper::setReservationMethod($this->warehouse->outType, ReservationMethod::MANUAL);

    InventoryHelper::stockUp($this->product, $this->stock, 10);

    $operation = InventoryHelper::delivery($this->warehouse, [[$this->product, 10]]);

    Inventory::confirmTransfer($operation);

    expect($operation->refresh()->moves->first()->state)->toBe(MoveState::CONFIRMED)
        ->and(InventoryHelper::reserved($this->product, $this->stock))->toBe(0.0);

    Inventory::assignTransfer($operation->refresh());

    expect($operation->refresh()->moves->first()->state)->toBe(MoveState::ASSIGNED)
        ->and(InventoryHelper::reserved($this->product, $this->stock))->toBe(10.0);
});

it('delivers more than the demand when the picked quantity is increased', function () {
    InventoryHelper::stockUp($this->product, $this->stock, 15);

    $operation = InventoryHelper::delivery($this->warehouse, [[$this->product, 10]]);

    Inventory::confirmTransfer($operation);

    InventoryHelper::pick($operation->refresh()->moves->first(), 12);

    Inventory::doneTransfer($operation->refresh());

    $customer = $operation->refresh()->destinationLocation;

    expect(InventoryHelper::onHand($this->product, $this->stock))->toBe(3.0)
        ->and(InventoryHelper::onHand($this->product, $customer))->toBe(12.0);
});

it('reserves the product uom equivalent when the delivery demand is in dozens', function () {
    InventoryHelper::stockUp($this->product, $this->stock, 12);

    $operation = InventoryHelper::delivery($this->warehouse, [[$this->product, 1, InventoryHelper::dozensUom()]]);

    Inventory::confirmTransfer($operation);

    $move = $operation->refresh()->moves->first();

    expect((float) $move->product_uom_qty)->toBe(1.0)
        ->and((float) $move->product_qty)->toBe(12.0);

    expect(InventoryHelper::reserved($this->product, $this->stock))->toBe(12.0);

    Inventory::doneTransfer($operation->refresh());

    expect(InventoryHelper::onHand($this->product, $this->stock))->toBe(0.0);
});

it('throws when only part of a source package is moved while keeping the same result package', function () {
    $package = InventoryHelper::package(PackageUse::REUSABLE, $this->stock, typed: false);

    InventoryHelper::stockUp($this->product, $this->stock, 10, null, $package->id);

    $operation = InventoryHelper::delivery($this->warehouse, [[$this->product, 5]]);

    Inventory::confirmTransfer($operation);

    $move = $operation->refresh()->moves->first();

    $move->lines->first()->update(['result_package_id' => $package->id]);

    InventoryHelper::pick($move, 5);

    expect(fn () => Inventory::doneTransfer($operation->refresh()))
        ->toThrow(Exception::class, __('inventories::filament/clusters/operations/actions/validate.notification.warning.partial-package.body'));
});
