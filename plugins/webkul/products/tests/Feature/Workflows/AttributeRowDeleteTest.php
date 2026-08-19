<?php

use Webkul\Inventory\Models\Lot;
use Webkul\Product\Exceptions\VariantInUseException;
use Webkul\Product\Models\Attribute;
use Webkul\Product\Models\AttributeOption;
use Webkul\Product\Models\Product;
use Webkul\Product\Models\ProductAttribute;
use Webkul\Product\Models\ProductAttributeValue;
use Webkul\Product\Models\ProductCombination;
use Webkul\Product\Support\ProductUsageRegistry;

require_once __DIR__.'/../../../../support/tests/Helpers/CompanyHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('products');
    TestBootstrapHelper::ensurePluginInstalled('inventories');

    SecurityHelper::disableUserEvents();

    $this->registeredModels = ProductUsageRegistry::models();

    ProductUsageRegistry::flush();
    ProductUsageRegistry::register(Lot::class);
});

afterEach(function () {
    ProductUsageRegistry::flush();
    ProductUsageRegistry::register(...$this->registeredModels);

    SecurityHelper::restoreUserEvents();
});

/**
 * A product carrying two attributes — Waist (34, 44) and Neck (Collar, No Collar) — so it has
 * four variants, each of which combines both attributes.
 */
function twoAttributeProduct(): array
{
    $product = Product::factory()->create(['name' => 'Jeans', 'price' => 0, 'cost' => 0]);

    $rows = [];

    foreach (['Waist' => ['34', '44'], 'Neck' => ['Collar', 'No Collar']] as $attributeName => $optionNames) {
        $attribute = Attribute::factory()->create(['name' => $attributeName]);

        $row = ProductAttribute::factory()->create([
            'product_id'   => $product->id,
            'attribute_id' => $attribute->id,
        ]);

        foreach ($optionNames as $optionName) {
            $option = AttributeOption::factory()->create([
                'attribute_id' => $attribute->id,
                'name'         => $optionName,
                'extra_price'  => 0,
            ]);

            ProductAttributeValue::create([
                'product_id'           => $product->id,
                'attribute_id'         => $attribute->id,
                'product_attribute_id' => $row->id,
                'attribute_option_id'  => $option->id,
                'extra_price'          => 0,
            ]);
        }

        $rows[$attributeName] = $row;
    }

    $product->generateVariants();

    return [$product, $rows];
}

it('deletes every variant when none of them is in use', function () {
    [$product, $rows] = twoAttributeProduct();

    expect($product->variants()->count())->toBe(4);

    $rows['Neck']->delete();

    expect(Product::withTrashed()->where('parent_id', $product->id)->count())->toBe(0)
        ->and(ProductAttribute::find($rows['Neck']->id))->toBeNull();
});

it('refuses to delete an attribute row while any variant of the product is in use', function () {
    [$product, $rows] = twoAttributeProduct();

    $variant = $product->variants()->orderBy('id')->first();

    Lot::factory()->create(['product_id' => $variant->id]);

    expect(fn () => $rows['Neck']->delete())->toThrow(VariantInUseException::class);

    expect($product->variants()->count())->toBe(4)
        ->and(ProductAttribute::find($rows['Neck']->id))->not->toBeNull();
});

it('refuses even when the in-use variant has already lost its attribute links', function () {
    [$product, $rows] = twoAttributeProduct();

    $variant = $product->variants()->orderBy('id')->first();

    Lot::factory()->create(['product_id' => $variant->id]);

    // An orphaned variant: still sold, but no longer tied to any attribute value.
    ProductCombination::where('product_id', $variant->id)->delete();

    expect(fn () => $rows['Neck']->delete())->toThrow(VariantInUseException::class);

    expect($product->variants()->count())->toBe(4);
});
