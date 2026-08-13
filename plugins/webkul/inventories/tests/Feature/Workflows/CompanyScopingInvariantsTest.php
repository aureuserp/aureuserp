<?php

use Webkul\Inventory\Models\ProductQuantity;

require_once __DIR__.'/../../../../support/tests/Helpers/CompanyScopeHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';

$plugin = 'inventories';

$shared = [
    ProductQuantity::class,
];

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('inventories');
});

it('finds the models of this plugin that opt into company scoping', function () use ($plugin) {
    expect(CompanyScopeHelper::companyModels($plugin))->not->toBeEmpty();
});

it('stamps the active company on every model that is not declared shared', function () use ($plugin, $shared) {
    expect(CompanyScopeHelper::unexpectedlyShared($plugin, $shared))->toBe([]);
});

it('keeps every model declared shared free of an automatic company', function () use ($shared) {
    expect(CompanyScopeHelper::unexpectedlyScoped($shared))->toBe([]);
});

it('lets every model declared shared hold no company', function () use ($shared) {
    expect(CompanyScopeHelper::withNonNullableCompanyColumn($shared))->toBe([]);
});
