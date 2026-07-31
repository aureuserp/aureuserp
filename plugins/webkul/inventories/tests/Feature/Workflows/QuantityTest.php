<?php

use Webkul\Inventory\Enums\MoveState;
use Webkul\Inventory\Models\Move;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../Helpers/InventoryHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('inventories');

    InventoryHelper::actingAsAdmin();

    $this->warehouse = InventoryHelper::warehouse();
    $this->product = InventoryHelper::product();
    $this->stock = $this->warehouse->lotStockLocation;
    $this->adjustment = InventoryHelper::inventoryAdjustmentLocation();
});

function adjustmentMovesFor($product)
{
    return Move::query()
        ->where('is_inventory', true)
        ->where('product_id', $product->id)
        ->get();
}

it('records an inventory move from the adjustment location into stock on a positive adjustment', function () {
    $quant = InventoryHelper::stockUp($this->product, $this->stock, 0);

    InventoryHelper::applyInventoryAdjustment($quant, 10);

    $moves = adjustmentMovesFor($this->product);

    expect($moves)->toHaveCount(1);

    $move = $moves->first();

    expect($move->is_inventory)->toBeTrue()
        ->and($move->state)->toBe(MoveState::DONE)
        ->and($move->is_picked)->toBeTrue()
        ->and((float) $move->quantity)->toBe(10.0)
        ->and($move->source_location_id)->toBe($this->adjustment->id)
        ->and($move->destination_location_id)->toBe($this->stock->id)
        ->and($move->lines)->toHaveCount(1);
});

it('records an inventory move from stock into the adjustment location on a negative adjustment', function () {
    $quant = InventoryHelper::stockUp($this->product, $this->stock, 10);

    InventoryHelper::applyInventoryAdjustment($quant, 4);

    $move = adjustmentMovesFor($this->product)->sortByDesc('id')->first();

    expect((float) $move->quantity)->toBe(6.0)
        ->and($move->source_location_id)->toBe($this->stock->id)
        ->and($move->destination_location_id)->toBe($this->adjustment->id)
        ->and($move->is_inventory)->toBeTrue();
});

it('resets the inventory difference quantity to zero after applying', function () {
    $quant = InventoryHelper::stockUp($this->product, $this->stock, 4);

    InventoryHelper::applyInventoryAdjustment($quant, 10);

    expect((float) $quant->refresh()->inventory_diff_quantity)->toBe(0.0)
        ->and($quant->inventory_quantity_set)->toBeFalse();
});

it('creates no inventory move when the applied quantity matches the current quantity', function () {
    $quant = InventoryHelper::stockUp($this->product, $this->stock, 10);

    InventoryHelper::applyInventoryAdjustment($quant, 10);

    expect(adjustmentMovesFor($this->product))->toHaveCount(0);
});

it('does not create an inventory move when a quant is simply stocked up', function () {
    InventoryHelper::stockUp($this->product, $this->stock, 10);

    expect(adjustmentMovesFor($this->product))->toHaveCount(0);
});

it('leaves the counterpart quantity at the adjustment location', function () {
    $quant = InventoryHelper::stockUp($this->product, $this->stock, 4);

    InventoryHelper::applyInventoryAdjustment($quant, 10);

    expect(InventoryHelper::onHand($this->product, $this->stock))->toBe(10.0)
        ->and(InventoryHelper::onHand($this->product, $this->adjustment))->toBeLessThan(0.0);
});

it('records a history move when inventory is added from the product', function () {
    $quant = InventoryHelper::addProductQuantity($this->product, $this->stock, 10);

    $moves = adjustmentMovesFor($this->product);

    expect($moves)->toHaveCount(1);

    $move = $moves->first();

    expect((float) $move->quantity)->toBe(10.0)
        ->and($move->is_inventory)->toBeTrue()
        ->and($move->source_location_id)->toBe($this->adjustment->id)
        ->and($move->destination_location_id)->toBe($this->stock->id)
        ->and($move->lines)->toHaveCount(1);

    expect(InventoryHelper::onHand($this->product, $this->stock))->toBe(10.0)
        ->and((float) $quant->refresh()->inventory_diff_quantity)->toBe(0.0);
});

it('records an adjustment move when the on hand quantity is edited from the product', function () {
    $quant = InventoryHelper::addProductQuantity($this->product, $this->stock, 10);

    InventoryHelper::editProductQuantity($quant->refresh(), 15);

    $moves = adjustmentMovesFor($this->product)->sortBy('id')->values();

    expect($moves)->toHaveCount(2)
        ->and((float) $moves->last()->quantity)->toBe(5.0)
        ->and($moves->last()->source_location_id)->toBe($this->adjustment->id)
        ->and($moves->last()->destination_location_id)->toBe($this->stock->id);

    expect(InventoryHelper::onHand($this->product, $this->stock))->toBe(15.0);
});

it('records a reverse adjustment move when the on hand quantity is reduced from the product', function () {
    $quant = InventoryHelper::addProductQuantity($this->product, $this->stock, 10);

    InventoryHelper::editProductQuantity($quant->refresh(), 4);

    $move = adjustmentMovesFor($this->product)->sortByDesc('id')->first();

    expect((float) $move->quantity)->toBe(6.0)
        ->and($move->source_location_id)->toBe($this->stock->id)
        ->and($move->destination_location_id)->toBe($this->adjustment->id);

    expect(InventoryHelper::onHand($this->product, $this->stock))->toBe(4.0);
});
