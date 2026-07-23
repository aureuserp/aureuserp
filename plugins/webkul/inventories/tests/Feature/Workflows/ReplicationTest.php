<?php

use Webkul\Inventory\Enums\MoveState;
use Webkul\Inventory\Enums\OperationState;
use Webkul\Inventory\Facades\Inventory;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../Helpers/InventoryHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('inventories');

    InventoryHelper::actingAsAdmin();

    $this->warehouse = InventoryHelper::warehouse();
    $this->product = InventoryHelper::product();
    $this->stock = $this->warehouse->lotStockLocation;
});

function replicationDelivery($warehouse, $product, float $stock, float $demand, ?float $picked = null)
{
    InventoryHelper::stockUp($product, $warehouse->lotStockLocation, $stock);

    $operation = InventoryHelper::delivery($warehouse, [[$product, $demand]]);

    Inventory::confirmTransfer($operation);

    if ($picked !== null) {
        InventoryHelper::pick($operation->refresh()->moves->first(), $picked);
    }

    Inventory::doneTransfer($operation->refresh());

    return $operation->refresh();
}

it('does not carry the source completion timestamp onto a return', function () {
    $operation = replicationDelivery($this->warehouse, $this->product, 10, 10);

    expect($operation->closed_at)->not->toBeNull();

    $return = Inventory::returnTransfer($operation, [$operation->moves->first()->id => 3]);

    expect($return->refresh()->closed_at)->toBeNull();
});

it('does not carry the source completion timestamp onto a backorder', function () {
    $operation = replicationDelivery($this->warehouse, $this->product, 10, 10, 4);

    $backorder = InventoryHelper::backorderOf($operation);

    expect($backorder)->not->toBeNull()
        ->and($backorder->closed_at)->toBeNull();
});

it('does not carry the printed flag onto a return', function () {
    $operation = replicationDelivery($this->warehouse, $this->product, 10, 10);

    $operation->is_printed = true;

    $operation->saveQuietly();

    $return = Inventory::returnTransfer($operation->refresh(), [$operation->moves->first()->id => 3]);

    expect($return->refresh()->is_printed)->toBeFalse();
});

it('does not carry the printed flag onto a backorder', function () {
    InventoryHelper::stockUp($this->product, $this->stock, 10);

    $operation = InventoryHelper::delivery($this->warehouse, [[$this->product, 10]]);

    Inventory::confirmTransfer($operation);

    $operation->is_printed = true;

    $operation->saveQuietly();

    InventoryHelper::pick($operation->refresh()->moves->first(), 4);

    Inventory::doneTransfer($operation->refresh());

    expect(InventoryHelper::backorderOf($operation)->is_printed)->toBeFalse();
});

it('does not point a return of a backorder at the original as its backorder parent', function () {
    $operation = replicationDelivery($this->warehouse, $this->product, 10, 10, 4);

    $backorder = InventoryHelper::backorderOf($operation);

    InventoryHelper::pick($backorder->refresh()->moves->first(), 6);

    Inventory::doneTransfer($backorder->refresh());

    $return = Inventory::returnTransfer($backorder->refresh(), [$backorder->moves->first()->id => 2]);

    expect($return->refresh()->back_order_id)->toBeNull();
});

it('does not carry a return link onto a backorder of a return', function () {
    $operation = replicationDelivery($this->warehouse, $this->product, 10, 10);

    $return = Inventory::returnTransfer($operation, [$operation->moves->first()->id => 6]);

    InventoryHelper::pick($return->refresh()->moves->first(), 2);

    Inventory::doneTransfer($return->refresh());

    $backorder = InventoryHelper::backorderOf($return);

    expect($backorder)->not->toBeNull()
        ->and($backorder->return_id)->toBeNull();
});

it('schedules a backorder for today instead of inheriting the original schedule', function () {
    InventoryHelper::stockUp($this->product, $this->stock, 10);

    $operation = InventoryHelper::delivery($this->warehouse, [[$this->product, 10]]);

    Inventory::confirmTransfer($operation);

    $operation->scheduled_at = now()->subDays(10);

    $operation->saveQuietly();

    InventoryHelper::pick($operation->refresh()->moves->first(), 4);

    Inventory::doneTransfer($operation->refresh());

    expect(InventoryHelper::backorderOf($operation)->scheduled_at->isToday())->toBeTrue();
});

