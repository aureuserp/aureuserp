<?php

use Illuminate\Support\Facades\DB;
use Webkul\PointOfSale\Enums\TaxDisplay;
use Webkul\PointOfSale\Services\CatalogLoader;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../inventories/tests/Helpers/InventoryHelper.php';
require_once __DIR__.'/../../Helpers/PosHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('inventories');
    TestBootstrapHelper::ensurePluginInstalled('accounts');
    TestBootstrapHelper::ensurePluginInstalled('point-of-sale');

    InventoryHelper::actingAsAdmin();

    $this->warehouse = InventoryHelper::warehouse();
    $this->config = PosHelper::configWithCashMethod($this->warehouse);
});

it('loads only products flagged available in pos', function () {
    $available = InventoryHelper::product(['name' => 'Counter Coffee', 'price' => 3.0]);
    InventoryHelper::product(['name' => 'Back Office Service', 'price' => 50.0]);

    DB::table('products_products')->where('id', $available->id)->update(['available_in_pos' => true]);

    $catalog = app(CatalogLoader::class)->load($this->config);

    $names = collect($catalog['products'])->pluck('name');

    expect($names)->toContain('Counter Coffee')
        ->and($names)->not->toContain('Back Office Service');
});

it('lists a configurable template and flags it for the configurator', function () {
    $template = InventoryHelper::product(['name' => 'Configurable Shirt', 'price' => 20.0]);

    DB::table('products_products')->where('id', $template->id)->update([
        'available_in_pos' => true,
        'is_configurable'  => true,
    ]);

    $catalog = app(CatalogLoader::class)->load($this->config);

    $listed = collect($catalog['products'])->firstWhere('name', 'Configurable Shirt');

    expect($listed)->not->toBeNull()
        ->and($listed['is_configurable'])->toBeTrue();
});

it('keeps the variants of a template out of the catalogue', function () {
    $template = InventoryHelper::product(['name' => 'Configurable Shirt', 'price' => 20.0]);

    $variant = InventoryHelper::product(['name' => 'Configurable Shirt - S', 'price' => 20.0]);

    DB::table('products_products')->where('id', $template->id)->update([
        'available_in_pos' => true,
        'is_configurable'  => true,
    ]);

    DB::table('products_products')->where('id', $variant->id)->update([
        'available_in_pos' => true,
        'parent_id'        => $template->id,
    ]);

    $catalog = app(CatalogLoader::class)->load($this->config);

    $names = collect($catalog['products'])->pluck('name');

    expect($names)->toContain('Configurable Shirt')
        ->not->toContain('Configurable Shirt - S');
});

it('carries the terminal display settings into the catalogue payload', function () {
    $this->config->update([
        'tax_display'              => TaxDisplay::TOTAL,
        'show_product_images'      => false,
        'show_category_images'     => false,
        'enable_customer_required' => true,
        'receipt_header'           => 'Webkul Store',
        'receipt_footer'           => 'Thanks for shopping',
    ]);

    $catalog = app(CatalogLoader::class)->load($this->config->refresh());

    expect($catalog['config']['tax_display'])->toBe(TaxDisplay::TOTAL)
        ->and($catalog['config']['show_product_images'])->toBeFalse()
        ->and($catalog['config']['show_category_images'])->toBeFalse()
        ->and($catalog['config']['enable_customer_required'])->toBeTrue()
        ->and($catalog['config']['receipt_header'])->toBe('Webkul Store')
        ->and($catalog['config']['receipt_footer'])->toBe('Thanks for shopping');
});

it('offers only the categories picked on the terminal when they are restricted', function () {
    $allowed = PosHelper::posCategory(['name' => 'Drinks']);

    PosHelper::posCategory(['name' => 'Back Office Only']);

    $this->config->update(['limit_categories' => true]);

    $this->config->categories()->sync([$allowed->id]);

    $catalog = app(CatalogLoader::class)->load($this->config->refresh());

    $names = collect($catalog['categories'])->pluck('name');

    expect($names)->toContain('Drinks')
        ->and($names)->not->toContain('Back Office Only');
});

it('falls back to every category when the terminal restricts but picks none', function () {
    PosHelper::posCategory(['name' => 'Drinks']);

    PosHelper::posCategory(['name' => 'Back Office Only']);

    $this->config->update(['limit_categories' => true]);

    $this->config->categories()->sync([]);

    $catalog = app(CatalogLoader::class)->load($this->config->refresh());

    $names = collect($catalog['categories'])->pluck('name');

    expect($names)->toContain('Drinks')
        ->and($names)->toContain('Back Office Only');
});

it('caps the catalogue at the configured product limit', function () {
    foreach (range(1, 3) as $index) {
        $product = InventoryHelper::product(['name' => "Counter Item {$index}", 'price' => 5.0]);

        DB::table('products_products')->where('id', $product->id)->update(['available_in_pos' => true]);
    }

    $this->config->update(['limited_products_amount' => 2]);

    $catalog = app(CatalogLoader::class)->load($this->config->refresh());

    expect($catalog['products'])->toHaveCount(2);
});

it('offers only the products of the picked categories when they are restricted', function () {
    $drinks = PosHelper::posCategory(['name' => 'Drinks']);
    $backOffice = PosHelper::posCategory(['name' => 'Back Office Only']);

    $coffee = InventoryHelper::product(['name' => 'Counter Coffee', 'price' => 3.0]);
    $stapler = InventoryHelper::product(['name' => 'Office Stapler', 'price' => 9.0]);
    $uncategorised = InventoryHelper::product(['name' => 'Loose Item', 'price' => 1.0]);

    DB::table('products_products')
        ->whereIn('id', [$coffee->id, $stapler->id, $uncategorised->id])
        ->update(['available_in_pos' => true]);

    $coffee->posCategories()->sync([$drinks->id]);
    $stapler->posCategories()->sync([$backOffice->id]);

    $this->config->update(['limit_categories' => true]);

    $this->config->categories()->sync([$drinks->id]);

    $catalog = app(CatalogLoader::class)->load($this->config->refresh());

    $names = collect($catalog['products'])->pluck('name');

    expect($names)->toContain('Counter Coffee')
        ->and($names)->not->toContain('Office Stapler')
        ->and($names)->not->toContain('Loose Item');
});
