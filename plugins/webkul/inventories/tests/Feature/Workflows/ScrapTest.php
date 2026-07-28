<?php

use Webkul\Inventory\Enums\ScrapState;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../Helpers/InventoryHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('inventories');

    InventoryHelper::actingAsAdmin();

    $this->warehouse = InventoryHelper::warehouse();
    $this->product = InventoryHelper::product();
    $this->stock = $this->warehouse->lotStockLocation;
    $this->scrapLocation = InventoryHelper::scrapLocation();
});

it('moves the scrapped quantity out of stock into the scrap location on validate', function () {
    InventoryHelper::stockUp($this->product, $this->stock, 10);

    $scrap = InventoryHelper::scrap($this->product, $this->stock, $this->scrapLocation, 3);

    $scrap->validate();

    expect($scrap->refresh()->state)->toBe(ScrapState::DONE)
        ->and(InventoryHelper::onHand($this->product, $this->stock))->toBe(7.0)
        ->and(InventoryHelper::onHand($this->product, $this->scrapLocation))->toBe(3.0);
});

it('refuses to validate a scrap when the source has insufficient quantity', function () {
    InventoryHelper::stockUp($this->product, $this->stock, 2);

    $scrap = InventoryHelper::scrap($this->product, $this->stock, $this->scrapLocation, 5);

    expect($scrap->validate())->toBeFalse()
        ->and($scrap->refresh()->state)->toBe(ScrapState::DRAFT)
        ->and(InventoryHelper::onHand($this->product, $this->stock))->toBe(2.0);
});

it('records an inventory move for the scrap', function () {
    InventoryHelper::stockUp($this->product, $this->stock, 10);

    $scrap = InventoryHelper::scrap($this->product, $this->stock, $this->scrapLocation, 3);

    $scrap->validate();

    expect(Webkul\Inventory\Models\Move::query()->where('scrap_id', $scrap->id)->exists())->toBeTrue();
});
