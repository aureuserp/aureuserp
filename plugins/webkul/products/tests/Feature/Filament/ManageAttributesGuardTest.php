<?php

use Filament\Actions\Testing\TestAction;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Webkul\Inventory\Models\Lot;
use Webkul\Invoice\Filament\Clusters\Customers\Resources\ProductResource\Pages\ManageAttributes;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Package;
use Webkul\Product\Models\Attribute;
use Webkul\Product\Models\AttributeOption;
use Webkul\Product\Models\Product;
use Webkul\Product\Models\ProductAttribute;
use Webkul\Product\Models\ProductAttributeValue;
use Webkul\Product\Models\ProductCombination;
use Webkul\Product\Support\ProductUsageRegistry;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/FilamentHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('products');
    TestBootstrapHelper::ensurePluginInstalled('inventories');
    TestBootstrapHelper::ensurePluginInstalled('invoices');

    Package::$plugins = Plugin::all()->keyBy('name');

    URL::resolveMissingNamedRoutesUsing(fn () => '#');

    $this->registeredModels = ProductUsageRegistry::models();

    ProductUsageRegistry::flush();
    ProductUsageRegistry::register(Lot::class);

    FilamentHelper::actingAs([
        'view_any_invoice_product',
        'view_invoice_product',
        'update_invoice_product',
    ]);
});

afterEach(function () {
    ProductUsageRegistry::flush();
    ProductUsageRegistry::register(...$this->registeredModels);
});

/**
 * A configurable product with Small/Medium variants already generated.
 */
function productWithGeneratedVariants(): array
{
    $product = Product::factory()->create(['price' => 0, 'cost' => 0]);

    $attribute = Attribute::factory()->create();

    $productAttribute = ProductAttribute::factory()->create([
        'product_id'   => $product->id,
        'attribute_id' => $attribute->id,
    ]);

    $options = [];

    foreach (['Small', 'Medium'] as $optionName) {
        $option = AttributeOption::factory()->create([
            'attribute_id' => $attribute->id,
            'name'         => $optionName,
            'extra_price'  => 0,
        ]);

        ProductAttributeValue::create([
            'product_id'           => $product->id,
            'attribute_id'         => $attribute->id,
            'product_attribute_id' => $productAttribute->id,
            'attribute_option_id'  => $option->id,
            'extra_price'          => 0,
        ]);

        $options[$optionName] = $option;
    }

    $product->generateVariants();

    return [$product, $productAttribute, $options];
}

function variantForOption(ProductAttribute $productAttribute, AttributeOption $option): Product
{
    $valueId = ProductAttributeValue::query()
        ->where('product_attribute_id', $productAttribute->id)
        ->where('attribute_option_id', $option->id)
        ->value('id');

    $variantId = ProductCombination::query()
        ->where('product_attribute_value_id', $valueId)
        ->value('product_id');

    return Product::withTrashed()->findOrFail($variantId);
}

function valueExistsFor(ProductAttribute $productAttribute, AttributeOption $option): bool
{
    return ProductAttributeValue::query()
        ->where('product_attribute_id', $productAttribute->id)
        ->where('attribute_option_id', $option->id)
        ->exists();
}

it('refuses to drop an attribute value whose variant is in use', function () {
    [$product, $productAttribute, $options] = productWithGeneratedVariants();

    $mediumVariant = variantForOption($productAttribute, $options['Medium']);

    Lot::factory()->create(['product_id' => $mediumVariant->id]);

    Livewire::test(ManageAttributes::class, ['record' => $product->getRouteKey()])
        ->callAction(
            TestAction::make('edit')->table($productAttribute),
            data: ['options' => [$options['Small']->id]],
        )
        ->assertNotified();

    expect(valueExistsFor($productAttribute, $options['Medium']))->toBeTrue();
});

it('still drops an attribute value whose variant is not in use', function () {
    [$product, $productAttribute, $options] = productWithGeneratedVariants();

    Livewire::test(ManageAttributes::class, ['record' => $product->getRouteKey()])
        ->callAction(
            TestAction::make('edit')->table($productAttribute),
            data: ['options' => [$options['Small']->id]],
        );

    expect(valueExistsFor($productAttribute, $options['Medium']))->toBeFalse();
});

