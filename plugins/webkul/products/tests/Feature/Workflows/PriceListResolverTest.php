<?php

use Webkul\Product\Enums\PriceRuleApplyTo;
use Webkul\Product\Enums\PriceRuleBase;
use Webkul\Product\Enums\PriceRuleType;
use Webkul\Product\Models\Category;
use Webkul\Product\Models\PriceList;
use Webkul\Product\Models\PriceRuleItem;
use Webkul\Product\Models\Product;
use Webkul\Product\Services\PriceListResolver;
use Webkul\Support\Models\UOM;
use Webkul\Support\Models\UOMCategory;

require_once __DIR__.'/../../../../support/tests/Helpers/CompanyHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('products');

    SecurityHelper::disableUserEvents();

    $this->resolver = app(PriceListResolver::class);

    $this->priceList = PriceList::factory()->create();
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

function priceListProduct(array $attributes = []): Product
{
    return Product::factory()->create(array_merge(['price' => 100, 'cost' => 60], $attributes));
}

function priceRule(PriceList $priceList, array $attributes): PriceRuleItem
{
    return PriceRuleItem::factory()->create(array_merge([
        'price_list_id' => $priceList->id,
        'apply_to'      => PriceRuleApplyTo::GLOBAL,
    ], $attributes));
}

it('falls back to the product sales price when the list holds no rule', function () {
    $product = priceListProduct();

    expect($this->resolver->getProductPrice($this->priceList, $product))->toBe(100.0);
});

it('falls back to the product sales price when no price list is given', function () {
    $product = priceListProduct();

    expect($this->resolver->getProductPrice(null, $product))->toBe(100.0);
});

it('applies a fixed price rule', function () {
    $product = priceListProduct();

    priceRule($this->priceList, ['type' => PriceRuleType::FIXED, 'fixed_price' => 42.5]);

    expect($this->resolver->getProductPrice($this->priceList, $product))->toBe(42.5);
});

it('applies a percentage discount against the sales price', function () {
    $product = priceListProduct();

    priceRule($this->priceList, ['type' => PriceRuleType::PERCENTAGE, 'percent_price' => 25]);

    expect($this->resolver->getProductPrice($this->priceList, $product))->toBe(75.0);
});

it('treats a negative percentage as a mark-up', function () {
    $product = priceListProduct();

    priceRule($this->priceList, ['type' => PriceRuleType::PERCENTAGE, 'percent_price' => -10]);

    expect($this->resolver->getProductPrice($this->priceList, $product))->toBe(110.0);
});

it('applies a formula as discount, then rounding, then the extra fee', function () {
    $product = priceListProduct();

    priceRule($this->priceList, [
        'type'            => PriceRuleType::FORMULA,
        'base'            => PriceRuleBase::LIST_PRICE,
        'price_discount'  => 10,
        'price_round'     => 10,
        'price_surcharge' => -0.01,
    ]);

    expect($this->resolver->getProductPrice($this->priceList, $product))->toBe(89.99);
});

it('marks the cost up when the formula is based on cost', function () {
    $product = priceListProduct();

    priceRule($this->priceList, [
        'type'         => PriceRuleType::FORMULA,
        'base'         => PriceRuleBase::STANDARD_PRICE,
        'price_markup' => 50,
    ]);

    expect($this->resolver->getProductPrice($this->priceList, $product))->toBe(90.0);
});

it('never discounts below the minimum margin', function () {
    $product = priceListProduct();

    priceRule($this->priceList, [
        'type'             => PriceRuleType::FORMULA,
        'price_discount'   => 50,
        'price_min_margin' => -20,
    ]);

    expect($this->resolver->getProductPrice($this->priceList, $product))->toBe(80.0);
});

it('never marks up beyond the maximum margin', function () {
    $product = priceListProduct();

    priceRule($this->priceList, [
        'type'             => PriceRuleType::FORMULA,
        'price_discount'   => -50,
        'price_max_margin' => 20,
    ]);

    expect($this->resolver->getProductPrice($this->priceList, $product))->toBe(120.0);
});

it('ignores a rule until its minimum quantity is reached', function () {
    $product = priceListProduct();

    priceRule($this->priceList, [
        'type'         => PriceRuleType::FIXED,
        'fixed_price'  => 70,
        'min_quantity' => 10,
    ]);

    expect($this->resolver->getProductPrice($this->priceList, $product, 9))->toBe(100.0)
        ->and($this->resolver->getProductPrice($this->priceList, $product, 10))->toBe(70.0);
});

