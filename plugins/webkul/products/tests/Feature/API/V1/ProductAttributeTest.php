<?php

use Webkul\Product\Models\Attribute;
use Webkul\Product\Models\AttributeOption;
use Webkul\Product\Models\Product;
use Webkul\Product\Models\ProductAttribute;
use Webkul\Security\Enums\PermissionType;
use Webkul\Security\Models\User;

require_once __DIR__.'/../../../../../support/tests/Helpers/SecurityHelper.php';
require_once __DIR__.'/../../../../../support/tests/Helpers/TestBootstrapHelper.php';

const PRODUCTS_PRODUCT_ATTRIBUTE_JSON_STRUCTURE = [
    'id',
    'product_id',
    'attribute_id',
];

const PRODUCTS_PRODUCT_ATTRIBUTE_REQUIRED_FIELDS = [
    'attribute_id',
];

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('products');
    SecurityHelper::disableUserEvents();
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

function actingAsProductsProductAttributeApiUser(array $permissions = []): User
{
    $user = SecurityHelper::authenticateWithPermissions($permissions);

    $user->forceFill([
        'resource_permission' => PermissionType::GLOBAL,
    ])->saveQuietly();

    return $user;
}

function productsProductAttributeRoute(string $action, mixed $product, mixed $attribute = null): string
{
    $name = "admin.api.v1.products.products.attributes.{$action}";

    if ($attribute) {
        return route($name, [$product, $attribute]);
    }

    return route($name, $product);
}

function productsProductAttributePayload(array $overrides = []): array
{
    $attribute = Attribute::factory()->create();

    return array_replace_recursive([
        'attribute_id' => $attribute->id,
    ], $overrides);
}

// ── Authentication ────────────────────────────────────────────────────────────

it('requires authentication to list product attributes', function () {
    $product = Product::factory()->create();

    $this->getJson(productsProductAttributeRoute('index', $product))
        ->assertUnauthorized();
});

it('requires authentication to create a product attribute', function () {
    $product = Product::factory()->create();

    $this->postJson(productsProductAttributeRoute('store', $product), [])
        ->assertUnauthorized();
});

it('requires authentication to show a product attribute', function () {
    $productAttribute = ProductAttribute::factory()->create();

    $this->getJson(productsProductAttributeRoute('show', $productAttribute->product_id, $productAttribute))
        ->assertUnauthorized();
});

it('requires authentication to update a product attribute', function () {
    $productAttribute = ProductAttribute::factory()->create();

    $this->patchJson(productsProductAttributeRoute('update', $productAttribute->product_id, $productAttribute), [])
        ->assertUnauthorized();
});

it('requires authentication to delete a product attribute', function () {
    $productAttribute = ProductAttribute::factory()->create();

    $this->deleteJson(productsProductAttributeRoute('destroy', $productAttribute->product_id, $productAttribute))
        ->assertUnauthorized();
});

// ── Authorization ─────────────────────────────────────────────────────────────

it('forbids listing product attributes without permission', function () {
    actingAsProductsProductAttributeApiUser();

    $product = Product::factory()->create();

    $this->getJson(productsProductAttributeRoute('index', $product))
        ->assertForbidden();
});

it('forbids creating a product attribute without permission', function () {
    actingAsProductsProductAttributeApiUser();

    $product = Product::factory()->create();

    $this->postJson(productsProductAttributeRoute('store', $product), productsProductAttributePayload())
        ->assertForbidden();
});

it('forbids creating a product attribute with only view permission (needs update)', function () {
    actingAsProductsProductAttributeApiUser(['view_product_product']);

    $product = Product::factory()->create();

    $this->postJson(productsProductAttributeRoute('store', $product), productsProductAttributePayload())
        ->assertForbidden();
});

it('forbids showing a product attribute without permission', function () {
    actingAsProductsProductAttributeApiUser();

    $productAttribute = ProductAttribute::factory()->create();

    $this->getJson(productsProductAttributeRoute('show', $productAttribute->product_id, $productAttribute))
        ->assertForbidden();
});

