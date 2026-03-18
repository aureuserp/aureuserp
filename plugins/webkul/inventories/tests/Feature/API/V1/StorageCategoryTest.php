<?php

use Webkul\Inventory\Enums\AllowNewProduct;
use Webkul\Inventory\Models\StorageCategory;
use Webkul\Security\Enums\PermissionType;
use Webkul\Security\Models\User;

require_once __DIR__.'/../../../../../support/tests/Helpers/SecurityHelper.php';
require_once __DIR__.'/../../../../../support/tests/Helpers/TestBootstrapHelper.php';

const INVENTORY_STORAGE_CATEGORY_JSON_STRUCTURE = [
    'id',
    'name',
];

const INVENTORY_STORAGE_CATEGORY_REQUIRED_FIELDS = [
    'name',
    'allow_new_products',
];

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('inventories');
    SecurityHelper::disableUserEvents();
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

function actingAsInventoryStorageCategoryApiUser(array $permissions = []): User
{
    $user = SecurityHelper::authenticateWithPermissions($permissions);

    $user->forceFill([
        'resource_permission' => PermissionType::GLOBAL,
    ])->saveQuietly();

    return $user;
}

function inventoryStorageCategoryRoute(string $action, mixed $storageCategory = null): string
{
    $name = "admin.api.v1.inventories.storage-categories.{$action}";

    return $storageCategory ? route($name, $storageCategory) : route($name);
}

function inventoryStorageCategoryPayload(array $overrides = []): array
{
    return array_replace_recursive([
        'name'               => 'Heavy Goods',
        'allow_new_products' => AllowNewProduct::MIXED->value,
    ], $overrides);
}

// ── Authentication ────────────────────────────────────────────────────────────

it('requires authentication to list storage categories', function () {
    $this->getJson(inventoryStorageCategoryRoute('index'))
        ->assertUnauthorized();
});

it('requires authentication to create a storage category', function () {
    $this->postJson(inventoryStorageCategoryRoute('store'), [])
        ->assertUnauthorized();
});

it('requires authentication to show a storage category', function () {
    $storageCategory = StorageCategory::factory()->create();

    $this->getJson(inventoryStorageCategoryRoute('show', $storageCategory))
        ->assertUnauthorized();
});

it('requires authentication to update a storage category', function () {
    $storageCategory = StorageCategory::factory()->create();

    $this->patchJson(inventoryStorageCategoryRoute('update', $storageCategory), [])
        ->assertUnauthorized();
});

it('requires authentication to delete a storage category', function () {
    $storageCategory = StorageCategory::factory()->create();

    $this->deleteJson(inventoryStorageCategoryRoute('destroy', $storageCategory))
        ->assertUnauthorized();
});

// ── Authorization ─────────────────────────────────────────────────────────────

it('forbids listing storage categories without permission', function () {
    actingAsInventoryStorageCategoryApiUser();

    $this->getJson(inventoryStorageCategoryRoute('index'))
        ->assertForbidden();
});

it('forbids creating a storage category without permission', function () {
    actingAsInventoryStorageCategoryApiUser();

    $this->postJson(inventoryStorageCategoryRoute('store'), inventoryStorageCategoryPayload())
        ->assertForbidden();
});

it('forbids showing a storage category without permission', function () {
    actingAsInventoryStorageCategoryApiUser();

    $storageCategory = StorageCategory::factory()->create();

    $this->getJson(inventoryStorageCategoryRoute('show', $storageCategory))
        ->assertForbidden();
});

it('forbids updating a storage category without permission', function () {
    actingAsInventoryStorageCategoryApiUser();

    $storageCategory = StorageCategory::factory()->create();

    $this->patchJson(inventoryStorageCategoryRoute('update', $storageCategory), [])
        ->assertForbidden();
});

it('forbids deleting a storage category without permission', function () {
    actingAsInventoryStorageCategoryApiUser();

    $storageCategory = StorageCategory::factory()->create();

    $this->deleteJson(inventoryStorageCategoryRoute('destroy', $storageCategory))
        ->assertForbidden();
});

// ── Index ─────────────────────────────────────────────────────────────────────

it('lists storage categories for authorized users', function () {
    actingAsInventoryStorageCategoryApiUser(['view_any_inventory_storage::category']);

    StorageCategory::factory()->count(3)->create();

    $this->getJson(inventoryStorageCategoryRoute('index'))
        ->assertOk()
        ->assertJsonStructure(['data', 'meta', 'links']);
});

it('filters storage categories by name', function () {
    actingAsInventoryStorageCategoryApiUser(['view_any_inventory_storage::category']);

    $category = StorageCategory::factory()->create(['name' => 'UniqueCategoryXYZ']);
    StorageCategory::factory()->count(2)->create();

    $response = $this->getJson(inventoryStorageCategoryRoute('index').'?filter[name]=UniqueCategoryXYZ')
        ->assertOk();

    $ids = collect($response->json('data'))->pluck('id');

    expect($ids)->toContain($category->id);
});

