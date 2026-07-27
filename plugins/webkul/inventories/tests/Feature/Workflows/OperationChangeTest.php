<?php

use Webkul\Inventory\Enums\MoveState;
use Webkul\Inventory\Enums\ProcureMethod;
use Webkul\Inventory\Facades\Inventory;
use Webkul\Inventory\Models\OperationType;

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
    $this->shelfB = InventoryHelper::sublocation($this->stock, 'Shelf B');

    $this->otherWarehouse = InventoryHelper::warehouse();
    $this->otherStock = $this->otherWarehouse->lotStockLocation;
});

it('removes the reserved move line when the source location changes to a location without stock', function () {
    InventoryHelper::stockUp($this->product, $this->stock, 10);

    $operation = InventoryHelper::internalTransfer($this->warehouse, $this->stock, $this->shelf, [[$this->product, 10]]);

    Inventory::confirmTransfer($operation);

    expect($operation->refresh()->moves->first()->lines)->toHaveCount(1);

    $operation->update(['source_location_id' => $this->otherStock->id]);

    $move = $operation->refresh()->moves->first();

    expect($move->lines)->toHaveCount(0)
        ->and($move->state)->toBe(MoveState::CONFIRMED)
        ->and((float) $move->quantity)->toBe(0.0);
});

it('releases the reservation on the old location when the source location changes away', function () {
    InventoryHelper::stockUp($this->product, $this->stock, 10);

    $operation = InventoryHelper::internalTransfer($this->warehouse, $this->stock, $this->shelf, [[$this->product, 10]]);

    Inventory::confirmTransfer($operation);

    expect(InventoryHelper::reserved($this->product, $this->stock))->toBe(10.0);

    $operation->update(['source_location_id' => $this->otherStock->id]);

    expect(InventoryHelper::reserved($this->product, $this->stock))->toBe(0.0);
});

it('re-reserves the move when the source location changes back to a location with stock', function () {
    InventoryHelper::stockUp($this->product, $this->stock, 10);

    $operation = InventoryHelper::internalTransfer($this->warehouse, $this->stock, $this->shelf, [[$this->product, 10]]);

    Inventory::confirmTransfer($operation);

    $operation->update(['source_location_id' => $this->otherStock->id]);

    expect($operation->refresh()->moves->first()->lines)->toHaveCount(0);

    $operation->update(['source_location_id' => $this->stock->id]);

    $move = $operation->refresh()->moves->first();

    expect($move->lines)->toHaveCount(1)
        ->and($move->state)->toBe(MoveState::ASSIGNED)
        ->and((float) $move->quantity)->toBe(10.0)
        ->and(InventoryHelper::reserved($this->product, $this->stock))->toBe(10.0);
});

it('reserves at the new source location when that location has the stock', function () {
    InventoryHelper::stockUp($this->product, $this->otherStock, 10);

    $operation = InventoryHelper::internalTransfer($this->warehouse, $this->stock, $this->shelf, [[$this->product, 10]]);

    Inventory::confirmTransfer($operation);

    expect($operation->refresh()->moves->first()->state)->toBe(MoveState::CONFIRMED);

    $operation->update(['source_location_id' => $this->otherStock->id]);

    $move = $operation->refresh()->moves->first();

    expect($move->lines)->toHaveCount(1)
        ->and($move->state)->toBe(MoveState::ASSIGNED)
        ->and(InventoryHelper::reserved($this->product, $this->otherStock))->toBe(10.0);
});

it('keeps the move line when the new source location is a parent of the line location', function () {
    InventoryHelper::stockUp($this->product, $this->shelf, 10);

    $operation = InventoryHelper::internalTransfer($this->warehouse, $this->shelf, $this->shelfB, [[$this->product, 10]]);

    Inventory::confirmTransfer($operation);

    $lineId = $operation->refresh()->moves->first()->lines->first()->id;

    $operation->update(['source_location_id' => $this->stock->id]);

    $move = $operation->refresh()->moves->first();

    expect($move->lines)->toHaveCount(1)
        ->and($move->lines->first()->id)->toBe($lineId)
        ->and($move->state)->toBe(MoveState::ASSIGNED)
        ->and(InventoryHelper::reserved($this->product, $this->shelf))->toBe(10.0);
});

