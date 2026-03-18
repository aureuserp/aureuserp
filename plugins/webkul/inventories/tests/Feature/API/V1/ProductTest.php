<?php

use Webkul\Inventory\Models\Product;
use Webkul\Product\Enums\ProductType;
use Webkul\Product\Models\Category;
use Webkul\Security\Enums\PermissionType;
use Webkul\Security\Models\User;
use Webkul\Support\Models\UOM;

require_once __DIR__.'/../../../../../support/tests/Helpers/SecurityHelper.php';
require_once __DIR__.'/../../../../../support/tests/Helpers/TestBootstrapHelper.php';

const INVENTORY_PRODUCT_JSON_STRUCTURE = [
    'id',
    'name',
    'type',
];

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('inventories');
    SecurityHelper::disableUserEvents();
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

function actingAsInventoryProductApiUser(array $permissions = []): User
{
    $user = SecurityHelper::authenticateWithPermissions($permissions);

    $user->forceFill([
        'resource_permission' => PermissionType::GLOBAL,
    ])->saveQuietly();

    return $user;
}

function inventoryProductRoute(string $action, mixed $product = null): string
{
    $name = "admin.api.v1.inventories.products.{$action}";

    return $product ? route($name, $product) : route($name);
}

function inventoryProductPayload(array $overrides = []): array
{
    $category = Category::factory()->create();
    $uom = UOM::factory()->create();

    return array_replace_recursive([
        'type'        => ProductType::GOODS->value,
        'name'        => 'Test Inventory Product '.uniqid(),
        'price'       => 100.00,
        'category_id' => $category->id,
        'uom_id'      => $uom->id,
        'uom_po_id'   => $uom->id,
    ], $overrides);
}

// ── Authentication ────────────────────────────────────────────────────────────

it('requires authentication to list inventory products', function () {
    $this->getJson(inventoryProductRoute('index'))
        ->assertUnauthorized();
});

it('requires authentication to create an inventory product', function () {
    $this->postJson(inventoryProductRoute('store'), [])
        ->assertUnauthorized();
});

it('requires authentication to show an inventory product', function () {
    $product = Product::factory()->create();

    $this->getJson(inventoryProductRoute('show', $product))
        ->assertUnauthorized();
});

it('requires authentication to update an inventory product', function () {
    $product = Product::factory()->create();

    $this->patchJson(inventoryProductRoute('update', $product), [])
        ->assertUnauthorized();
});

it('requires authentication to delete an inventory product', function () {
    $product = Product::factory()->create();

    $this->deleteJson(inventoryProductRoute('destroy', $product))
        ->assertUnauthorized();
});

it('requires authentication to restore an inventory product', function () {
    $product = Product::factory()->create();
    $product->delete();

    $this->postJson(inventoryProductRoute('restore', $product->id))
        ->assertUnauthorized();
});

it('requires authentication to force-delete an inventory product', function () {
    $product = Product::factory()->create();
    $product->delete();

    $this->deleteJson(inventoryProductRoute('force-destroy', $product->id))
        ->assertUnauthorized();
});

// ── Authorization ─────────────────────────────────────────────────────────────

it('forbids listing inventory products without permission', function () {
    actingAsInventoryProductApiUser();

    $this->getJson(inventoryProductRoute('index'))
        ->assertForbidden();
});

it('forbids creating an inventory product without permission', function () {
    actingAsInventoryProductApiUser();

    $this->postJson(inventoryProductRoute('store'), inventoryProductPayload())
        ->assertForbidden();
});

it('forbids showing an inventory product without permission', function () {
    actingAsInventoryProductApiUser();

    $product = Product::factory()->create();

    $this->getJson(inventoryProductRoute('show', $product))
        ->assertForbidden();
});

it('forbids updating an inventory product without permission', function () {
    actingAsInventoryProductApiUser();

    $product = Product::factory()->create();

    $this->patchJson(inventoryProductRoute('update', $product), [])
        ->assertForbidden();
});

it('forbids deleting an inventory product without permission', function () {
    actingAsInventoryProductApiUser();

    $product = Product::factory()->create();

    $this->deleteJson(inventoryProductRoute('destroy', $product))
        ->assertForbidden();
});

it('forbids restoring an inventory product without permission', function () {
    actingAsInventoryProductApiUser();

    $product = Product::factory()->create();
    $product->delete();

    $this->postJson(inventoryProductRoute('restore', $product->id))
        ->assertForbidden();
});

it('forbids force-deleting an inventory product without permission', function () {
    actingAsInventoryProductApiUser();

    $product = Product::factory()->create();
    $product->delete();

    $this->deleteJson(inventoryProductRoute('force-destroy', $product->id))
        ->assertForbidden();
});

// ── Index ─────────────────────────────────────────────────────────────────────

it('lists inventory products for authorized users', function () {
    actingAsInventoryProductApiUser(['view_any_inventory_product']);

    Product::factory()->count(3)->create();

    $this->getJson(inventoryProductRoute('index'))
        ->assertOk()
        ->assertJsonStructure(['data', 'meta', 'links']);
});

it('filters inventory products by name', function () {
    actingAsInventoryProductApiUser(['view_any_inventory_product']);

    $product = Product::factory()->create(['name' => 'UniqueInventoryProductXYZ']);
    Product::factory()->count(2)->create();

    $response = $this->getJson(inventoryProductRoute('index').'?filter[name]=UniqueInventoryProductXYZ')
        ->assertOk();

    $ids = collect($response->json('data'))->pluck('id');

    expect($ids)->toContain($product->id);
});

// ── Store ─────────────────────────────────────────────────────────────────────