it('prefers the highest quantity break the order qualifies for', function () {
    $product = priceListProduct();

    priceRule($this->priceList, ['type' => PriceRuleType::FIXED, 'fixed_price' => 90, 'min_quantity' => 5]);
    priceRule($this->priceList, ['type' => PriceRuleType::FIXED, 'fixed_price' => 80, 'min_quantity' => 20]);

    expect($this->resolver->getProductPrice($this->priceList, $product, 5))->toBe(90.0)
        ->and($this->resolver->getProductPrice($this->priceList, $product, 25))->toBe(80.0);
});

it('ignores a rule that has not started yet', function () {
    $product = priceListProduct();

    priceRule($this->priceList, [
        'type'        => PriceRuleType::FIXED,
        'fixed_price' => 70,
        'starts_at'   => now()->addWeek(),
    ]);

    expect($this->resolver->getProductPrice($this->priceList, $product))->toBe(100.0);
});

it('ignores a rule that has already expired', function () {
    $product = priceListProduct();

    priceRule($this->priceList, [
        'type'        => PriceRuleType::FIXED,
        'fixed_price' => 70,
        'ends_at'     => now()->subDay(),
    ]);

    expect($this->resolver->getProductPrice($this->priceList, $product))->toBe(100.0);
});

it('honours a rule inside its window', function () {
    $product = priceListProduct();

    priceRule($this->priceList, [
        'type'        => PriceRuleType::FIXED,
        'fixed_price' => 70,
        'starts_at'   => now()->subDay(),
        'ends_at'     => now()->addDay(),
    ]);

    expect($this->resolver->getProductPrice($this->priceList, $product))->toBe(70.0);
});

it('lets the most specific rule win over broader ones', function () {
    $category = Category::factory()->create();

    $product = priceListProduct(['category_id' => $category->id]);

    priceRule($this->priceList, ['type' => PriceRuleType::FIXED, 'fixed_price' => 90]);
    priceRule($this->priceList, [
        'apply_to'    => PriceRuleApplyTo::CATEGORY,
        'category_id' => $category->id,
        'type'        => PriceRuleType::FIXED,
        'fixed_price' => 80,
    ]);
    priceRule($this->priceList, [
        'apply_to'    => PriceRuleApplyTo::PRODUCT,
        'product_id'  => $product->id,
        'type'        => PriceRuleType::FIXED,
        'fixed_price' => 70,
    ]);

    expect($this->resolver->getProductPrice($this->priceList, $product))->toBe(70.0);
});

it('prefers a category rule over a global one', function () {
    $category = Category::factory()->create();

    $product = priceListProduct(['category_id' => $category->id]);

    priceRule($this->priceList, ['type' => PriceRuleType::FIXED, 'fixed_price' => 90]);
    priceRule($this->priceList, [
        'apply_to'    => PriceRuleApplyTo::CATEGORY,
        'category_id' => $category->id,
        'type'        => PriceRuleType::FIXED,
        'fixed_price' => 80,
    ]);

    expect($this->resolver->getProductPrice($this->priceList, $product))->toBe(80.0);
});

it('applies a parent category rule to a product filed under a child category', function () {
    $parent = Category::factory()->create();

    $child = Category::factory()->create(['parent_id' => $parent->id]);

    $product = priceListProduct(['category_id' => $child->id]);

    priceRule($this->priceList, [
        'apply_to'    => PriceRuleApplyTo::CATEGORY,
        'category_id' => $parent->id,
        'type'        => PriceRuleType::FIXED,
        'fixed_price' => 55,
    ]);

    expect($this->resolver->getProductPrice($this->priceList, $product))->toBe(55.0);
});

it('does not apply a child category rule to a product in the parent category', function () {
    $parent = Category::factory()->create();

    $child = Category::factory()->create(['parent_id' => $parent->id]);

    $product = priceListProduct(['category_id' => $parent->id]);

    priceRule($this->priceList, [
        'apply_to'    => PriceRuleApplyTo::CATEGORY,
        'category_id' => $child->id,
        'type'        => PriceRuleType::FIXED,
        'fixed_price' => 55,
    ]);

    expect($this->resolver->getProductPrice($this->priceList, $product))->toBe(100.0);
});

it('applies a product rule to each of its variants', function () {
    $template = priceListProduct(['is_configurable' => true]);

    $variant = priceListProduct(['parent_id' => $template->id, 'price' => 120]);

    priceRule($this->priceList, [
        'apply_to'    => PriceRuleApplyTo::PRODUCT,
        'product_id'  => $template->id,
        'type'        => PriceRuleType::FIXED,
        'fixed_price' => 65,
    ]);

    expect($this->resolver->getProductPrice($this->priceList, $variant))->toBe(65.0);
});