it('does not carry the source deadline onto a returned move', function () {
    $operation = replicationDelivery($this->warehouse, $this->product, 10, 10);

    $sourceMove = $operation->refresh()->moves->first();

    $sourceMove->deadline = now()->subDays(5);

    $sourceMove->saveQuietly();

    $return = Inventory::returnTransfer($operation->refresh(), [$sourceMove->id => 3]);

    expect($return->refresh()->moves->first()->deadline)->toBeNull();
});

it('does not carry the source unit price onto a returned move', function () {
    $operation = replicationDelivery($this->warehouse, $this->product, 10, 10);

    $sourceMove = $operation->refresh()->moves->first();

    $sourceMove->price_unit = 42.5;

    $sourceMove->saveQuietly();

    $return = Inventory::returnTransfer($operation->refresh(), [$sourceMove->id => 3]);

    expect((float) $return->refresh()->moves->first()->price_unit)->toBe(0.0);
});

it('leaves a returned operation out of the backorder lookup for the original', function () {
    $operation = replicationDelivery($this->warehouse, $this->product, 10, 10, 4);

    $backorder = InventoryHelper::backorderOf($operation);

    InventoryHelper::pick($backorder->refresh()->moves->first(), 6);

    Inventory::doneTransfer($backorder->refresh());

    Inventory::returnTransfer($backorder->refresh(), [$backorder->moves->first()->id => 2]);

    expect(InventoryHelper::backorderOf($operation)->id)->toBe($backorder->id);
});

it('re-derives the real quantity when the move demand is updated', function () {
    InventoryHelper::stockUp($this->product, $this->stock, 20);

    $operation = InventoryHelper::delivery($this->warehouse, [[$this->product, 10]]);

    $move = $operation->refresh()->moves->first();

    expect((float) $move->product_qty)->toBe(10.0);

    $move->update(['product_uom_qty' => 20]);

    expect((float) $move->refresh()->product_qty)->toBe(20.0);
});

it('re-derives the real quantity when the move uom is changed to dozens', function () {
    $operation = InventoryHelper::operation(
        $this->warehouse->outType,
        [[$this->product, 1, InventoryHelper::dozensUom()]]
    );

    $move = $operation->refresh()->moves->first();

    expect((float) $move->product_uom_qty)->toBe(1.0)
        ->and((float) $move->product_qty)->toBe(12.0);

    $move->update(['product_uom_qty' => 2]);

    expect((float) $move->refresh()->product_qty)->toBe(24.0);
});

it('reserves only once when availability is checked twice on an assigned move', function () {
    InventoryHelper::stockUp($this->product, $this->stock, 10);

    $operation = InventoryHelper::delivery($this->warehouse, [[$this->product, 10]]);

    Inventory::confirmTransfer($operation);

    expect(InventoryHelper::reserved($this->product, $this->stock))->toBe(10.0);

    Inventory::assignTransfer($operation->refresh());

    $move = $operation->refresh()->moves->first();

    expect(InventoryHelper::reserved($this->product, $this->stock))->toBe(10.0)
        ->and($move->lines)->toHaveCount(1)
        ->and((float) $move->quantity)->toBe(10.0);
});

it('does not reserve the same stock for two competing deliveries', function () {
    InventoryHelper::stockUp($this->product, $this->stock, 10);

    $first = InventoryHelper::delivery($this->warehouse, [[$this->product, 10]]);

    Inventory::confirmTransfer($first);

    $second = InventoryHelper::delivery($this->warehouse, [[$this->product, 10]]);

    Inventory::confirmTransfer($second);

    expect(InventoryHelper::reserved($this->product, $this->stock))->toBe(10.0)
        ->and($first->refresh()->moves->first()->state)->toBe(MoveState::ASSIGNED)
        ->and($second->refresh()->moves->first()->state)->toBe(MoveState::CONFIRMED);
});