it('forbids updating a product attribute without permission', function () {
    actingAsProductsProductAttributeApiUser();

    $productAttribute = ProductAttribute::factory()->create();

    $this->patchJson(productsProductAttributeRoute('update', $productAttribute->product_id, $productAttribute), [])
        ->assertForbidden();
});

it('forbids deleting a product attribute without permission', function () {
    actingAsProductsProductAttributeApiUser();

    $productAttribute = ProductAttribute::factory()->create();

    $this->deleteJson(productsProductAttributeRoute('destroy', $productAttribute->product_id, $productAttribute))
        ->assertForbidden();
});

// ── Index ─────────────────────────────────────────────────────────────────────

it('lists product attributes for authorized users', function () {
    actingAsProductsProductAttributeApiUser(['view_product_product']);

    $product = Product::factory()->create();
    ProductAttribute::factory()->count(3)->create(['product_id' => $product->id]);

    $this->getJson(productsProductAttributeRoute('index', $product))
        ->assertOk()
        ->assertJsonStructure(['data', 'meta', 'links']);
});

it('only returns attributes belonging to the given product', function () {
    actingAsProductsProductAttributeApiUser(['view_product_product']);

    $product = Product::factory()->create();
    $otherProduct = Product::factory()->create();

    $ownAttribute = ProductAttribute::factory()->create(['product_id' => $product->id]);
    $otherAttribute = ProductAttribute::factory()->create(['product_id' => $otherProduct->id]);

    $response = $this->getJson(productsProductAttributeRoute('index', $product))
        ->assertOk();

    $ids = collect($response->json('data'))->pluck('id');

    expect($ids)->toContain($ownAttribute->id)
        ->and($ids)->not->toContain($otherAttribute->id);
});

it('returns 404 when listing attributes for a non-existent product', function () {
    actingAsProductsProductAttributeApiUser(['view_product_product']);

    $this->getJson(productsProductAttributeRoute('index', 999999))
        ->assertNotFound();
});

// ── Store ─────────────────────────────────────────────────────────────────────

it('creates a product attribute', function () {
    actingAsProductsProductAttributeApiUser(['update_product_product']);

    $product = Product::factory()->create();
    $payload = productsProductAttributePayload();

    $this->postJson(productsProductAttributeRoute('store', $product), $payload)
        ->assertCreated()
        ->assertJsonPath('message', 'Product attribute created successfully.')
        ->assertJsonStructure(['data' => PRODUCTS_PRODUCT_ATTRIBUTE_JSON_STRUCTURE]);

    $this->assertDatabaseHas('products_product_attributes', [
        'product_id'   => $product->id,
        'attribute_id' => $payload['attribute_id'],
    ]);
});

it('creates a product attribute with options', function () {
    actingAsProductsProductAttributeApiUser(['update_product_product']);

    $product = Product::factory()->create();
    $attribute = Attribute::factory()->create();
    $option1 = AttributeOption::factory()->create(['attribute_id' => $attribute->id]);
    $option2 = AttributeOption::factory()->create(['attribute_id' => $attribute->id]);

    $this->postJson(productsProductAttributeRoute('store', $product), [
        'attribute_id' => $attribute->id,
        'options'      => [$option1->id, $option2->id],
    ])
        ->assertCreated()
        ->assertJsonPath('message', 'Product attribute created successfully.');

    $this->assertDatabaseHas('products_product_attributes', [
        'product_id'   => $product->id,
        'attribute_id' => $attribute->id,
    ]);
});

it('validates required fields when creating a product attribute', function (string $field) {
    actingAsProductsProductAttributeApiUser(['update_product_product']);

    $product = Product::factory()->create();
    $payload = productsProductAttributePayload();
    unset($payload[$field]);

    $this->postJson(productsProductAttributeRoute('store', $product), $payload)
        ->assertUnprocessable()
        ->assertJsonValidationErrors([$field]);
})->with(PRODUCTS_PRODUCT_ATTRIBUTE_REQUIRED_FIELDS);

it('returns 404 when creating attribute for a non-existent product', function () {
    actingAsProductsProductAttributeApiUser(['update_product_product']);

    $this->postJson(productsProductAttributeRoute('store', 999999), productsProductAttributePayload())
        ->assertNotFound();
});

