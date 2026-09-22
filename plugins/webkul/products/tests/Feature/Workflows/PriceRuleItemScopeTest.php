<?php

use Webkul\Product\Enums\PriceRuleApplyTo;
use Webkul\Product\Enums\PriceRuleBase;
use Webkul\Product\Enums\PriceRuleType;
use Webkul\Product\Models\Category;
use Webkul\Product\Models\PriceList;
use Webkul\Product\Models\PriceRuleItem;
use Webkul\Product\Models\Product;

require_once __DIR__.'/../../../../support/tests/Helpers/CompanyHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('products');

    SecurityHelper::disableUserEvents();

    $this->priceList = PriceList::factory()->create();
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

function scopeRule(array $attributes = []): PriceRuleItem
{
    return PriceRuleItem::factory()->create(array_merge([
        'price_list_id' => test()->priceList->id,
    ], $attributes));
}

it('covers everything when no product is named', function () {
    $rule = scopeRule(['display_apply_to' => PriceRuleApplyTo::PRODUCT->value]);

    expect($rule->refresh()->apply_to)->toBe(PriceRuleApplyTo::GLOBAL);
});

it('covers everything when the category scope names no category', function () {
    $rule = scopeRule(['display_apply_to' => PriceRuleApplyTo::CATEGORY->value]);

    expect($rule->refresh()->apply_to)->toBe(PriceRuleApplyTo::GLOBAL);
});

it('scopes to a product when one is named', function () {
    $product = Product::factory()->create();

    $rule = scopeRule([
        'display_apply_to' => PriceRuleApplyTo::PRODUCT->value,
        'product_id'       => $product->id,
    ]);

    expect($rule->refresh()->apply_to)->toBe(PriceRuleApplyTo::PRODUCT);
});

it('scopes to a variant when the named product is one', function () {
    $template = Product::factory()->create(['is_configurable' => true]);

    $variant = Product::factory()->create(['parent_id' => $template->id]);

    $rule = scopeRule([
        'display_apply_to' => PriceRuleApplyTo::PRODUCT->value,
        'product_id'       => $variant->id,
    ]);

    expect($rule->refresh()->apply_to)->toBe(PriceRuleApplyTo::VARIANT);
});

it('scopes to a category and drops any product left behind', function () {
    $category = Category::factory()->create();

    $product = Product::factory()->create();

    $rule = scopeRule([
        'display_apply_to' => PriceRuleApplyTo::CATEGORY->value,
        'category_id'      => $category->id,
        'product_id'       => $product->id,
    ]);

    expect($rule->refresh()->apply_to)->toBe(PriceRuleApplyTo::CATEGORY)
        ->and($rule->product_id)->toBeNull();
});

it('drops a category left behind on a product scoped rule', function () {
    $category = Category::factory()->create();

    $product = Product::factory()->create();

    $rule = scopeRule([
        'display_apply_to' => PriceRuleApplyTo::PRODUCT->value,
        'product_id'       => $product->id,
        'category_id'      => $category->id,
    ]);

    expect($rule->refresh()->category_id)->toBeNull();
});

it('derives the display scope from a category rule created without one', function () {
    $category = Category::factory()->create();

    $rule = scopeRule([
        'apply_to'    => PriceRuleApplyTo::CATEGORY,
        'category_id' => $category->id,
    ]);

    expect($rule->refresh()->apply_to)->toBe(PriceRuleApplyTo::CATEGORY)
        ->and($rule->category_id)->toBe($category->id)
        ->and($rule->display_apply_to)->toBe(PriceRuleApplyTo::CATEGORY->value);
});

it('inherits company and currency from its price list', function () {
    $rule = scopeRule();

    expect($rule->refresh()->currency_id)->toBe($this->priceList->currency_id)
        ->and($rule->company_id)->toBe($this->priceList->company_id);
});

it('reads a discount from another price list once one is named', function () {
    $other = PriceList::factory()->create();

    $rule = scopeRule([
        'type'               => PriceRuleType::PERCENTAGE,
        'percent_price'      => 10,
        'base_price_list_id' => $other->id,
    ]);

    expect($rule->refresh()->base)->toBe(PriceRuleBase::PRICE_RULES);
});

it('reads a discount from the sales price when no other list is named', function () {
    $rule = scopeRule([
        'type'          => PriceRuleType::PERCENTAGE,
        'percent_price' => 10,
    ]);

    expect($rule->refresh()->base)->toBe(PriceRuleBase::LIST_PRICE);
});

it('drops a base price list from a fixed price rule', function () {
    $other = PriceList::factory()->create();

    $rule = scopeRule([
        'type'               => PriceRuleType::FIXED,
        'fixed_price'        => 10,
        'base'               => PriceRuleBase::PRICE_RULES,
        'base_price_list_id' => $other->id,
    ]);

    expect($rule->refresh()->base_price_list_id)->toBeNull()
        ->and($rule->base)->toBe(PriceRuleBase::LIST_PRICE);
});

it('keeps a formula rule reading from the price list it names', function () {
    $other = PriceList::factory()->create();

    $rule = scopeRule([
        'type'               => PriceRuleType::FORMULA,
        'base'               => PriceRuleBase::PRICE_RULES,
        'base_price_list_id' => $other->id,
    ]);

    expect($rule->refresh()->base)->toBe(PriceRuleBase::PRICE_RULES)
        ->and($rule->base_price_list_id)->toBe($other->id);
});
