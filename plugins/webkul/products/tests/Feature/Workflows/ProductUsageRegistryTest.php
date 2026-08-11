<?php

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Webkul\Inventory\Models\Lot;
use Webkul\Product\Models\Product;
use Webkul\Product\Support\ProductUsageRegistry;

class UsageModelWithoutTable extends Model
{
    protected $table = 'a_table_that_does_not_exist';
}

require_once __DIR__.'/../../../../support/tests/Helpers/CompanyHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('products');
    TestBootstrapHelper::ensurePluginInstalled('inventories');
    SecurityHelper::disableUserEvents();

    // The registry is static and filled at boot by every installed plugin. Snapshot it
    // so these tests can register in isolation without leaking into the rest of the suite.
    $this->registeredModels = ProductUsageRegistry::models();
});

afterEach(function () {
    ProductUsageRegistry::flush();
    ProductUsageRegistry::register(...$this->registeredModels);

    SecurityHelper::restoreUserEvents();
});

it('registers each model only once', function () {
    ProductUsageRegistry::flush();

    ProductUsageRegistry::register(Lot::class, Lot::class);
    ProductUsageRegistry::register(Lot::class);

    expect(ProductUsageRegistry::models())->toBe([Lot::class]);
});

it('rejects anything that is not an eloquent model', function () {
    ProductUsageRegistry::register(stdClass::class);
})->throws(InvalidArgumentException::class);

it('reports a product as unused without querying anything when nothing is registered', function () {
    ProductUsageRegistry::flush();

    $product = Product::factory()->create();

    DB::enableQueryLog();
    DB::flushQueryLog();

    $inUse = $product->isInUse();

    $queries = DB::getQueryLog();

    DB::disableQueryLog();

    expect($inUse)->toBeFalse()
        ->and($queries)->toBeEmpty();
});

it('reports a product as in use when a registered model references it', function () {
    ProductUsageRegistry::flush();
    ProductUsageRegistry::register(Lot::class);

    $product = Product::factory()->create();

    expect($product->isInUse())->toBeFalse();

    Lot::factory()->create(['product_id' => $product->id]);

    expect($product->fresh()->isInUse())->toBeTrue();
});

it('skips a registered model whose table is missing', function () {
    ProductUsageRegistry::flush();
    ProductUsageRegistry::register(UsageModelWithoutTable::class, Lot::class);

    $product = Product::factory()->create();

    Lot::factory()->create(['product_id' => $product->id]);

    // A plugin uninstalled after boot, or a partially migrated database, must not
    // take the whole check down with a "table doesn't exist" query exception.
    expect($product->fresh()->isInUse())->toBeTrue();
});

it('reports a product as in use even when the document belongs to another company', function () {
    ProductUsageRegistry::flush();
    ProductUsageRegistry::register(Lot::class);

    $companyA = CompanyHelper::company();
    $companyB = CompanyHelper::company();

    $product = Product::factory()->create();

    CompanyHelper::actingAsCompanyUser($companyB);

    $lot = Lot::factory()->create([
        'product_id' => $product->id,
        'company_id' => $companyB->id,
    ]);

    CompanyHelper::actingAsCompanyUser($companyA);

    // Company A cannot see the lot, but the product is still in use: generating
    // variants on it would corrupt company B's document.
    expect(Lot::query()->pluck('id'))->not->toContain($lot->id)
        ->and($product->fresh()->isInUse())->toBeTrue();
});
