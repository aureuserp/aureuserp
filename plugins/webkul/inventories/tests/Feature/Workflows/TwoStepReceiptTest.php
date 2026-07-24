<?php

use Webkul\Inventory\Enums\LocationType;
use Webkul\Inventory\Enums\MoveState;
use Webkul\Inventory\Enums\OperationState;
use Webkul\Inventory\Enums\ProcureMethod;
use Webkul\Inventory\Enums\ReceptionStep;
use Webkul\Inventory\Facades\Inventory;
use Webkul\Inventory\Models\Operation;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../Helpers/InventoryHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('inventories');

    InventoryHelper::actingAsAdmin();

    $this->warehouse = InventoryHelper::warehouse(ReceptionStep::TWO_STEPS);
    $this->product = InventoryHelper::product();
    $this->stock = $this->warehouse->lotStockLocation;
    $this->input = $this->warehouse->inputStockLocation;
});

function storageOperation($warehouse): ?Operation
{
    return Operation::query()
        ->where('operation_type_id', $warehouse->store_type_id)
        ->whereNull('back_order_id')
        ->first();
}

function validatedReceiptLeg($warehouse, $product, float $demand, ?float $picked = null): Operation
{
    $operation = InventoryHelper::receipt($warehouse, [[$product, $demand]]);

    Inventory::confirmTransfer($operation);

    if ($picked !== null) {
        InventoryHelper::pick($operation->refresh()->moves->first(), $picked);
    }

    Inventory::doneTransfer($operation->refresh());

    return $operation->refresh();
}

it('routes the receipt leg from the supplier into the input location', function () {
    $operation = InventoryHelper::receipt($this->warehouse, [[$this->product, 10]]);

    expect($operation->operation_type_id)->toBe($this->warehouse->in_type_id)
        ->and($operation->sourceLocation->type)->toBe(LocationType::SUPPLIER)
        ->and($operation->destination_location_id)->toBe($this->input->id);
});

it('assigns the receipt leg on confirm because the supplier bypasses reservation', function () {
    $operation = InventoryHelper::receipt($this->warehouse, [[$this->product, 10]]);

    Inventory::confirmTransfer($operation);

    $move = $operation->refresh()->moves->first();

    expect($operation->refresh()->state)->toBe(OperationState::ASSIGNED)
        ->and($move->state)->toBe(MoveState::ASSIGNED)
        ->and((float) $move->quantity)->toBe(10.0);
});

it('creates no storage operation before the receipt leg is validated', function () {
    $operation = InventoryHelper::receipt($this->warehouse, [[$this->product, 10]]);

    Inventory::confirmTransfer($operation);

    expect(storageOperation($this->warehouse))->toBeNull();
});

it('lands the received quantity in input and not in stock', function () {
    validatedReceiptLeg($this->warehouse, $this->product, 10);

    expect(InventoryHelper::onHand($this->product, $this->input))->toBe(10.0)
        ->and(InventoryHelper::onHand($this->product, $this->stock))->toBe(0.0);
});

it('pushes a storage operation from input to stock when the receipt leg is validated', function () {
    validatedReceiptLeg($this->warehouse, $this->product, 10);

    $storage = storageOperation($this->warehouse);

    expect($storage)->not->toBeNull()
        ->and($storage->moves)->toHaveCount(1);

    $move = $storage->moves->first();

    expect($move->operation_type_id)->toBe($this->warehouse->store_type_id)
        ->and($move->source_location_id)->toBe($this->input->id)
        ->and($move->destination_location_id)->toBe($this->stock->id)
        ->and($move->procure_method)->toBe(ProcureMethod::MAKE_TO_ORDER)
        ->and((float) $move->product_uom_qty)->toBe(10.0);
});

it('links the storage move back to the receipt move as its origin', function () {
    $operation = validatedReceiptLeg($this->warehouse, $this->product, 10);

    $receiptMove = $operation->moves->first();
    $storageMove = storageOperation($this->warehouse)->moves->first();

    expect($receiptMove->moveDestinations->pluck('id')->all())->toContain($storageMove->id)
        ->and($storageMove->moveOrigins->pluck('id')->all())->toContain($receiptMove->id);
});

it('reserves the storage move against the input location', function () {
    validatedReceiptLeg($this->warehouse, $this->product, 10);

    $move = storageOperation($this->warehouse)->moves->first();

    expect($move->state)->toBe(MoveState::ASSIGNED)
        ->and((float) $move->quantity)->toBe(10.0)
        ->and($move->lines)->toHaveCount(1);

    expect(InventoryHelper::reserved($this->product, $this->input))->toBe(10.0);
});