it('switches the move to make to stock and drops origin links when the reservation is dropped', function () {
    InventoryHelper::stockUp($this->product, $this->stock, 10);

    $operation = InventoryHelper::internalTransfer($this->warehouse, $this->stock, $this->shelf, [[$this->product, 10]]);

    Inventory::confirmTransfer($operation);

    $originOperation = InventoryHelper::internalTransfer($this->warehouse, $this->stock, $this->shelf, [[$this->product, 10]]);

    $move = $operation->refresh()->moves->first();

    $move->update(['procure_method' => ProcureMethod::MAKE_TO_ORDER]);

    $move->moveOrigins()->attach($originOperation->moves->first()->id);

    $operation->update(['source_location_id' => $this->otherStock->id]);

    $move = $move->refresh();

    expect($move->procure_method)->toBe(ProcureMethod::MAKE_TO_STOCK)
        ->and($move->moveOrigins)->toHaveCount(0);
});

it('applies the same source location handling to a delivery order', function () {
    InventoryHelper::stockUp($this->product, $this->stock, 10);

    $operation = InventoryHelper::delivery($this->warehouse, [[$this->product, 10]]);

    Inventory::confirmTransfer($operation);

    expect($operation->refresh()->moves->first()->lines)->toHaveCount(1);

    $operation->update(['source_location_id' => $this->otherStock->id]);

    $move = $operation->refresh()->moves->first();

    expect($move->lines)->toHaveCount(0)
        ->and($move->state)->toBe(MoveState::CONFIRMED)
        ->and(InventoryHelper::reserved($this->product, $this->stock))->toBe(0.0);
});

it('leaves the move line alone when the source location is saved without changing', function () {
    InventoryHelper::stockUp($this->product, $this->stock, 10);

    $operation = InventoryHelper::internalTransfer($this->warehouse, $this->stock, $this->shelf, [[$this->product, 10]]);

    Inventory::confirmTransfer($operation);

    $lineId = $operation->refresh()->moves->first()->lines->first()->id;

    $operation->update(['source_location_id' => $this->stock->id]);

    $move = $operation->refresh()->moves->first();

    expect($move->lines)->toHaveCount(1)
        ->and($move->lines->first()->id)->toBe($lineId)
        ->and($move->state)->toBe(MoveState::ASSIGNED);
});

it('does not propagate the operation source location to scrapped moves', function () {
    InventoryHelper::stockUp($this->product, $this->stock, 10);

    $operation = InventoryHelper::internalTransfer($this->warehouse, $this->stock, $this->shelf, [[$this->product, 10]]);

    Inventory::confirmTransfer($operation);

    $move = $operation->refresh()->moves->first();

    $move->is_scraped = true;

    $move->saveQuietly();

    $operation->update(['source_location_id' => $this->otherStock->id]);

    expect($move->refresh()->source_location_id)->toBe($this->stock->id);
});

it('propagates the operation destination location to its moves', function () {
    InventoryHelper::stockUp($this->product, $this->stock, 10);

    $operation = InventoryHelper::internalTransfer($this->warehouse, $this->stock, $this->shelf, [[$this->product, 10]]);

    Inventory::confirmTransfer($operation);

    $operation->update(['destination_location_id' => $this->shelfB->id]);

    expect($operation->refresh()->moves->first()->destination_location_id)->toBe($this->shelfB->id);
});

