<?php

use Webkul\Inventory\Models\Warehouse;
use Webkul\Security\Enums\PermissionType;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

require_once __DIR__.'/../../../../../support/tests/Helpers/SecurityHelper.php';
require_once __DIR__.'/../../../../../support/tests/Helpers/TestBootstrapHelper.php';

const INVENTORY_WAREHOUSE_JSON_STRUCTURE = [
    'id',
    'name',
    'code',
];

const INVENTORY_WAREHOUSE_REQUIRED_FIELDS = [
    'name',
    'code',
    'company_id',
];

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('inventories');
    SecurityHelper::disableUserEvents();
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

function actingAsInventoryWarehouseApiUser(array $permissions = []): User
{
    $user = SecurityHelper::authenticateWithPermissions($permissions);

    $user->forceFill([
        'resource_permission' => PermissionType::GLOBAL,
    ])->saveQuietly();

    return $user;
}

function inventoryWarehouseRoute(string $action, mixed $warehouse = null): string
{
    $name = "admin.api.v1.inventories.warehouses.{$action}";

    return $warehouse ? route($name, $warehouse) : route($name);
}

function inventoryWarehousePayload(array $overrides = []): array
{
    $company = Company::factory()->create();

    return array_replace_recursive([
        'name'       => 'Test Warehouse',
        'code'       => 'TWH-'.uniqid(),
        'company_id' => $company->id,
    ], $overrides);
}

// ── Authentication ────────────────────────────────────────────────────────────

it('requires authentication to list warehouses', function () {
    $this->getJson(inventoryWarehouseRoute('index'))
        ->assertUnauthorized();
});

it('requires authentication to create a warehouse', function () {
    $this->postJson(inventoryWarehouseRoute('store'), [])
        ->assertUnauthorized();
});

it('requires authentication to show a warehouse', function () {
    $warehouse = Warehouse::factory()->create();

    $this->getJson(inventoryWarehouseRoute('show', $warehouse))
        ->assertUnauthorized();
});

it('requires authentication to update a warehouse', function () {
    $warehouse = Warehouse::factory()->create();

    $this->patchJson(inventoryWarehouseRoute('update', $warehouse), [])
        ->assertUnauthorized();
});

it('requires authentication to delete a warehouse', function () {
    $warehouse = Warehouse::factory()->create();

    $this->deleteJson(inventoryWarehouseRoute('destroy', $warehouse))
        ->assertUnauthorized();
});

it('requires authentication to restore a warehouse', function () {
    $warehouse = Warehouse::factory()->create();
    $warehouse->delete();

    $this->postJson(inventoryWarehouseRoute('restore', $warehouse))
        ->assertUnauthorized();
});

it('requires authentication to force-delete a warehouse', function () {
    $warehouse = Warehouse::factory()->create();
    $warehouse->delete();

    $this->deleteJson(inventoryWarehouseRoute('force-destroy', $warehouse))
        ->assertUnauthorized();
});

// ── Authorization ─────────────────────────────────────────────────────────────

it('forbids listing warehouses without permission', function () {
    actingAsInventoryWarehouseApiUser();

    $this->getJson(inventoryWarehouseRoute('index'))
        ->assertForbidden();
});

it('forbids creating a warehouse without permission', function () {
    actingAsInventoryWarehouseApiUser();

    $this->postJson(inventoryWarehouseRoute('store'), inventoryWarehousePayload())
        ->assertForbidden();
});

it('forbids showing a warehouse without permission', function () {
    actingAsInventoryWarehouseApiUser();

    $warehouse = Warehouse::factory()->create();

    $this->getJson(inventoryWarehouseRoute('show', $warehouse))
        ->assertForbidden();
});

it('forbids updating a warehouse without permission', function () {
    actingAsInventoryWarehouseApiUser();

    $warehouse = Warehouse::factory()->create();

    $this->patchJson(inventoryWarehouseRoute('update', $warehouse), [])
        ->assertForbidden();
});

it('forbids deleting a warehouse without permission', function () {
    actingAsInventoryWarehouseApiUser();

    $warehouse = Warehouse::factory()->create();

    $this->deleteJson(inventoryWarehouseRoute('destroy', $warehouse))
        ->assertForbidden();
});

