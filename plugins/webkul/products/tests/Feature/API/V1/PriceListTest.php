<?php

use Webkul\Product\Enums\PriceRuleApplyTo;
use Webkul\Product\Enums\PriceRuleType;
use Webkul\Product\Models\Category;
use Webkul\Product\Models\PriceList;
use Webkul\Product\Models\Product;
use Webkul\Security\Enums\PermissionType;
use Webkul\Security\Models\User;

require_once __DIR__.'/../../../../../support/tests/Helpers/SecurityHelper.php';
require_once __DIR__.'/../../../../../support/tests/Helpers/TestBootstrapHelper.php';

const PRODUCTS_PRICE_LIST_JSON_STRUCTURE = [
    'id',
    'name',
    'currency_id',
    'is_active',
];

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('products');
    SecurityHelper::disableUserEvents();
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

function actingAsProductsPriceListApiUser(array $permissions = []): User
{
    $user = SecurityHelper::authenticateWithPermissions($permissions);

    $user->forceFill([
        'resource_permission' => PermissionType::GLOBAL,
    ])->saveQuietly();

    return $user;
}

function productsPriceListRoute(string $action, mixed $priceList = null): string
{
    $name = "admin.api.v1.products.price-lists.{$action}";

    return $priceList ? route($name, $priceList) : route($name);
}

function productsPriceListPayload(array $overrides = []): array
{
    return array_replace_recursive([
        'name'        => 'Wholesale',
        'currency_id' => default_currency_id(),
        'is_active'   => true,
        'items'       => [
            [
                'type'        => PriceRuleType::FIXED->value,
                'fixed_price' => 42.5,
            ],
        ],
    ], $overrides);
}

it('requires authentication to list price lists', function () {
    $this->getJson(productsPriceListRoute('index'))
        ->assertUnauthorized();
});

it('requires authentication to create a price list', function () {
    $this->postJson(productsPriceListRoute('store'), [])
        ->assertUnauthorized();
});

it('forbids listing price lists without permission', function () {
    actingAsProductsPriceListApiUser();

    $this->getJson(productsPriceListRoute('index'))
        ->assertForbidden();
});

it('forbids creating a price list without permission', function () {
    actingAsProductsPriceListApiUser();

    $this->postJson(productsPriceListRoute('store'), productsPriceListPayload())
        ->assertForbidden();
});

it('lists price lists', function () {
    actingAsProductsPriceListApiUser(['view_any_product_price::list']);

    $priceList = PriceList::factory()->create();

    $this->getJson(productsPriceListRoute('index'))
        ->assertOk()
        ->assertJsonStructure(['data' => [PRODUCTS_PRICE_LIST_JSON_STRUCTURE]])
        ->assertJsonFragment(['id' => $priceList->id]);
});

it('shows a price list with its rules', function () {
    actingAsProductsPriceListApiUser(['view_product_price::list']);

    $priceList = PriceList::factory()->create();

    $priceList->items()->create([
        'display_apply_to' => PriceRuleApplyTo::PRODUCT->value,
        'type'             => PriceRuleType::FIXED,
        'fixed_price'      => 30,
    ]);

    $this->getJson(productsPriceListRoute('show', $priceList).'?include=items,currency')
        ->assertOk()
        ->assertJsonPath('data.id', $priceList->id)
        ->assertJsonPath('data.items.0.fixed_price', fn ($value): bool => (float) $value === 30.0);
});

it('creates a price list with its rules', function () {
    actingAsProductsPriceListApiUser(['create_product_price::list']);

    $this->postJson(productsPriceListRoute('store'), productsPriceListPayload())
        ->assertCreated()
        ->assertJsonPath('data.name', 'Wholesale')
        ->assertJsonPath('data.items.0.fixed_price', 42.5);

    $priceList = PriceList::query()->where('name', 'Wholesale')->first();

    expect($priceList)->not->toBeNull()
        ->and($priceList->items()->count())->toBe(1);
});

it('rejects a price list without a name', function () {
    actingAsProductsPriceListApiUser(['create_product_price::list']);

    $this->postJson(productsPriceListRoute('store'), productsPriceListPayload(['name' => null]))
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['name']);
});

it('treats a rule with no product as covering all products', function () {
    actingAsProductsPriceListApiUser(['create_product_price::list']);

    $this->postJson(productsPriceListRoute('store'), productsPriceListPayload())
        ->assertCreated()
        ->assertJsonPath('data.items.0.apply_to', PriceRuleApplyTo::GLOBAL->value);
});

it('derives a product scope from the product sent with the rule', function () {
    actingAsProductsPriceListApiUser(['create_product_price::list']);

    $product = Product::factory()->create();

    $payload = productsPriceListPayload();
    $payload['items'][0]['product_id'] = $product->id;

    $this->postJson(productsPriceListRoute('store'), $payload)
        ->assertCreated()
        ->assertJsonPath('data.items.0.apply_to', PriceRuleApplyTo::PRODUCT->value)
        ->assertJsonPath('data.items.0.product_id', $product->id);
});

it('derives a variant scope when the rule points at a variant', function () {
    actingAsProductsPriceListApiUser(['create_product_price::list']);

    $template = Product::factory()->create(['is_configurable' => true]);

    $variant = Product::factory()->create(['parent_id' => $template->id]);

    $payload = productsPriceListPayload();
    $payload['items'][0]['product_id'] = $variant->id;

    $this->postJson(productsPriceListRoute('store'), $payload)
        ->assertCreated()
        ->assertJsonPath('data.items.0.apply_to', PriceRuleApplyTo::VARIANT->value);
});

it('derives a category scope from the category sent with the rule', function () {
    actingAsProductsPriceListApiUser(['create_product_price::list']);

    $category = Category::factory()->create();

    $payload = productsPriceListPayload();
    $payload['items'][0]['display_apply_to'] = PriceRuleApplyTo::CATEGORY->value;
    $payload['items'][0]['category_id'] = $category->id;

    $this->postJson(productsPriceListRoute('store'), $payload)
        ->assertCreated()
        ->assertJsonPath('data.items.0.apply_to', PriceRuleApplyTo::CATEGORY->value)
        ->assertJsonPath('data.items.0.category_id', $category->id);
});

it('replaces the rules when updating a price list', function () {
    actingAsProductsPriceListApiUser(['update_product_price::list']);

    $priceList = PriceList::factory()->create();

    $priceList->items()->create([
        'display_apply_to' => PriceRuleApplyTo::PRODUCT->value,
        'type'             => PriceRuleType::FIXED,
        'fixed_price'      => 10,
    ]);

    $this->patchJson(productsPriceListRoute('update', $priceList), [
        'items' => [
            [
                'type'          => PriceRuleType::PERCENTAGE->value,
                'percent_price' => 15,
            ],
        ],
    ])->assertOk();

    $items = $priceList->refresh()->items;

    expect($items)->toHaveCount(1)
        ->and((float) $items->first()->percent_price)->toBe(15.0);
});

it('deletes a price list', function () {
    actingAsProductsPriceListApiUser(['delete_product_price::list']);

    $priceList = PriceList::factory()->create();

    $this->deleteJson(productsPriceListRoute('destroy', $priceList))
        ->assertOk();

    $this->assertDatabaseMissing('products_product_price_lists', ['id' => $priceList->id]);
});