it('moves the quantity from input into stock when the storage leg is validated', function () {
    validatedReceiptLeg($this->warehouse, $this->product, 10);

    $storage = storageOperation($this->warehouse);

    Inventory::doneTransfer($storage->refresh());

    expect($storage->refresh()->state)->toBe(OperationState::DONE)
        ->and(InventoryHelper::onHand($this->product, $this->input))->toBe(0.0)
        ->and(InventoryHelper::onHand($this->product, $this->stock))->toBe(10.0)
        ->and(InventoryHelper::reserved($this->product, $this->input))->toBe(0.0);
});

it('stops pushing once the goods reach stock', function () {
    validatedReceiptLeg($this->warehouse, $this->product, 10);

    $storage = storageOperation($this->warehouse);

    Inventory::doneTransfer($storage->refresh());

    expect($storage->refresh()->moves->first()->moveDestinations)->toHaveCount(0)
        ->and(InventoryHelper::operationCount($this->warehouse))->toBe(2);
});

it('backorders the receipt leg and pushes only the received quantity', function () {
    $operation = validatedReceiptLeg($this->warehouse, $this->product, 10, 4);

    $backorder = InventoryHelper::backorderOf($operation);

    expect($backorder)->not->toBeNull()
        ->and($backorder->operation_type_id)->toBe($this->warehouse->in_type_id)
        ->and((float) $backorder->moves->first()->product_uom_qty)->toBe(6.0);

    expect((float) storageOperation($this->warehouse)->moves->first()->product_uom_qty)->toBe(4.0)
        ->and(InventoryHelper::onHand($this->product, $this->input))->toBe(4.0);
});

it('merges the second push into the existing storage move when the receipt backorder is validated', function () {
    $operation = validatedReceiptLeg($this->warehouse, $this->product, 10, 4);

    $backorder = InventoryHelper::backorderOf($operation);

    Inventory::doneTransfer($backorder->refresh());

    $storage = storageOperation($this->warehouse);

    expect($storage->refresh()->moves)->toHaveCount(1)
        ->and((float) $storage->moves->first()->product_uom_qty)->toBe(10.0);

    expect(InventoryHelper::onHand($this->product, $this->input))->toBe(10.0)
        ->and(InventoryHelper::reserved($this->product, $this->input))->toBe(10.0);
});

it('creates no receipt backorder when validating with cancelBackOrder', function () {
    $operation = InventoryHelper::receipt($this->warehouse, [[$this->product, 10]]);

    Inventory::confirmTransfer($operation);

    InventoryHelper::pick($operation->refresh()->moves->first(), 4);

    Inventory::doneTransfer($operation->refresh(), cancelBackOrder: true);

    expect(InventoryHelper::backorderOf($operation->refresh()))->toBeNull()
        ->and((float) storageOperation($this->warehouse)->moves->first()->product_uom_qty)->toBe(4.0);
});

it('backorders the storage leg when it is partially validated', function () {
    validatedReceiptLeg($this->warehouse, $this->product, 10);

    $storage = storageOperation($this->warehouse);

    InventoryHelper::pick($storage->refresh()->moves->first(), 4);

    Inventory::doneTransfer($storage->refresh());

    $backorder = InventoryHelper::backorderOf($storage->refresh());

    expect($backorder)->not->toBeNull()
        ->and($backorder->operation_type_id)->toBe($this->warehouse->store_type_id)
        ->and((float) $backorder->moves->first()->product_uom_qty)->toBe(6.0);

    expect(InventoryHelper::onHand($this->product, $this->stock))->toBe(4.0)
        ->and(InventoryHelper::onHand($this->product, $this->input))->toBe(6.0);
});

it('cancels the receipt leg and pushes nothing', function () {
    $operation = InventoryHelper::receipt($this->warehouse, [[$this->product, 10]]);

    Inventory::confirmTransfer($operation);

    Inventory::cancelTransfer($operation->refresh());

    expect($operation->refresh()->state)->toBe(OperationState::CANCELED)
        ->and($operation->moves->first()->state)->toBe(MoveState::CANCELED)
        ->and(storageOperation($this->warehouse))->toBeNull()
        ->and(InventoryHelper::onHand($this->product, $this->input))->toBe(0.0);
});