it('forbids restoring a warehouse without permission', function () {
    actingAsInventoryWarehouseApiUser();

    $warehouse = Warehouse::factory()->create();
    $warehouse->delete();

    $this->postJson(inventoryWarehouseRoute('restore', $warehouse))
        ->assertForbidden();
});

it('forbids force-deleting a warehouse without permission', function () {
    actingAsInventoryWarehouseApiUser();

    $warehouse = Warehouse::factory()->create();
    $warehouse->delete();

    $this->deleteJson(inventoryWarehouseRoute('force-destroy', $warehouse))
        ->assertForbidden();
});

// ── Index ─────────────────────────────────────────────────────────────────────

it('lists warehouses for authorized users', function () {
    actingAsInventoryWarehouseApiUser(['view_any_inventory_warehouse']);

    Warehouse::factory()->count(3)->create();

    $this->getJson(inventoryWarehouseRoute('index'))
        ->assertOk()
        ->assertJsonStructure(['data', 'meta', 'links']);
});

it('filters warehouses by name', function () {
    actingAsInventoryWarehouseApiUser(['view_any_inventory_warehouse']);

    $warehouse = Warehouse::factory()->create(['name' => 'UniqueWarehouseXYZ']);
    Warehouse::factory()->count(2)->create();

    $response = $this->getJson(inventoryWarehouseRoute('index').'?filter[name]=UniqueWarehouseXYZ')
        ->assertOk();

    $ids = collect($response->json('data'))->pluck('id');

    expect($ids)->toContain($warehouse->id);
});

it('excludes soft-deleted warehouses from default listing', function () {
    actingAsInventoryWarehouseApiUser(['view_any_inventory_warehouse']);

    $active = Warehouse::factory()->create();
    $deleted = Warehouse::factory()->create();
    $deleted->delete();

    $response = $this->getJson(inventoryWarehouseRoute('index'))
        ->assertOk();

    $ids = collect($response->json('data'))->pluck('id');

    expect($ids)->toContain($active->id)
        ->and($ids)->not->toContain($deleted->id);
});

// ── Store ─────────────────────────────────────────────────────────────────────

it('creates a warehouse', function () {
    actingAsInventoryWarehouseApiUser(['create_inventory_warehouse']);

    $payload = inventoryWarehousePayload();

    $this->postJson(inventoryWarehouseRoute('store'), $payload)
        ->assertCreated()
        ->assertJsonPath('message', 'Warehouse created successfully.')
        ->assertJsonStructure(['data' => INVENTORY_WAREHOUSE_JSON_STRUCTURE]);

    $this->assertDatabaseHas('inventories_warehouses', [
        'name' => $payload['name'],
        'code' => $payload['code'],
    ]);
});

it('validates required fields when creating a warehouse', function (string $field) {
    actingAsInventoryWarehouseApiUser(['create_inventory_warehouse']);

    $payload = inventoryWarehousePayload();
    unset($payload[$field]);

    $this->postJson(inventoryWarehouseRoute('store'), $payload)
        ->assertUnprocessable()
        ->assertJsonValidationErrors([$field]);
})->with(INVENTORY_WAREHOUSE_REQUIRED_FIELDS);

