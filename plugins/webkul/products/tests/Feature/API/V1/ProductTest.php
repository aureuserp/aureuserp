<?php

use Webkul\Product\Enums\ProductType;
use Webkul\Product\Models\Category;
use Webkul\Product\Models\Product;
use Webkul\Product\Models\Tag;
use Webkul\Security\Enums\PermissionType;
use Webkul\Security\Models\User;
use Webkul\Support\Models\UOM;

require_once __DIR__.'/../../../../../support/tests/Helpers/SecurityHelper.php';
require_once __DIR__.'/../../../../../support/tests/Helpers/TestBootstrapHelper.php';

const PRODUCTS_PRODUCT_JSON_STRUCTURE = [
    'id',
    'type',
    'name',
    'price',
    'category_id',
    'created_at',
    'updated_at',
];

const PRODUCTS_PRODUCT_REQUIRED_FIELDS = [
    'type',
    'name',
    'price',
    'category_id',
];

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('products');
    SecurityHelper::disableUserEvents();
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

function actingAsProductsProductApiUser(array $permissions = []): User
{
    $user = SecurityHelper::authenticateWithPermissions($permissions);

    $user->forceFill([
        'resource_permission' => PermissionType::GLOBAL,
    ])->saveQuietly();

    return $user;
}

function productsProductRoute(string $action, mixed $product = null): string
{
    $name = "admin.api.v1.products.products.{$action}";

    return $product ? route($name, $product) : route($name);
}

function productsProductPayload(array $overrides = []): array
{
    $category = Category::factory()->create();
    $uom = UOM::factory()->create();

    return array_replace_recursive([
        'type'        => ProductType::GOODS->value,
        'name'        => 'Test Product',
        'price'       => 99.99,
        'category_id' => $category->id,
        'uom_id'      => $uom->id,
        'uom_po_id'   => $uom->id,
    ], $overrides);
}

// ── Authentication ────────────────────────────────────────────────────────────

it('requires authentication to list products', function () {
    $this->getJson(productsProductRoute('index'))
        ->assertUnauthorized();
});

it('requires authentication to create a product', function () {
    $this->postJson(productsProductRoute('store'), [])
        ->assertUnauthorized();
});

it('requires authentication to show a product', function () {
    $product = Product::factory()->create();

    $this->getJson(productsProductRoute('show', $product))
        ->assertUnauthorized();
});

it('requires authentication to update a product', function () {
    $product = Product::factory()->create();

    $this->patchJson(productsProductRoute('update', $product), [])
        ->assertUnauthorized();
});

it('requires authentication to delete a product', function () {
    $product = Product::factory()->create();

    $this->deleteJson(productsProductRoute('destroy', $product))
        ->assertUnauthorized();
});

it('requires authentication to restore a product', function () {
    $product = Product::factory()->create();
    $product->delete();

    $this->postJson(productsProductRoute('restore', $product))
        ->assertUnauthorized();
});

it('requires authentication to force-delete a product', function () {
    $product = Product::factory()->create();
    $product->delete();

    $this->deleteJson(productsProductRoute('force-destroy', $product))
        ->assertUnauthorized();
});

// ── Authorization ─────────────────────────────────────────────────────────────

it('forbids listing products without permission', function () {
    actingAsProductsProductApiUser();

    $this->getJson(productsProductRoute('index'))
        ->assertForbidden();
});

it('forbids creating a product without permission', function () {
    actingAsProductsProductApiUser();

    $this->postJson(productsProductRoute('store'), productsProductPayload())
        ->assertForbidden();
});

it('forbids showing a product without permission', function () {
    actingAsProductsProductApiUser();

    $product = Product::factory()->create();

    $this->getJson(productsProductRoute('show', $product))
        ->assertForbidden();
});

it('forbids updating a product without permission', function () {
    actingAsProductsProductApiUser();

    $product = Product::factory()->create();

    $this->patchJson(productsProductRoute('update', $product), [])
        ->assertForbidden();
});

it('forbids deleting a product without permission', function () {
    actingAsProductsProductApiUser();

    $product = Product::factory()->create();

    $this->deleteJson(productsProductRoute('destroy', $product))
        ->assertForbidden();
});

it('forbids restoring a product without permission', function () {
    actingAsProductsProductApiUser();

    $product = Product::factory()->create();
    $product->delete();

    $this->postJson(productsProductRoute('restore', $product))
        ->assertForbidden();
});

it('forbids force-deleting a product without permission', function () {
    actingAsProductsProductApiUser();

    $product = Product::factory()->create();
    $product->delete();

    $this->deleteJson(productsProductRoute('force-destroy', $product))
        ->assertForbidden();
});

// ── Index ─────────────────────────────────────────────────────────────────────