it('lets a variant rule outrank the rule on its product', function () {
    $template = priceListProduct(['is_configurable' => true]);

    $variant = priceListProduct(['parent_id' => $template->id]);

    priceRule($this->priceList, [
        'apply_to'    => PriceRuleApplyTo::PRODUCT,
        'product_id'  => $template->id,
        'type'        => PriceRuleType::FIXED,
        'fixed_price' => 65,
    ]);
    priceRule($this->priceList, [
        'apply_to'    => PriceRuleApplyTo::VARIANT,
        'product_id'  => $variant->id,
        'type'        => PriceRuleType::FIXED,
        'fixed_price' => 50,
    ]);

    expect($this->resolver->getProductPrice($this->priceList, $variant))->toBe(50.0);
});

it('does not leak a variant rule onto a sibling variant', function () {
    $template = priceListProduct(['is_configurable' => true]);

    $variant = priceListProduct(['parent_id' => $template->id]);

    $sibling = priceListProduct(['parent_id' => $template->id]);

    priceRule($this->priceList, [
        'apply_to'    => PriceRuleApplyTo::VARIANT,
        'product_id'  => $variant->id,
        'type'        => PriceRuleType::FIXED,
        'fixed_price' => 50,
    ]);

    expect($this->resolver->getProductPrice($this->priceList, $sibling))->toBe(100.0);
});

it('computes a rule on top of another price list', function () {
    $product = priceListProduct();

    $basePriceList = PriceList::factory()->create(['currency_id' => $this->priceList->currency_id]);

    priceRule($basePriceList, ['type' => PriceRuleType::FIXED, 'fixed_price' => 80]);

    priceRule($this->priceList, [
        'type'               => PriceRuleType::PERCENTAGE,
        'base'               => PriceRuleBase::PRICE_RULES,
        'base_price_list_id' => $basePriceList->id,
        'percent_price'      => 50,
    ]);

    expect($this->resolver->getProductPrice($this->priceList, $product))->toBe(40.0);
});

it('falls back to the sales price rather than looping on a cyclic base chain', function () {
    $product = priceListProduct();

    $other = PriceList::factory()->create(['currency_id' => $this->priceList->currency_id]);

    priceRule($this->priceList, [
        'type'               => PriceRuleType::PERCENTAGE,
        'base'               => PriceRuleBase::PRICE_RULES,
        'base_price_list_id' => $other->id,
        'percent_price'      => 0,
    ]);

    priceRule($other, [
        'type'               => PriceRuleType::PERCENTAGE,
        'base'               => PriceRuleBase::PRICE_RULES,
        'base_price_list_id' => $this->priceList->id,
        'percent_price'      => 0,
    ]);

    expect($this->resolver->getProductPrice($this->priceList, $product))->toBe(100.0);
});

it('reports the rule it applied', function () {
    $product = priceListProduct();

    $rule = priceRule($this->priceList, ['type' => PriceRuleType::FIXED, 'fixed_price' => 42]);

    expect($this->resolver->getProductPriceRule($this->priceList, $product)?->id)->toBe($rule->id);
});

it('reports no rule when the list leaves the product alone', function () {
    $product = priceListProduct();

    expect($this->resolver->getProductPriceRule($this->priceList, $product))->toBeNull();
});

it('restates a fixed price in the requested unit of measure', function () {
    $category = UOMCategory::factory()->create();

    $unit = UOM::factory()->create(['category_id' => $category->id, 'type' => 'reference', 'factor' => 1]);

    $dozen = UOM::factory()->create(['category_id' => $category->id, 'type' => 'bigger', 'factor' => 1 / 12]);

    $product = priceListProduct(['uom_id' => $unit->id]);

    priceRule($this->priceList, ['type' => PriceRuleType::FIXED, 'fixed_price' => 10]);

    expect($this->resolver->getProductPrice($this->priceList, $product, 1, $dozen))->toBe(120.0);
});

it('measures a quantity break in the product unit, not the order unit', function () {
    $category = UOMCategory::factory()->create();

    $unit = UOM::factory()->create(['category_id' => $category->id, 'type' => 'reference', 'factor' => 1]);

    $dozen = UOM::factory()->create(['category_id' => $category->id, 'type' => 'bigger', 'factor' => 1 / 12]);

    $product = priceListProduct(['uom_id' => $unit->id]);

    priceRule($this->priceList, [
        'type'         => PriceRuleType::FIXED,
        'fixed_price'  => 5,
        'min_quantity' => 12,
    ]);

    expect($this->resolver->getProductPrice($this->priceList, $product, 1, $dozen))->toBe(60.0);
});