it('rejects duplicate warehouse names', function () {
    actingAsInventoryWarehouseApiUser(['create_inventory_warehouse']);

    $existing = Warehouse::factory()->create(['name' => 'DuplicateWarehouse']);
    $company = Company::factory()->create();

    $this->postJson(inventoryWarehouseRoute('store'), [
        'name'       => $existing->name,
        'code'       => 'UNIQ-'.uniqid(),
        'company_id' => $company->id,
    ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['name']);
});

it('rejects duplicate warehouse codes', function () {
    actingAsInventoryWarehouseApiUser(['create_inventory_warehouse']);

    $existing = Warehouse::factory()->create(['code' => 'DUPCODE']);
    $company = Company::factory()->create();

    $this->postJson(inventoryWarehouseRoute('store'), [
        'name'       => 'New Warehouse',
        'code'       => $existing->code,
        'company_id' => $company->id,
    ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['code']);
});

// ── Show ──────────────────────────────────────────────────────────────────────

it('shows a warehouse for authorized users', function () {
    actingAsInventoryWarehouseApiUser(['view_inventory_warehouse']);

    $warehouse = Warehouse::factory()->create();

    $this->getJson(inventoryWarehouseRoute('show', $warehouse))
        ->assertOk()
        ->assertJsonPath('data.id', $warehouse->id)
        ->assertJsonPath('data.name', $warehouse->name)
        ->assertJsonStructure(['data' => INVENTORY_WAREHOUSE_JSON_STRUCTURE]);
});

it('returns 404 for a non-existent warehouse', function () {
    actingAsInventoryWarehouseApiUser(['view_inventory_warehouse']);

    $this->getJson(inventoryWarehouseRoute('show', 999999))
        ->assertNotFound();
});

// ── Update ────────────────────────────────────────────────────────────────────

it('updates a warehouse', function () {
    actingAsInventoryWarehouseApiUser(['update_inventory_warehouse']);

    $warehouse = Warehouse::factory()->create();

    $this->patchJson(inventoryWarehouseRoute('update', $warehouse), ['name' => 'Updated Warehouse'])
        ->assertOk()
        ->assertJsonPath('message', 'Warehouse updated successfully.')
        ->assertJsonPath('data.name', 'Updated Warehouse');

    $this->assertDatabaseHas('inventories_warehouses', [
        'id'   => $warehouse->id,
        'name' => 'Updated Warehouse',
    ]);
});

it('returns 404 when updating a non-existent warehouse', function () {
    actingAsInventoryWarehouseApiUser(['update_inventory_warehouse']);

    $this->patchJson(inventoryWarehouseRoute('update', 999999), ['name' => 'X'])
        ->assertNotFound();
});

// ── Destroy ───────────────────────────────────────────────────────────────────

it('soft deletes a warehouse', function () {
    actingAsInventoryWarehouseApiUser(['delete_inventory_warehouse']);

    $warehouse = Warehouse::factory()->create();

    $this->deleteJson(inventoryWarehouseRoute('destroy', $warehouse))
        ->assertOk()
        ->assertJsonPath('message', 'Warehouse deleted successfully.');

    $this->assertSoftDeleted('inventories_warehouses', ['id' => $warehouse->id]);
});

it('returns 404 when deleting a non-existent warehouse', function () {
    actingAsInventoryWarehouseApiUser(['delete_inventory_warehouse']);

    $this->deleteJson(inventoryWarehouseRoute('destroy', 999999))
        ->assertNotFound();
});

// ── Restore ───────────────────────────────────────────────────────────────────

it('restores a soft-deleted warehouse', function () {
    actingAsInventoryWarehouseApiUser(['restore_inventory_warehouse']);

    $warehouse = Warehouse::factory()->create();
    $warehouse->delete();

    $this->postJson(inventoryWarehouseRoute('restore', $warehouse))
        ->assertOk()
        ->assertJsonPath('message', 'Warehouse restored successfully.');

    $this->assertDatabaseHas('inventories_warehouses', [
        'id'         => $warehouse->id,
        'deleted_at' => null,
    ]);
});

it('returns 404 when restoring a non-existent warehouse', function () {
    actingAsInventoryWarehouseApiUser(['restore_inventory_warehouse']);

    $this->postJson(inventoryWarehouseRoute('restore', 999999))
        ->assertNotFound();
});

// ── Force Delete ──────────────────────────────────────────────────────────────

it('permanently deletes a warehouse', function () {
    actingAsInventoryWarehouseApiUser(['force_delete_inventory_warehouse']);

    $warehouse = Warehouse::factory()->create();
    $warehouse->delete();

    $this->deleteJson(inventoryWarehouseRoute('force-destroy', $warehouse))
        ->assertOk()
        ->assertJsonPath('message', 'Warehouse permanently deleted successfully.');

    $this->assertDatabaseMissing('inventories_warehouses', ['id' => $warehouse->id]);
});

it('returns 404 when force-deleting a non-existent warehouse', function () {
    actingAsInventoryWarehouseApiUser(['force_delete_inventory_warehouse']);

    $this->deleteJson(inventoryWarehouseRoute('force-destroy', 999999))
        ->assertNotFound();
});
