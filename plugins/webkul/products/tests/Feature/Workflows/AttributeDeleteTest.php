<?php

use Webkul\Inventory\Models\Lot;
use Webkul\Product\Exceptions\VariantInUseException;
use Webkul\Product\Models\Attribute;
use Webkul\Product\Models\AttributeOption;
use Webkul\Product\Models\Product;
use Webkul\Product\Models\ProductAttribute;
use Webkul\Product\Models\ProductAttributeValue;
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
 * A shared attribute (24, 34) used by one product whose variants have been generated.
 */
function sharedAttributeProduct(): array
{
    $attribute = Attribute::factory()->create(['name' => 'Waist']);

    $product = Product::factory()->create(['name' => 'Jeans', 'price' => 0, 'cost' => 0]);

    $row = ProductAttribute::factory()->create([
        'product_id'   => $product->id,
        'attribute_id' => $attribute->id,
    ]);

    $options = [];

    foreach (['24', '34'] as $optionName) {
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

        $options[$optionName] = $option;
    }

    $product->generateVariants();

    return [$product, $attribute, $options];
}

function sell(Product $product, string $suffix): void
{
    $variant = $product->variants()->where('name', 'like', '%'.$suffix)->firstOrFail();

    Lot::factory()->create(['product_id' => $variant->id]);
}

it('refuses to delete an attribute option whose variant is in use', function () {
    [$product, , $options] = sharedAttributeProduct();

    sell($product, '- 24');

    expect(fn () => $options['24']->delete())->toThrow(VariantInUseException::class);

    expect(AttributeOption::find($options['24']->id))->not->toBeNull()
        ->and($product->variants()->count())->toBe(2);
});

it('still deletes an attribute option whose variant is not in use', function () {
    [$product, , $options] = sharedAttributeProduct();

    sell($product, '- 24');

    $options['34']->delete();

    expect(AttributeOption::find($options['34']->id))->toBeNull();
});

it('refuses to force delete an attribute while a variant built from it is in use', function () {
    [$product, $attribute] = sharedAttributeProduct();

    sell($product, '- 24');

    expect(fn () => $attribute->forceDelete())->toThrow(VariantInUseException::class);

    expect(Attribute::find($attribute->id))->not->toBeNull()
        ->and(ProductAttribute::where('product_id', $product->id)->count())->toBe(1)
        ->and($product->variants()->count())->toBe(2);
});

it('still allows soft deleting an attribute that is in use', function () {
    [$product, $attribute] = sharedAttributeProduct();

    sell($product, '- 24');

    $attribute->delete();

    expect(Attribute::withTrashed()->find($attribute->id)->trashed())->toBeTrue()
        ->and(ProductAttribute::where('product_id', $product->id)->count())->toBe(1)
        ->and($product->variants()->count())->toBe(2);
});

it('still force deletes an attribute when none of its variants is in use', function () {
    [$product, $attribute] = sharedAttributeProduct();

    $attribute->forceDelete();

    expect(Attribute::withTrashed()->find($attribute->id))->toBeNull()
        ->and(ProductAttribute::where('product_id', $product->id)->count())->toBe(0);
});