it('creates an inventory product', function () {
    actingAsInventoryProductApiUser(['create_inventory_product']);

    $payload = inventoryProductPayload();

    $this->postJson(inventoryProductRoute('store'), $payload)
        ->assertCreated()
        ->assertJsonPath('message', 'Product created successfully.')
        ->assertJsonPath('data.name', $payload['name'])
        ->assertJsonStructure(['data' => INVENTORY_PRODUCT_JSON_STRUCTURE]);

    $this->assertDatabaseHas('products_products', ['name' => $payload['name']]);
});

it('validates required fields when creating an inventory product', function (string $field) {
    actingAsInventoryProductApiUser(['create_inventory_product']);

    $payload = inventoryProductPayload();
    unset($payload[$field]);

    $this->postJson(inventoryProductRoute('store'), $payload)
        ->assertUnprocessable()
        ->assertJsonValidationErrors([$field]);
})->with(['type', 'name', 'price', 'category_id']);

it('rejects an invalid product type when creating an inventory product', function () {
    actingAsInventoryProductApiUser(['create_inventory_product']);

    $this->postJson(inventoryProductRoute('store'), inventoryProductPayload(['type' => 'invalid']))
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['type']);
});

it('creates a storable inventory product with tracking', function () {
    actingAsInventoryProductApiUser(['create_inventory_product']);

    $payload = inventoryProductPayload([
        'is_storable' => true,
        'tracking'    => 'lot',
    ]);

    $this->postJson(inventoryProductRoute('store'), $payload)
        ->assertCreated();

    $this->assertDatabaseHas('products_products', ['name' => $payload['name']]);
});

// ── Show ──────────────────────────────────────────────────────────────────────

it('shows an inventory product for authorized users', function () {
    actingAsInventoryProductApiUser(['view_inventory_product']);

    $product = Product::factory()->create();

    $this->getJson(inventoryProductRoute('show', $product))
        ->assertOk()
        ->assertJsonPath('data.id', $product->id)
        ->assertJsonPath('data.name', $product->name)
        ->assertJsonStructure(['data' => INVENTORY_PRODUCT_JSON_STRUCTURE]);
});

it('returns 404 for a non-existent inventory product', function () {
    actingAsInventoryProductApiUser(['view_inventory_product']);

    $this->getJson(inventoryProductRoute('show', 999999))
        ->assertNotFound();
});

it('returns 404 for a soft-deleted inventory product', function () {
    actingAsInventoryProductApiUser(['view_inventory_product']);

    $product = Product::factory()->create();
    $product->delete();

    $this->getJson(inventoryProductRoute('show', $product))
        ->assertNotFound();
});

// ── Update ────────────────────────────────────────────────────────────────────

it('updates an inventory product', function () {
    actingAsInventoryProductApiUser(['update_inventory_product']);

    $product = Product::factory()->create();

    $this->patchJson(inventoryProductRoute('update', $product), ['name' => 'Updated Inventory Product'])
        ->assertOk()
        ->assertJsonPath('message', 'Product updated successfully.')
        ->assertJsonPath('data.name', 'Updated Inventory Product');

    $this->assertDatabaseHas('products_products', [
        'id'   => $product->id,
        'name' => 'Updated Inventory Product',
    ]);
});

it('returns 404 when updating a non-existent inventory product', function () {
    actingAsInventoryProductApiUser(['update_inventory_product']);

    $this->patchJson(inventoryProductRoute('update', 999999), ['name' => 'X'])
        ->assertNotFound();
});

// ── Destroy ───────────────────────────────────────────────────────────────────

it('soft-deletes an inventory product', function () {
    actingAsInventoryProductApiUser(['delete_inventory_product']);

    $product = Product::factory()->create();

    $this->deleteJson(inventoryProductRoute('destroy', $product))
        ->assertOk()
        ->assertJsonPath('message', 'Product deleted successfully.');

    $this->assertSoftDeleted('products_products', ['id' => $product->id]);
});

it('returns 404 when deleting a non-existent inventory product', function () {
    actingAsInventoryProductApiUser(['delete_inventory_product']);

    $this->deleteJson(inventoryProductRoute('destroy', 999999))
        ->assertNotFound();
});

// ── Restore ───────────────────────────────────────────────────────────────────

it('restores a soft-deleted inventory product', function () {
    actingAsInventoryProductApiUser(['restore_inventory_product']);

    $product = Product::factory()->create();
    $product->delete();

    $this->postJson(inventoryProductRoute('restore', $product->id))
        ->assertOk()
        ->assertJsonPath('message', 'Product restored successfully.');

    $this->assertDatabaseHas('products_products', [
        'id'         => $product->id,
        'deleted_at' => null,
    ]);
});

it('returns 404 when restoring a non-existent inventory product', function () {
    actingAsInventoryProductApiUser(['restore_inventory_product']);

    $this->postJson(inventoryProductRoute('restore', 999999))
        ->assertNotFound();
});

// ── Force Destroy ─────────────────────────────────────────────────────────────

it('permanently deletes a soft-deleted inventory product', function () {
    actingAsInventoryProductApiUser(['force_delete_inventory_product']);

    $product = Product::factory()->create();
    $product->delete();

    $this->deleteJson(inventoryProductRoute('force-destroy', $product->id))
        ->assertOk()
        ->assertJsonPath('message', 'Product permanently deleted successfully.');

    $this->assertDatabaseMissing('products_products', ['id' => $product->id]);
});

it('returns 404 when force-deleting a non-existent inventory product', function () {
    actingAsInventoryProductApiUser(['force_delete_inventory_product']);

    $this->deleteJson(inventoryProductRoute('force-destroy', 999999))
        ->assertNotFound();
});