/**
 * Jeans with Waist (24, 34, 44) and Length (24, 30) — both attributes carry a "24", and only
 * the "24 / 24" variant is sold.
 */
function jeansFixture(): array
{
    $product = Product::factory()->create(['name' => 'Jeans', 'price' => 0, 'cost' => 0]);

    $rows = [];

    foreach (['Waist' => ['24', '34', '44'], 'Length' => ['24', '30']] as $attributeName => $optionNames) {
        $attribute = Attribute::factory()->create(['name' => $attributeName]);

        $productAttribute = ProductAttribute::factory()->create([
            'product_id'   => $product->id,
            'attribute_id' => $attribute->id,
        ]);

        $options = [];

        foreach ($optionNames as $optionName) {
            $option = AttributeOption::factory()->create([
                'attribute_id' => $attribute->id,
                'name'         => $optionName,
                'extra_price'  => 0,
            ]);

            ProductAttributeValue::create([
                'product_id'           => $product->id,
                'attribute_id'         => $attribute->id,
                'product_attribute_id' => $productAttribute->id,
                'attribute_option_id'  => $option->id,
                'extra_price'          => 0,
            ]);

            $options[$optionName] = $option;
        }

        $rows[$attributeName] = ['row' => $productAttribute, 'options' => $options];
    }

    $product->generateVariants();

    $soldVariantId = ProductCombination::query()
        ->where('product_attribute_value_id', ProductAttributeValue::query()
            ->where('product_attribute_id', $rows['Waist']['row']->id)
            ->where('attribute_option_id', $rows['Waist']['options']['24']->id)
            ->value('id'))
        ->whereIn('product_id', ProductCombination::query()
            ->where('product_attribute_value_id', ProductAttributeValue::query()
                ->where('product_attribute_id', $rows['Length']['row']->id)
                ->where('attribute_option_id', $rows['Length']['options']['24']->id)
                ->value('id'))
            ->pluck('product_id'))
        ->value('product_id');

    Lot::factory()->create(['product_id' => $soldVariantId]);

    return [$product, $rows];
}

it('drops an unused value even while a sibling value of the same attribute is in use', function () {
    [$product, $rows] = jeansFixture();

    Livewire::test(ManageAttributes::class, ['record' => $product->getRouteKey()])
        ->callAction(
            TestAction::make('edit')->table($rows['Waist']['row']),
            data: ['options' => [
                $rows['Waist']['options']['24']->id,
                $rows['Waist']['options']['44']->id,
            ]],
        );

    expect(valueExistsFor($rows['Waist']['row'], $rows['Waist']['options']['34']))->toBeFalse()
        ->and(valueExistsFor($rows['Waist']['row'], $rows['Waist']['options']['24']))->toBeTrue();
});

it('refuses the edit when the removal set also contains a value that is in use', function () {
    [$product, $rows] = jeansFixture();

    Livewire::test(ManageAttributes::class, ['record' => $product->getRouteKey()])
        ->callAction(
            TestAction::make('edit')->table($rows['Waist']['row']),
            data: ['options' => [$rows['Waist']['options']['44']->id]],
        )
        ->assertNotified();

    expect(valueExistsFor($rows['Waist']['row'], $rows['Waist']['options']['24']))->toBeTrue()
        ->and(valueExistsFor($rows['Waist']['row'], $rows['Waist']['options']['34']))->toBeTrue();
});

it('refuses to delete an attribute row backing a variant that is in use', function () {
    [$product, $productAttribute, $options] = productWithGeneratedVariants();

    $mediumVariant = variantForOption($productAttribute, $options['Medium']);

    Lot::factory()->create(['product_id' => $mediumVariant->id]);

    Livewire::test(ManageAttributes::class, ['record' => $product->getRouteKey()])
        ->callAction(TestAction::make('delete')->table($productAttribute))
        ->assertNotified();

    expect(ProductAttribute::find($productAttribute->id))->not->toBeNull();
});