it('keeps the reservation intact when only the destination location changes', function () {
    InventoryHelper::stockUp($this->product, $this->stock, 10);

    $operation = InventoryHelper::internalTransfer($this->warehouse, $this->stock, $this->shelf, [[$this->product, 10]]);

    Inventory::confirmTransfer($operation);

    $lineId = $operation->refresh()->moves->first()->lines->first()->id;

    $operation->update(['destination_location_id' => $this->shelfB->id]);

    $move = $operation->refresh()->moves->first();

    expect($move->lines)->toHaveCount(1)
        ->and($move->lines->first()->id)->toBe($lineId)
        ->and($move->state)->toBe(MoveState::ASSIGNED)
        ->and((float) $move->quantity)->toBe(10.0)
        ->and(InventoryHelper::reserved($this->product, $this->stock))->toBe(10.0);
});

it('keeps the reservation when the destination location moves to another warehouse', function () {
    InventoryHelper::stockUp($this->product, $this->stock, 10);

    $operation = InventoryHelper::internalTransfer($this->warehouse, $this->stock, $this->shelf, [[$this->product, 10]]);

    Inventory::confirmTransfer($operation);

    $operation->update(['destination_location_id' => $this->otherStock->id]);

    $move = $operation->refresh()->moves->first();

    expect($move->lines)->toHaveCount(1)
        ->and($move->state)->toBe(MoveState::ASSIGNED)
        ->and(InventoryHelper::reserved($this->product, $this->stock))->toBe(10.0);
});

it('reserves against the unchanged source when both locations change at once', function () {
    InventoryHelper::stockUp($this->product, $this->otherStock, 10);

    $operation = InventoryHelper::internalTransfer($this->warehouse, $this->stock, $this->shelf, [[$this->product, 10]]);

    Inventory::confirmTransfer($operation);

    expect($operation->refresh()->moves->first()->state)->toBe(MoveState::CONFIRMED);

    $operation->update([
        'source_location_id'      => $this->otherStock->id,
        'destination_location_id' => $this->shelfB->id,
    ]);

    $move = $operation->refresh()->moves->first();

    expect($move->source_location_id)->toBe($this->otherStock->id)
        ->and($move->destination_location_id)->toBe($this->shelfB->id)
        ->and($move->lines)->toHaveCount(1)
        ->and($move->state)->toBe(MoveState::ASSIGNED)
        ->and(InventoryHelper::reserved($this->product, $this->otherStock))->toBe(10.0);
});

it('propagates the destination location on a receipt', function () {
    $operation = InventoryHelper::receipt($this->warehouse, [[$this->product, 10]]);

    Inventory::confirmTransfer($operation);

    $operation->update(['destination_location_id' => $this->shelf->id]);

    expect($operation->refresh()->moves->first()->destination_location_id)->toBe($this->shelf->id);
});

it('does not propagate the operation destination location to scrapped moves', function () {
    InventoryHelper::stockUp($this->product, $this->stock, 10);

    $operation = InventoryHelper::internalTransfer($this->warehouse, $this->stock, $this->shelf, [[$this->product, 10]]);

    Inventory::confirmTransfer($operation);

    $move = $operation->refresh()->moves->first();

    $move->is_scraped = true;

    $move->saveQuietly();

    $operation->update(['destination_location_id' => $this->shelfB->id]);

    expect($move->refresh()->destination_location_id)->toBe($this->shelf->id);
});

it('releases the old reservation when the operation type changes to another warehouse', function () {
    InventoryHelper::stockUp($this->product, $this->stock, 10);

    $operation = InventoryHelper::internalTransfer($this->warehouse, $this->stock, $this->shelf, [[$this->product, 10]]);

    Inventory::confirmTransfer($operation);

    expect(InventoryHelper::reserved($this->product, $this->stock))->toBe(10.0);

    $otherType = OperationType::findOrFail($this->otherWarehouse->internal_type_id);

    $operation->update([
        'operation_type_id'       => $otherType->id,
        'source_location_id'      => $otherType->source_location_id,
        'destination_location_id' => $otherType->destination_location_id,
    ]);

    $move = $operation->refresh()->moves->first();

    expect($move->source_location_id)->toBe($otherType->source_location_id)
        ->and($move->lines)->toHaveCount(0)
        ->and($move->state)->toBe(MoveState::CONFIRMED)
        ->and(InventoryHelper::reserved($this->product, $this->stock))->toBe(0.0);
});