it('lists products for authorized users', function () {
    actingAsProductsProductApiUser(['view_any_product_product']);

    Product::factory()->count(3)->create();

    $this->getJson(productsProductRoute('index'))
        ->assertOk()
        ->assertJsonStructure(['data', 'meta', 'links']);
});

it('filters products by type', function () {
    actingAsProductsProductApiUser(['view_any_product_product']);

    $goodsProduct = Product::factory()->create(['type' => ProductType::GOODS->value]);
    $serviceProduct = Product::factory()->create(['type' => ProductType::SERVICE->value]);

    $response = $this->getJson(productsProductRoute('index').'?filter[type]='.ProductType::GOODS->value)
        ->assertOk();

    $ids = collect($response->json('data'))->pluck('id');

    expect($ids)->toContain($goodsProduct->id);
    collect($response->json('data'))->each(fn ($item) => expect($item['type'])->toBe(ProductType::GOODS->value));
});

it('filters products by enable_sales', function () {
    actingAsProductsProductApiUser(['view_any_product_product']);

    $enabled = Product::factory()->create(['enable_sales' => true]);
    $disabled = Product::factory()->create(['enable_sales' => false]);

    $response = $this->getJson(productsProductRoute('index').'?filter[enable_sales]=true')
        ->assertOk();

    $ids = collect($response->json('data'))->pluck('id');

    expect($ids)->toContain($enabled->id)
        ->and($ids)->not->toContain($disabled->id);
});

it('excludes soft-deleted products from default listing', function () {
    actingAsProductsProductApiUser(['view_any_product_product']);

    $active = Product::factory()->create();
    $deleted = Product::factory()->create();
    $deleted->delete();

    $response = $this->getJson(productsProductRoute('index'))
        ->assertOk();

    $ids = collect($response->json('data'))->pluck('id');

    expect($ids)->toContain($active->id)
        ->and($ids)->not->toContain($deleted->id);
});

it('includes soft-deleted products when filter[trashed]=with', function () {
    actingAsProductsProductApiUser(['view_any_product_product']);

    $deleted = Product::factory()->create();
    $deleted->delete();

    $response = $this->getJson(productsProductRoute('index').'?filter[trashed]=with')
        ->assertOk();

    $ids = collect($response->json('data'))->pluck('id');

    expect($ids)->toContain($deleted->id);
});

// ── Store ─────────────────────────────────────────────────────────────────────

it('creates a product', function () {
    actingAsProductsProductApiUser(['create_product_product']);

    $payload = productsProductPayload();

    $this->postJson(productsProductRoute('store'), $payload)
        ->assertCreated()
        ->assertJsonPath('message', 'Product created successfully.')
        ->assertJsonPath('data.name', $payload['name'])
        ->assertJsonPath('data.type', $payload['type'])
        ->assertJsonPath('data.price', $payload['price'])
        ->assertJsonStructure(['data' => PRODUCTS_PRODUCT_JSON_STRUCTURE]);

    $this->assertDatabaseHas('products_products', [
        'name'        => $payload['name'],
        'category_id' => $payload['category_id'],
    ]);
});

it('creates a product with optional fields', function () {
    actingAsProductsProductApiUser(['create_product_product']);

    $this->postJson(productsProductRoute('store'), productsProductPayload([
        'cost'             => 49.99,
        'weight'           => 1.5,
        'volume'           => 0.3,
        'enable_sales'     => true,
        'enable_purchase'  => true,
        'is_favorite'      => true,
        'description'      => 'A great product',
        'description_sale' => 'Perfect for everyone',
    ]))
        ->assertCreated()
        ->assertJsonPath('data.enable_sales', true)
        ->assertJsonPath('data.enable_purchase', true);
});

it('creates a product with tags', function () {
    actingAsProductsProductApiUser(['create_product_product']);

    $tag1 = Tag::factory()->create();
    $tag2 = Tag::factory()->create();

    $this->postJson(productsProductRoute('store'), productsProductPayload([
        'tags' => [$tag1->id, $tag2->id],
    ]))
        ->assertCreated();
});

it('validates required fields when creating a product', function (string $field) {
    actingAsProductsProductApiUser(['create_product_product']);

    $payload = productsProductPayload();
    unset($payload[$field]);

    $this->postJson(productsProductRoute('store'), $payload)
        ->assertUnprocessable()
        ->assertJsonValidationErrors([$field]);
})->with(PRODUCTS_PRODUCT_REQUIRED_FIELDS);

it('rejects an invalid product type', function () {
    actingAsProductsProductApiUser(['create_product_product']);

    $this->postJson(productsProductRoute('store'), productsProductPayload(['type' => 'invalid']))
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['type']);
});