it('never reserves more than the quantity on hand', function () {
    InventoryHelper::stockUp($this->product, $this->stock, 10);

    $operation = InventoryHelper::delivery($this->warehouse, [[$this->product, 25]]);

    Inventory::confirmTransfer($operation);

    $quant = InventoryHelper::quantOf($this->product, $this->stock);

    expect((float) $quant->reserved_quantity)->toBeLessThanOrEqual((float) $quant->quantity)
        ->and((float) $quant->reserved_quantity)->toBe(10.0)
        ->and($operation->refresh()->moves->first()->state)->toBe(MoveState::PARTIALLY_ASSIGNED);
});

it('keeps a counted quant with a zero quantity instead of sweeping it', function () {
    $quant = InventoryHelper::stockUp($this->product, $this->stock, 0);

    $quant->update(['user_id' => InventoryHelper::actingAsAdmin()->id]);

    InventoryHelper::stockUp($this->product, $this->warehouse->lotStockLocation, 5);

    expect(InventoryHelper::quantOf($this->product, $this->stock))->not->toBeNull();
});

it('returns the reserved quantity to stock when a return of a return is created', function () {
    $operation = replicationDelivery($this->warehouse, $this->product, 10, 10);

    $return = Inventory::returnTransfer($operation, [$operation->moves->first()->id => 4]);

    Inventory::doneTransfer($return->refresh());

    expect(InventoryHelper::onHand($this->product, $this->stock))->toBe(4.0);

    $secondReturn = Inventory::returnTransfer($return->refresh(), [$return->moves->first()->id => 4]);

    expect($secondReturn->refresh()->return_id)->toBe($return->id);

    Inventory::doneTransfer($secondReturn->refresh());

    expect(InventoryHelper::onHand($this->product, $this->stock))->toBe(0.0);
});

it('returns a delivery twice without exceeding the delivered quantity', function () {
    $operation = replicationDelivery($this->warehouse, $this->product, 10, 10);

    $move = $operation->moves->first();

    $firstReturn = Inventory::returnTransfer($operation, [$move->id => 4]);

    Inventory::doneTransfer($firstReturn->refresh());

    $secondReturn = Inventory::returnTransfer($operation->refresh(), [$move->id => 6]);

    Inventory::doneTransfer($secondReturn->refresh());

    expect(InventoryHelper::onHand($this->product, $this->stock))->toBe(10.0)
        ->and(InventoryHelper::returnOf($operation))->not->toBeNull();
});

it('splits a returned move across the locations that hold the stock', function () {
    $shelfA = InventoryHelper::sublocation($this->stock, 'Shelf A');
    $shelfB = InventoryHelper::sublocation($this->stock, 'Shelf B');

    InventoryHelper::stockUp($this->product, $shelfA, 5);
    InventoryHelper::stockUp($this->product, $shelfB, 5);

    $operation = InventoryHelper::delivery($this->warehouse, [[$this->product, 10]]);

    Inventory::confirmTransfer($operation);

    expect($operation->refresh()->moves->first()->lines)->toHaveCount(2);

    Inventory::doneTransfer($operation->refresh());

    $return = Inventory::returnTransfer($operation->refresh(), [$operation->moves->first()->id => 6]);

    expect($return->refresh()->moves->first()->lines)->toHaveCount(1);
});

it('carries the split across to the backorder when a split move is partially picked', function () {
    $shelfA = InventoryHelper::sublocation($this->stock, 'Shelf A');
    $shelfB = InventoryHelper::sublocation($this->stock, 'Shelf B');

    InventoryHelper::stockUp($this->product, $shelfA, 5);
    InventoryHelper::stockUp($this->product, $shelfB, 5);

    $operation = InventoryHelper::delivery($this->warehouse, [[$this->product, 10]]);

    Inventory::confirmTransfer($operation);

    InventoryHelper::pick($operation->refresh()->moves->first(), 4);

    Inventory::doneTransfer($operation->refresh());

    $backorder = InventoryHelper::backorderOf($operation);

    expect($backorder)->not->toBeNull()
        ->and((float) $backorder->moves->first()->product_uom_qty)->toBe(6.0)
        ->and($operation->refresh()->state)->toBe(OperationState::DONE);
});