it('cancels the storage leg and releases the input reservation', function () {
    validatedReceiptLeg($this->warehouse, $this->product, 10);

    $storage = storageOperation($this->warehouse);

    Inventory::cancelTransfer($storage->refresh());

    expect($storage->refresh()->state)->toBe(OperationState::CANCELED)
        ->and($storage->moves->first()->state)->toBe(MoveState::CANCELED);

    expect(InventoryHelper::onHand($this->product, $this->input))->toBe(10.0)
        ->and(InventoryHelper::reserved($this->product, $this->input))->toBe(0.0);
});

it('unreserves the storage move and drops it back to confirmed', function () {
    validatedReceiptLeg($this->warehouse, $this->product, 10);

    $storage = storageOperation($this->warehouse);

    Inventory::unreserveMoves($storage->refresh()->moves);

    $move = $storage->refresh()->moves->first();

    expect($move->state)->toBe(MoveState::CONFIRMED)
        ->and((float) $move->quantity)->toBe(0.0)
        ->and($move->lines)->toHaveCount(0);

    expect(InventoryHelper::reserved($this->product, $this->input))->toBe(0.0)
        ->and(InventoryHelper::onHand($this->product, $this->input))->toBe(10.0);
});

it('returns the receipt leg back to the supplier and unreserves the storage move', function () {
    $operation = validatedReceiptLeg($this->warehouse, $this->product, 10);

    $storageMove = storageOperation($this->warehouse)->moves->first();

    expect($storageMove->state)->toBe(MoveState::ASSIGNED);

    $return = Inventory::returnTransfer($operation, [$operation->moves->first()->id => 4]);

    expect($return->return_id)->toBe($operation->id)
        ->and($return->source_location_id)->toBe($this->input->id)
        ->and($return->destinationLocation->type)->toBe(LocationType::SUPPLIER);

    expect($storageMove->refresh()->state)->not->toBe(MoveState::ASSIGNED)
        ->and((float) $storageMove->quantity)->toBe(0.0);
});

it('removes the returned quantity from input when the receipt return is validated', function () {
    $operation = validatedReceiptLeg($this->warehouse, $this->product, 10);

    $return = Inventory::returnTransfer($operation, [$operation->moves->first()->id => 4]);

    Inventory::doneTransfer($return->refresh());

    expect($return->refresh()->state)->toBe(OperationState::DONE)
        ->and(InventoryHelper::onHand($this->product, $this->input))->toBe(6.0);
});

it('returns the storage leg from stock back into input', function () {
    validatedReceiptLeg($this->warehouse, $this->product, 10);

    $storage = storageOperation($this->warehouse);

    Inventory::doneTransfer($storage->refresh());

    $storage->refresh();

    $return = Inventory::returnTransfer($storage, [$storage->moves->first()->id => 3]);

    expect($return->return_id)->toBe($storage->id)
        ->and($return->source_location_id)->toBe($this->stock->id)
        ->and($return->destination_location_id)->toBe($this->input->id);

    Inventory::doneTransfer($return->refresh());

    expect(InventoryHelper::onHand($this->product, $this->stock))->toBe(7.0)
        ->and(InventoryHelper::onHand($this->product, $this->input))->toBe(3.0);
});

it('receives more than the demanded quantity and pushes the whole received amount', function () {
    validatedReceiptLeg($this->warehouse, $this->product, 10, 12);

    expect(InventoryHelper::onHand($this->product, $this->input))->toBe(12.0)
        ->and((float) storageOperation($this->warehouse)->moves->first()->product_uom_qty)->toBe(12.0);
});

it('creates one storage move per product on a multi product receipt', function () {
    $other = InventoryHelper::product();

    $operation = InventoryHelper::receipt($this->warehouse, [
        [$this->product, 10],
        [$other, 4],
    ]);

    Inventory::confirmTransfer($operation);

    Inventory::doneTransfer($operation->refresh());

    $storage = storageOperation($this->warehouse);

    expect($storage->moves)->toHaveCount(2);

    $byProduct = $storage->moves->keyBy('product_id');

    expect((float) $byProduct[$this->product->id]->product_uom_qty)->toBe(10.0)
        ->and((float) $byProduct[$other->id]->product_uom_qty)->toBe(4.0);

    expect(InventoryHelper::onHand($this->product, $this->input))->toBe(10.0)
        ->and(InventoryHelper::onHand($other, $this->input))->toBe(4.0);
});

it('pushes a decimal received quantity through to storage', function () {
    validatedReceiptLeg($this->warehouse, $this->product, 2.5, 1.25);

    expect((float) storageOperation($this->warehouse)->moves->first()->product_uom_qty)->toBe(1.25)
        ->and(InventoryHelper::onHand($this->product, $this->input))->toBe(1.25);
});