it('filters storage categories by allow_new_products', function () {
    actingAsInventoryStorageCategoryApiUser(['view_any_inventory_storage::category']);

    $emptyOnly = StorageCategory::factory()->emptyOnly()->create();
    StorageCategory::factory()->sameProduct()->create();

    $response = $this->getJson(inventoryStorageCategoryRoute('index').'?filter[allow_new_products]=empty')
        ->assertOk();

    $ids = collect($response->json('data'))->pluck('id');

    expect($ids)->toContain($emptyOnly->id);
});

// ── Store ─────────────────────────────────────────────────────────────────────

it('creates a storage category', function () {
    actingAsInventoryStorageCategoryApiUser(['create_inventory_storage::category']);

    $payload = inventoryStorageCategoryPayload();

    $this->postJson(inventoryStorageCategoryRoute('store'), $payload)
        ->assertCreated()
        ->assertJsonPath('message', 'Storage category created successfully.')
        ->assertJsonPath('data.name', $payload['name'])
        ->assertJsonStructure(['data' => INVENTORY_STORAGE_CATEGORY_JSON_STRUCTURE]);

    $this->assertDatabaseHas('inventories_storage_categories', [
        'name' => $payload['name'],
    ]);
});

it('validates required fields when creating a storage category', function (string $field) {
    actingAsInventoryStorageCategoryApiUser(['create_inventory_storage::category']);

    $payload = inventoryStorageCategoryPayload();
    unset($payload[$field]);

    $this->postJson(inventoryStorageCategoryRoute('store'), $payload)
        ->assertUnprocessable()
        ->assertJsonValidationErrors([$field]);
})->with(INVENTORY_STORAGE_CATEGORY_REQUIRED_FIELDS);

it('rejects an invalid allow_new_products value', function () {
    actingAsInventoryStorageCategoryApiUser(['create_inventory_storage::category']);

    $this->postJson(inventoryStorageCategoryRoute('store'), inventoryStorageCategoryPayload(['allow_new_products' => 'invalid']))
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['allow_new_products']);
});

it('creates a storage category with max_weight', function () {
    actingAsInventoryStorageCategoryApiUser(['create_inventory_storage::category']);

    $payload = inventoryStorageCategoryPayload(['max_weight' => 500.0]);

    $this->postJson(inventoryStorageCategoryRoute('store'), $payload)
        ->assertCreated();

    $this->assertDatabaseHas('inventories_storage_categories', [
        'name'       => $payload['name'],
        'max_weight' => 500.0,
    ]);
});

// ── Show ──────────────────────────────────────────────────────────────────────

it('shows a storage category for authorized users', function () {
    actingAsInventoryStorageCategoryApiUser(['view_inventory_storage::category']);

    $storageCategory = StorageCategory::factory()->create();

    $this->getJson(inventoryStorageCategoryRoute('show', $storageCategory))
        ->assertOk()
        ->assertJsonPath('data.id', $storageCategory->id)
        ->assertJsonPath('data.name', $storageCategory->name)
        ->assertJsonStructure(['data' => INVENTORY_STORAGE_CATEGORY_JSON_STRUCTURE]);
});

it('returns 404 for a non-existent storage category', function () {
    actingAsInventoryStorageCategoryApiUser(['view_inventory_storage::category']);

    $this->getJson(inventoryStorageCategoryRoute('show', 999999))
        ->assertNotFound();
});

// ── Update ────────────────────────────────────────────────────────────────────

it('updates a storage category', function () {
    actingAsInventoryStorageCategoryApiUser(['update_inventory_storage::category']);

    $storageCategory = StorageCategory::factory()->create();

    $this->patchJson(inventoryStorageCategoryRoute('update', $storageCategory), ['name' => 'Updated Category'])
        ->assertOk()
        ->assertJsonPath('message', 'Storage category updated successfully.')
        ->assertJsonPath('data.name', 'Updated Category');

    $this->assertDatabaseHas('inventories_storage_categories', [
        'id'   => $storageCategory->id,
        'name' => 'Updated Category',
    ]);
});

it('returns 404 when updating a non-existent storage category', function () {
    actingAsInventoryStorageCategoryApiUser(['update_inventory_storage::category']);

    $this->patchJson(inventoryStorageCategoryRoute('update', 999999), ['name' => 'X'])
        ->assertNotFound();
});

// ── Destroy ───────────────────────────────────────────────────────────────────

it('deletes a storage category', function () {
    actingAsInventoryStorageCategoryApiUser(['delete_inventory_storage::category']);

    $storageCategory = StorageCategory::factory()->create();

    $this->deleteJson(inventoryStorageCategoryRoute('destroy', $storageCategory))
        ->assertOk()
        ->assertJsonPath('message', 'Storage category deleted successfully.');

    $this->assertDatabaseMissing('inventories_storage_categories', ['id' => $storageCategory->id]);
});

it('returns 404 when deleting a non-existent storage category', function () {
    actingAsInventoryStorageCategoryApiUser(['delete_inventory_storage::category']);

    $this->deleteJson(inventoryStorageCategoryRoute('destroy', 999999))
        ->assertNotFound();
});
