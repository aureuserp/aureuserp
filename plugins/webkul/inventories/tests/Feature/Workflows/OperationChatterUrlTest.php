<?php

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../Helpers/InventoryHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('inventories');

    InventoryHelper::actingAsAdmin();

    InventoryHelper::enableLocations();
});

it('builds a chatter url for a transfer', function () {
    $warehouse = InventoryHelper::warehouse();
    $product = InventoryHelper::product();
    $stock = $warehouse->lotStockLocation;
    $shelf = InventoryHelper::sublocation($stock, 'Shelf A');

    $operation = InventoryHelper::internalTransfer($warehouse, $stock, $shelf, [[$product, 1]]);

    expect($operation->getChatterResourceUrl())->toContain("/{$operation->id}/view");
});