it('pushes a dozen received as twelve units through to storage', function () {
    $operation = InventoryHelper::receipt($this->warehouse, [
        [$this->product, 1, InventoryHelper::dozensUom()],
    ]);

    Inventory::confirmTransfer($operation);

    Inventory::doneTransfer($operation->refresh());

    expect(InventoryHelper::onHand($this->product, $this->input))->toBe(12.0)
        ->and((float) storageOperation($this->warehouse)->moves->first()->product_qty)->toBe(12.0);
});

it('creates the lot at the receipt leg and carries it into stock through storage', function () {
    InventoryHelper::trackLots($this->warehouse->inType);

    $product = InventoryHelper::lotTrackedProduct();

    $operation = InventoryHelper::receipt($this->warehouse, [[$product, 10]]);

    Inventory::confirmTransfer($operation);

    InventoryHelper::nameLines($operation->refresh()->moves->first(), ['LOT-A']);

    Inventory::doneTransfer($operation->refresh());

    expect(InventoryHelper::lotsOf($product))->toBe(['LOT-A'])
        ->and(InventoryHelper::onHand($product, $this->input))->toBe(10.0);

    $storage = storageOperation($this->warehouse);
    $storageLine = $storage->moves->first()->lines->first();

    expect($storageLine->lot_id)->not->toBeNull();

    Inventory::doneTransfer($storage->refresh());

    $stockQuant = InventoryHelper::quantOf($product, $this->stock, $storageLine->lot_id);

    expect(InventoryHelper::onHand($product, $this->input))->toBe(0.0)
        ->and((float) $stockQuant->quantity)->toBe(10.0);
});

it('keeps two lots apart as they flow through the storage leg into stock', function () {
    InventoryHelper::trackLots($this->warehouse->inType);

    $product = InventoryHelper::lotTrackedProduct();

    foreach (['LOT-A' => 10, 'LOT-B' => 4] as $name => $qty) {
        $operation = InventoryHelper::receipt($this->warehouse, [[$product, $qty]]);

        Inventory::confirmTransfer($operation);

        InventoryHelper::nameLines($operation->refresh()->moves->first(), [$name]);

        Inventory::doneTransfer($operation->refresh());
    }

    Operation::query()
        ->where('operation_type_id', $this->warehouse->store_type_id)
        ->get()
        ->each(fn ($storage) => Inventory::doneTransfer($storage->refresh()));

    expect(InventoryHelper::lotsOf($product))->toBe(['LOT-A', 'LOT-B'])
        ->and(InventoryHelper::onHand($product, $this->stock))->toBe(14.0)
        ->and(InventoryHelper::onHand($product, $this->input))->toBe(0.0);
});

it('explodes a serial tracked receipt leg into one move line per unit', function () {
    InventoryHelper::trackLots($this->warehouse->inType);

    $product = InventoryHelper::serialTrackedProduct();

    $operation = InventoryHelper::receipt($this->warehouse, [[$product, 3]]);

    Inventory::confirmTransfer($operation);

    $move = $operation->refresh()->moves->first();

    expect($move->lines)->toHaveCount(3)
        ->and(InventoryHelper::lineQuantities($move))->toBe([1.0, 1.0, 1.0]);
});

it('stores each serial number in stock through the whole chain', function () {
    InventoryHelper::trackLots($this->warehouse->inType);
    InventoryHelper::trackLots($this->warehouse->storeType);

    $product = InventoryHelper::serialTrackedProduct();

    $operation = InventoryHelper::receipt($this->warehouse, [[$product, 3]]);

    Inventory::confirmTransfer($operation);

    InventoryHelper::nameLines($operation->refresh()->moves->first(), ['SN-1', 'SN-2', 'SN-3']);

    Inventory::doneTransfer($operation->refresh());

    $storage = storageOperation($this->warehouse);

    expect($storage->moves->first()->lines)->toHaveCount(3);

    Inventory::doneTransfer($storage->refresh());

    expect(InventoryHelper::lotsOf($product))->toBe(['SN-1', 'SN-2', 'SN-3'])
        ->and(InventoryHelper::onHand($product, $this->stock))->toBe(3.0)
        ->and(InventoryHelper::onHand($product, $this->input))->toBe(0.0);
});