it('rejects a non-existent category_id', function () {
    actingAsProductsProductApiUser(['create_product_product']);

    $this->postJson(productsProductRoute('store'), productsProductPayload(['category_id' => 999999]))
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['category_id']);
});

// ── Show ──────────────────────────────────────────────────────────────────────

it('shows a product for authorized users', function () {
    actingAsProductsProductApiUser(['view_product_product']);

    $product = Product::factory()->create();

    $this->getJson(productsProductRoute('show', $product))
        ->assertOk()
        ->assertJsonPath('data.id', $product->id)
        ->assertJsonPath('data.name', $product->name)
        ->assertJsonPath('data.type', $product->type->value)
        ->assertJsonStructure(['data' => PRODUCTS_PRODUCT_JSON_STRUCTURE]);
});

it('returns 404 for a non-existent product', function () {
    actingAsProductsProductApiUser(['view_product_product']);

    $this->getJson(productsProductRoute('show', 999999))
        ->assertNotFound();
});

// ── Update ────────────────────────────────────────────────────────────────────

it('updates a product name', function () {
    actingAsProductsProductApiUser(['update_product_product']);

    $product = Product::factory()->create();

    $this->patchJson(productsProductRoute('update', $product), ['name' => 'Updated Product Name'])
        ->assertOk()
        ->assertJsonPath('message', 'Product updated successfully.')
        ->assertJsonPath('data.name', 'Updated Product Name');

    $this->assertDatabaseHas('products_products', [
        'id'   => $product->id,
        'name' => 'Updated Product Name',
    ]);
});

it('updates product price and cost', function () {
    actingAsProductsProductApiUser(['update_product_product']);

    $product = Product::factory()->create();

    $this->patchJson(productsProductRoute('update', $product), [
        'price' => 149.99,
        'cost'  => 89.99,
    ])
        ->assertOk()
        ->assertJsonPath('data.price', 149.99);

    $this->assertDatabaseHas('products_products', [
        'id'    => $product->id,
        'price' => 149.99,
    ]);
});

it('syncs tags when updating a product', function () {
    actingAsProductsProductApiUser(['update_product_product']);

    $product = Product::factory()->create();
    $tag1 = Tag::factory()->create();
    $tag2 = Tag::factory()->create();

    $this->patchJson(productsProductRoute('update', $product), [
        'tags' => [$tag1->id, $tag2->id],
    ])
        ->assertOk();

    $this->assertDatabaseHas('products_product_tag', [
        'product_id' => $product->id,
        'tag_id'     => $tag1->id,
    ]);
});

it('returns 404 when updating a non-existent product', function () {
    actingAsProductsProductApiUser(['update_product_product']);

    $this->patchJson(productsProductRoute('update', 999999), ['name' => 'X'])
        ->assertNotFound();
});

// ── Destroy (Soft Delete) ─────────────────────────────────────────────────────

it('soft deletes a product', function () {
    actingAsProductsProductApiUser(['delete_product_product']);

    $product = Product::factory()->create();

    $this->deleteJson(productsProductRoute('destroy', $product))
        ->assertOk()
        ->assertJsonPath('message', 'Product deleted successfully.');

    $this->assertSoftDeleted('products_products', ['id' => $product->id]);
});

it('returns 404 when deleting a non-existent product', function () {
    actingAsProductsProductApiUser(['delete_product_product']);

    $this->deleteJson(productsProductRoute('destroy', 999999))
        ->assertNotFound();
});

// ── Restore ───────────────────────────────────────────────────────────────────

it('restores a soft-deleted product', function () {
    actingAsProductsProductApiUser(['restore_product_product']);

    $product = Product::factory()->create();
    $product->delete();

    $this->postJson(productsProductRoute('restore', $product))
        ->assertOk()
        ->assertJsonPath('message', 'Product restored successfully.');

    $this->assertDatabaseHas('products_products', [
        'id'         => $product->id,
        'deleted_at' => null,
    ]);
});

it('returns 404 when restoring a non-existent product', function () {
    actingAsProductsProductApiUser(['restore_product_product']);

    $this->postJson(productsProductRoute('restore', 999999))
        ->assertNotFound();
});

// ── Force Delete ──────────────────────────────────────────────────────────────

it('permanently deletes a product', function () {
    actingAsProductsProductApiUser(['force_delete_product_product']);

    $product = Product::factory()->create();
    $product->delete();

    $this->deleteJson(productsProductRoute('force-destroy', $product))
        ->assertOk()
        ->assertJsonPath('message', 'Product permanently deleted.');

    $this->assertDatabaseMissing('products_products', ['id' => $product->id]);
});

it('returns 404 when force-deleting a non-existent product', function () {
    actingAsProductsProductApiUser(['force_delete_product_product']);

    $this->deleteJson(productsProductRoute('force-destroy', 999999))
        ->assertNotFound();
});