it('reserves at the new warehouse when the operation type changes to one holding stock', function () {
    InventoryHelper::stockUp($this->product, $this->otherStock, 10);

    $operation = InventoryHelper::internalTransfer($this->warehouse, $this->stock, $this->shelf, [[$this->product, 10]]);

    Inventory::confirmTransfer($operation);

    expect($operation->refresh()->moves->first()->state)->toBe(MoveState::CONFIRMED);

    $otherType = OperationType::findOrFail($this->otherWarehouse->internal_type_id);

    $operation->update([
        'operation_type_id'       => $otherType->id,
        'source_location_id'      => $otherType->source_location_id,
        'destination_location_id' => $otherType->destination_location_id,
    ]);

    $move = $operation->refresh()->moves->first();

    expect($move->lines)->toHaveCount(1)
        ->and($move->state)->toBe(MoveState::ASSIGNED)
        ->and(InventoryHelper::reserved($this->product, $this->otherStock))->toBe(10.0);
});

it('releases the reservation when the operation type changes to a delivery in the same warehouse', function () {
    InventoryHelper::stockUp($this->product, $this->shelf, 10);

    $operation = InventoryHelper::internalTransfer($this->warehouse, $this->shelf, $this->shelfB, [[$this->product, 10]]);

    Inventory::confirmTransfer($operation);

    expect(InventoryHelper::reserved($this->product, $this->shelf))->toBe(10.0);

    $outType = OperationType::findOrFail($this->warehouse->out_type_id);

    $operation->update([
        'operation_type_id'       => $outType->id,
        'source_location_id'      => $outType->source_location_id,
        'destination_location_id' => $outType->destination_location_id,
    ]);

    $move = $operation->refresh()->moves->first();

    expect($move->source_location_id)->toBe($outType->source_location_id)
        ->and($move->destination_location_id)->toBe($outType->destination_location_id);
});

it('re-derives the move warehouse when the source location moves to another warehouse', function () {
    InventoryHelper::stockUp($this->product, $this->stock, 10);

    $operation = InventoryHelper::internalTransfer($this->warehouse, $this->stock, $this->shelf, [[$this->product, 10]]);

    Inventory::confirmTransfer($operation);

    $operation->update(['source_location_id' => $this->otherStock->id]);

    expect($operation->refresh()->moves->first()->warehouse_id)->toBe($this->otherWarehouse->id);
});

it('re-derives the move warehouse when the operation type changes to another warehouse', function () {
    InventoryHelper::stockUp($this->product, $this->stock, 10);

    $operation = InventoryHelper::internalTransfer($this->warehouse, $this->stock, $this->shelf, [[$this->product, 10]]);

    Inventory::confirmTransfer($operation);

    expect($operation->refresh()->moves->first()->warehouse_id)->toBe($this->warehouse->id);

    $otherType = OperationType::findOrFail($this->otherWarehouse->internal_type_id);

    $operation->update([
        'operation_type_id'       => $otherType->id,
        'source_location_id'      => $otherType->source_location_id,
        'destination_location_id' => $otherType->destination_location_id,
    ]);

    expect($operation->refresh()->moves->first()->warehouse_id)->toBe($this->otherWarehouse->id);
});