it('releases the freed reservation when the storage move line quantity is decreased', function () {
    validatedReceiptLeg($this->warehouse, $this->product, 10);

    $storage = storageOperation($this->warehouse);

    $storage->moves->first()->lines->first()->update(['qty' => 6]);

    $move = $storage->refresh()->moves->first()->refresh();

    expect((float) $move->quantity)->toBe(6.0)
        ->and($move->state)->toBe(MoveState::PARTIALLY_ASSIGNED);

    expect(InventoryHelper::reserved($this->product, $this->input))->toBe(6.0);
});

it('releases the reservation when a reserved storage move line is deleted', function () {
    validatedReceiptLeg($this->warehouse, $this->product, 10);

    $storage = storageOperation($this->warehouse);

    $storage->moves->first()->lines->first()->delete();

    $move = $storage->refresh()->moves->first()->refresh();

    expect($move->lines)->toHaveCount(0)
        ->and($move->state)->toBe(MoveState::CONFIRMED);

    expect(InventoryHelper::reserved($this->product, $this->input))->toBe(0.0);
});

it('deletes the input quant when the storage leg empties it', function () {
    validatedReceiptLeg($this->warehouse, $this->product, 10);

    Inventory::doneTransfer(storageOperation($this->warehouse)->refresh());

    expect(InventoryHelper::quantOf($this->product, $this->input))->toBeNull()
        ->and((float) InventoryHelper::quantOf($this->product, $this->stock)?->quantity)->toBe(10.0);
});

it('redirects the storage move line to the putaway sublocation', function () {
    $shelf = InventoryHelper::sublocation($this->stock, 'Shelf A');

    InventoryHelper::putawayRule($this->stock, $shelf, $this->product);

    validatedReceiptLeg($this->warehouse, $this->product, 10);

    $storageLine = storageOperation($this->warehouse)->moves->first()->lines->first();

    expect($storageLine->destination_location_id)->toBe($shelf->id);
});

it('lands the stored quantity in the putaway sublocation when validated', function () {
    $shelf = InventoryHelper::sublocation($this->stock, 'Shelf A');

    InventoryHelper::putawayRule($this->stock, $shelf, $this->product);

    validatedReceiptLeg($this->warehouse, $this->product, 10);

    Inventory::doneTransfer(storageOperation($this->warehouse)->refresh());

    expect((float) InventoryHelper::quantOf($this->product, $shelf)?->quantity)->toBe(10.0)
        ->and(InventoryHelper::quantOf($this->product, $this->stock))->toBeNull();
});

it('ignores a putaway rule that targets a different product', function () {
    $shelf = InventoryHelper::sublocation($this->stock, 'Shelf A');

    InventoryHelper::putawayRule($this->stock, $shelf, InventoryHelper::product());

    validatedReceiptLeg($this->warehouse, $this->product, 10);

    $storageLine = storageOperation($this->warehouse)->moves->first()->lines->first();

    expect($storageLine->destination_location_id)->toBe($this->stock->id);
});

it('applies a product agnostic putaway rule to any product', function () {
    $shelf = InventoryHelper::sublocation($this->stock, 'Shelf A');

    InventoryHelper::putawayRule($this->stock, $shelf);

    validatedReceiptLeg($this->warehouse, $this->product, 10);

    $storageLine = storageOperation($this->warehouse)->moves->first()->lines->first();

    expect($storageLine->destination_location_id)->toBe($shelf->id);
});


it('consolidates goods into a storage category location that has spare capacity', function () {
    $category = InventoryHelper::storageCategory();
    $shelf = InventoryHelper::sublocation($this->stock, 'Cat Shelf', $category);

    InventoryHelper::storageCategoryCapacity($category, $this->product, 5);

    InventoryHelper::stockUp($this->product, $shelf, 2);

    InventoryHelper::putawayRule($this->stock, $this->stock, null, $category);

    validatedReceiptLeg($this->warehouse, $this->product, 3);

    $storageLine = storageOperation($this->warehouse)->moves->first()->lines->first();

    expect($storageLine->destination_location_id)->toBe($shelf->id);
});

it('skips a storage category location when the incoming quantity exceeds its capacity', function () {
    $category = InventoryHelper::storageCategory();
    $shelf = InventoryHelper::sublocation($this->stock, 'Cat Shelf', $category);

    InventoryHelper::storageCategoryCapacity($category, $this->product, 5);

    InventoryHelper::stockUp($this->product, $shelf, 2);

    InventoryHelper::putawayRule($this->stock, $this->stock, null, $category);

    validatedReceiptLeg($this->warehouse, $this->product, 4);

    $storageLine = storageOperation($this->warehouse)->moves->first()->lines->first();

    expect($storageLine->destination_location_id)->toBe($this->stock->id);
});
