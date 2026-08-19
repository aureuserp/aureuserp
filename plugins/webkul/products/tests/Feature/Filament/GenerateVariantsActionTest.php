<?php

use Filament\Actions\Testing\TestAction;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Exceptions;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Webkul\Invoice\Filament\Clusters\Customers\Resources\ProductResource;
use Webkul\Invoice\Filament\Clusters\Customers\Resources\ProductResource\Pages\ManageAttributes;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Package;
use Webkul\Product\Models\Attribute;
use Webkul\Product\Models\AttributeOption;
use Webkul\Product\Models\Product;
use Webkul\Product\Models\ProductAttribute;
use Webkul\Product\Models\ProductAttributeValue;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/FilamentHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('products');
    // The product form on this page renders inventory routes.
    TestBootstrapHelper::ensurePluginInstalled('inventories');
    TestBootstrapHelper::ensurePluginInstalled('invoices');

    Package::$plugins = Plugin::all()->keyBy('name');

    URL::resolveMissingNamedRoutesUsing(fn () => '#');

    FilamentHelper::actingAs([
        'view_any_invoice_product',
        'view_invoice_product',
        'update_invoice_product',
    ]);
});

function productReadyForVariants(): Product
{
    $product = Product::factory()->create(['price' => 0, 'cost' => 0]);

    $attribute = Attribute::factory()->create();

    $productAttribute = ProductAttribute::factory()->create([
        'product_id'   => $product->id,
        'attribute_id' => $attribute->id,
    ]);

    $option = AttributeOption::factory()->create([
        'attribute_id' => $attribute->id,
        'name'         => 'Small',
        'extra_price'  => 0,
    ]);

    ProductAttributeValue::create([
        'product_id'           => $product->id,
        'attribute_id'         => $attribute->id,
        'product_attribute_id' => $productAttribute->id,
        'attribute_option_id'  => $option->id,
        'extra_price'          => 0,
    ]);

    return $product;
}

it('reports the underlying failure when variant generation breaks', function () {
    Exceptions::fake();

    $product = productReadyForVariants();

    Event::listen(
        'eloquent.creating: '.ProductResource::getModel(),
        fn () => throw new RuntimeException('variant creation blew up'),
    );

    Livewire::test(ManageAttributes::class, ['record' => $product->getRouteKey()])
        ->callAction(TestAction::make('products.generate.variants')->table())
        ->assertNotified();

    Exceptions::assertReported(
        fn (RuntimeException $exception): bool => $exception->getMessage() === 'variant creation blew up',
    );
});

it('notifies and reports when variant generation raises an error rather than an exception', function () {
    Exceptions::fake();

    $product = productReadyForVariants();

    Event::listen(
        'eloquent.creating: '.ProductResource::getModel(),
        fn () => throw new Error('variant creation exploded'),
    );

    Livewire::test(ManageAttributes::class, ['record' => $product->getRouteKey()])
        ->callAction(TestAction::make('products.generate.variants')->table())
        ->assertNotified();

    Exceptions::assertReported(
        fn (Error $exception): bool => $exception->getMessage() === 'variant creation exploded',
    );
});

it('does not report anything when variant generation succeeds', function () {
    Exceptions::fake();

    $product = productReadyForVariants();

    Livewire::test(ManageAttributes::class, ['record' => $product->getRouteKey()])
        ->callAction(TestAction::make('products.generate.variants')->table())
        ->assertNotified();

    Exceptions::assertNothingReported();

    expect($product->variants()->count())->toBe(1);
});