it('releases the reservation on the old location when a move line source location is edited', function () {
    InventoryHelper::stockUp($this->product, $this->shelf, 10);
    InventoryHelper::stockUp($this->product, $this->shelfB, 10);

    $operation = InventoryHelper::internalTransfer($this->warehouse, $this->stock, $this->shelf, [[$this->product, 10]]);

    Inventory::confirmTransfer($operation);

    $line = $operation->refresh()->moves->first()->lines->first();

    expect(InventoryHelper::reserved($this->product, $this->shelf))->toBe(10.0);

    $line->update(['source_location_id' => $this->shelfB->id]);

    expect(InventoryHelper::reserved($this->product, $this->shelf))->toBe(0.0)
        ->and(InventoryHelper::reserved($this->product, $this->shelfB))->toBe(10.0);
});

it('releases the reservation on the old lot when a move line lot is edited', function () {
    $product = InventoryHelper::lotTrackedProduct();

    InventoryHelper::trackLots(OperationType::findOrFail($this->warehouse->internal_type_id));

    $lotA = InventoryHelper::lot($product, 'LOT-A');
    $lotB = InventoryHelper::lot($product, 'LOT-B');

    InventoryHelper::stockUp($product, $this->stock, 10, $lotA->id);
    InventoryHelper::stockUp($product, $this->stock, 10, $lotB->id);

    $operation = InventoryHelper::internalTransfer($this->warehouse, $this->stock, $this->shelf, [[$product, 10]]);

    Inventory::confirmTransfer($operation);

    $line = $operation->refresh()->moves->first()->lines->first();

    expect($line->lot_id)->toBe($lotA->id)
        ->and((float) InventoryHelper::quantOf($product, $this->stock, $lotA->id)->reserved_quantity)->toBe(10.0);

    $line->update(['lot_id' => $lotB->id]);

    expect((float) InventoryHelper::quantOf($product, $this->stock, $lotA->id)->reserved_quantity)->toBe(0.0)
        ->and((float) InventoryHelper::quantOf($product, $this->stock, $lotB->id)->reserved_quantity)->toBe(10.0);
});

it('releases the reservation on the old lot when the lot and source location are edited together', function () {
    $product = InventoryHelper::lotTrackedProduct();

    InventoryHelper::trackLots(OperationType::findOrFail($this->warehouse->internal_type_id));

    $lotA = InventoryHelper::lot($product, 'LOT-A');
    $lotB = InventoryHelper::lot($product, 'LOT-B');

    InventoryHelper::stockUp($product, $this->shelf, 10, $lotA->id);
    InventoryHelper::stockUp($product, $this->shelfB, 10, $lotB->id);

    $operation = InventoryHelper::internalTransfer($this->warehouse, $this->shelf, $this->stock, [[$product, 10]]);

    Inventory::confirmTransfer($operation);

    $line = $operation->refresh()->moves->first()->lines->first();

    expect((float) InventoryHelper::quantOf($product, $this->shelf, $lotA->id)->reserved_quantity)->toBe(10.0);

    $line->update([
        'source_location_id' => $this->shelfB->id,
        'lot_id'             => $lotB->id,
    ]);

    expect((float) InventoryHelper::quantOf($product, $this->shelf, $lotA->id)->reserved_quantity)->toBe(0.0)
        ->and((float) InventoryHelper::quantOf($product, $this->shelfB, $lotB->id)->reserved_quantity)->toBe(10.0);
});

it('derives the locations from the new operation type when only the operation type changes', function () {
    InventoryHelper::stockUp($this->product, $this->stock, 10);

    $operation = InventoryHelper::internalTransfer($this->warehouse, $this->stock, $this->shelf, [[$this->product, 10]]);

    Inventory::confirmTransfer($operation);

    $otherType = OperationType::findOrFail($this->otherWarehouse->internal_type_id);

    $operation->update(['operation_type_id' => $otherType->id]);

    $operation = $operation->refresh();

    expect($operation->source_location_id)->toBe($otherType->source_location_id)
        ->and($operation->destination_location_id)->toBe($otherType->destination_location_id)
        ->and($operation->moves->first()->source_location_id)->toBe($otherType->source_location_id);
});