// ── Show ──────────────────────────────────────────────────────────────────────

it('shows a product attribute for authorized users', function () {
    actingAsProductsProductAttributeApiUser(['view_product_product']);

    $productAttribute = ProductAttribute::factory()->create();

    $this->getJson(productsProductAttributeRoute('show', $productAttribute->product_id, $productAttribute))
        ->assertOk()
        ->assertJsonPath('data.id', $productAttribute->id)
        ->assertJsonPath('data.product_id', $productAttribute->product_id)
        ->assertJsonPath('data.attribute_id', $productAttribute->attribute_id)
        ->assertJsonStructure(['data' => PRODUCTS_PRODUCT_ATTRIBUTE_JSON_STRUCTURE]);
});

it('returns 404 for a non-existent product attribute', function () {
    actingAsProductsProductAttributeApiUser(['view_product_product']);

    $product = Product::factory()->create();

    $this->getJson(productsProductAttributeRoute('show', $product, 999999))
        ->assertNotFound();
});

it('returns 404 when attribute does not belong to the given product', function () {
    actingAsProductsProductAttributeApiUser(['view_product_product']);

    $product = Product::factory()->create();
    $otherProduct = Product::factory()->create();
    $otherProductAttr = ProductAttribute::factory()->create(['product_id' => $otherProduct->id]);

    $this->getJson(productsProductAttributeRoute('show', $product, $otherProductAttr))
        ->assertNotFound();
});

// ── Update ────────────────────────────────────────────────────────────────────

it('updates a product attribute sort order', function () {
    actingAsProductsProductAttributeApiUser(['update_product_product']);

    $productAttribute = ProductAttribute::factory()->create();

    $this->patchJson(productsProductAttributeRoute('update', $productAttribute->product_id, $productAttribute), ['sort' => 5])
        ->assertOk()
        ->assertJsonPath('message', 'Product attribute updated successfully.');

    $this->assertDatabaseHas('products_product_attributes', [
        'id'   => $productAttribute->id,
        'sort' => 5,
    ]);
});

it('updates product attribute options', function () {
    actingAsProductsProductAttributeApiUser(['update_product_product']);

    $productAttribute = ProductAttribute::factory()->create();
    $option = AttributeOption::factory()->create(['attribute_id' => $productAttribute->attribute_id]);

    $this->patchJson(productsProductAttributeRoute('update', $productAttribute->product_id, $productAttribute), [
        'options' => [$option->id],
    ])
        ->assertOk()
        ->assertJsonPath('message', 'Product attribute updated successfully.');
});

it('returns 404 when updating a non-existent product attribute', function () {
    actingAsProductsProductAttributeApiUser(['update_product_product']);

    $product = Product::factory()->create();

    $this->patchJson(productsProductAttributeRoute('update', $product, 999999), ['sort' => 1])
        ->assertNotFound();
});

// ── Destroy ───────────────────────────────────────────────────────────────────

it('deletes a product attribute', function () {
    actingAsProductsProductAttributeApiUser(['update_product_product']);

    $productAttribute = ProductAttribute::factory()->create();

    $this->deleteJson(productsProductAttributeRoute('destroy', $productAttribute->product_id, $productAttribute))
        ->assertOk()
        ->assertJsonPath('message', 'Product attribute deleted successfully.');

    $this->assertDatabaseMissing('products_product_attributes', ['id' => $productAttribute->id]);
});

it('returns 404 when deleting a non-existent product attribute', function () {
    actingAsProductsProductAttributeApiUser(['update_product_product']);

    $product = Product::factory()->create();

    $this->deleteJson(productsProductAttributeRoute('destroy', $product, 999999))
        ->assertNotFound();
});

it('returns 404 when deleting an attribute that does not belong to the given product', function () {
    actingAsProductsProductAttributeApiUser(['update_product_product']);

    $product = Product::factory()->create();
    $otherProduct = Product::factory()->create();
    $otherProductAttr = ProductAttribute::factory()->create(['product_id' => $otherProduct->id]);

    $this->deleteJson(productsProductAttributeRoute('destroy', $product, $otherProductAttr))
        ->assertNotFound();
});
