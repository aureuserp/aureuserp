<?php

use Webkul\Inventory\Models\Lot;
use Webkul\Product\Models\Product as BaseProduct;
use Webkul\Security\Enums\PermissionType;
use Webkul\Security\Models\User;

require_once __DIR__.'/../../../../../support/tests/Helpers/SecurityHelper.php';
require_once __DIR__.'/../../../../../support/tests/Helpers/TestBootstrapHelper.php';

const INVENTORY_LOT_JSON_STRUCTURE = [
    'id',
    'name',
];

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('inventories');
    SecurityHelper::disableUserEvents();
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

function actingAsInventoryLotApiUser(array $permissions = []): User
{
    $user = SecurityHelper::authenticateWithPermissions($permissions);

    $user->forceFill([
        'resource_permission' => PermissionType::GLOBAL,
    ])->saveQuietly();

    return $user;
}

function inventoryLotRoute(string $action, mixed $lot = null): string
{
    $name = "admin.api.v1.inventories.lots.{$action}";

    return $lot ? route($name, $lot) : route($name);
}

function inventoryLotPayload(array $overrides = []): array
{
    $product = BaseProduct::factory()->create();

    return array_replace_recursive([
        'name'       => 'LOT-'.uniqid(),
        'product_id' => $product->id,
    ], $overrides);
}

// ── Authentication ────────────────────────────────────────────────────────────

it('requires authentication to list lots', function () {
    $this->getJson(inventoryLotRoute('index'))
        ->assertUnauthorized();
});

it('requires authentication to create a lot', function () {
    $this->postJson(inventoryLotRoute('store'), [])
        ->assertUnauthorized();
});

it('requires authentication to show a lot', function () {
    $lot = Lot::factory()->create();

    $this->getJson(inventoryLotRoute('show', $lot))
        ->assertUnauthorized();
});

it('requires authentication to update a lot', function () {
    $lot = Lot::factory()->create();

    $this->patchJson(inventoryLotRoute('update', $lot), [])
        ->assertUnauthorized();
});

it('requires authentication to delete a lot', function () {
    $lot = Lot::factory()->create();

    $this->deleteJson(inventoryLotRoute('destroy', $lot))
        ->assertUnauthorized();
});

// ── Authorization ─────────────────────────────────────────────────────────────

it('forbids listing lots without permission', function () {
    actingAsInventoryLotApiUser();

    $this->getJson(inventoryLotRoute('index'))
        ->assertForbidden();
});

it('forbids creating a lot without permission', function () {
    actingAsInventoryLotApiUser();

    $this->postJson(inventoryLotRoute('store'), inventoryLotPayload())
        ->assertForbidden();
});

it('forbids showing a lot without permission', function () {
    actingAsInventoryLotApiUser();

    $lot = Lot::factory()->create();

    $this->getJson(inventoryLotRoute('show', $lot))
        ->assertForbidden();
});

it('forbids updating a lot without permission', function () {
    actingAsInventoryLotApiUser();

    $lot = Lot::factory()->create();

    $this->patchJson(inventoryLotRoute('update', $lot), [])
        ->assertForbidden();
});

it('forbids deleting a lot without permission', function () {
    actingAsInventoryLotApiUser();

    $lot = Lot::factory()->create();

    $this->deleteJson(inventoryLotRoute('destroy', $lot))
        ->assertForbidden();
});

// ── Index ─────────────────────────────────────────────────────────────────────

it('lists lots for authorized users', function () {
    actingAsInventoryLotApiUser(['view_any_inventory_lot']);

    Lot::factory()->count(3)->create();

    $this->getJson(inventoryLotRoute('index'))
        ->assertOk()
        ->assertJsonStructure(['data', 'meta', 'links']);
});

it('filters lots by name', function () {
    actingAsInventoryLotApiUser(['view_any_inventory_lot']);

    $lot = Lot::factory()->create(['name' => 'UNIQUE-LOT-XYZ']);
    Lot::factory()->count(2)->create();

    $response = $this->getJson(inventoryLotRoute('index').'?filter[name]=UNIQUE-LOT-XYZ')
        ->assertOk();

    $ids = collect($response->json('data'))->pluck('id');

    expect($ids)->toContain($lot->id);
});

// ── Store ─────────────────────────────────────────────────────────────────────

it('creates a lot', function () {
    actingAsInventoryLotApiUser(['create_inventory_lot']);

    $payload = inventoryLotPayload();

    $this->postJson(inventoryLotRoute('store'), $payload)
        ->assertCreated()
        ->assertJsonPath('message', 'Lot created successfully.')
        ->assertJsonPath('data.name', $payload['name'])
        ->assertJsonStructure(['data' => INVENTORY_LOT_JSON_STRUCTURE]);

    $this->assertDatabaseHas('inventories_lots', ['name' => $payload['name']]);
});

it('validates required fields when creating a lot', function (string $field) {
    actingAsInventoryLotApiUser(['create_inventory_lot']);

    $payload = inventoryLotPayload();
    unset($payload[$field]);

    $this->postJson(inventoryLotRoute('store'), $payload)
        ->assertUnprocessable()
        ->assertJsonValidationErrors([$field]);
})->with(['name', 'product_id']);

it('rejects a non-existent product_id', function () {
    actingAsInventoryLotApiUser(['create_inventory_lot']);

    $this->postJson(inventoryLotRoute('store'), [
        'name'       => 'LOT-'.uniqid(),
        'product_id' => 999999,
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['product_id']);
});

it('creates a lot with optional reference and description', function () {
    actingAsInventoryLotApiUser(['create_inventory_lot']);

    $payload = inventoryLotPayload([
        'reference'   => 'BATCH-A1',
        'description' => 'Primary production batch.',
    ]);

    $this->postJson(inventoryLotRoute('store'), $payload)
        ->assertCreated();

    $this->assertDatabaseHas('inventories_lots', [
        'name'      => $payload['name'],
        'reference' => 'BATCH-A1',
    ]);
});

// ── Show ──────────────────────────────────────────────────────────────────────

it('shows a lot for authorized users', function () {
    actingAsInventoryLotApiUser(['view_inventory_lot']);

    $lot = Lot::factory()->create();

    $this->getJson(inventoryLotRoute('show', $lot))
        ->assertOk()
        ->assertJsonPath('data.id', $lot->id)
        ->assertJsonPath('data.name', $lot->name)
        ->assertJsonStructure(['data' => INVENTORY_LOT_JSON_STRUCTURE]);
});

it('returns 404 for a non-existent lot', function () {
    actingAsInventoryLotApiUser(['view_inventory_lot']);

    $this->getJson(inventoryLotRoute('show', 999999))
        ->assertNotFound();
});

// ── Update ────────────────────────────────────────────────────────────────────

it('updates a lot', function () {
    actingAsInventoryLotApiUser(['update_inventory_lot']);

    $lot = Lot::factory()->create();

    $this->patchJson(inventoryLotRoute('update', $lot), ['name' => 'LOT-UPDATED'])
        ->assertOk()
        ->assertJsonPath('message', 'Lot updated successfully.')
        ->assertJsonPath('data.name', 'LOT-UPDATED');

    $this->assertDatabaseHas('inventories_lots', [
        'id'   => $lot->id,
        'name' => 'LOT-UPDATED',
    ]);
});

it('returns 404 when updating a non-existent lot', function () {
    actingAsInventoryLotApiUser(['update_inventory_lot']);

    $this->patchJson(inventoryLotRoute('update', 999999), ['name' => 'X'])
        ->assertNotFound();
});

// ── Destroy ───────────────────────────────────────────────────────────────────

it('deletes a lot', function () {
    actingAsInventoryLotApiUser(['delete_inventory_lot']);

    $lot = Lot::factory()->create();

    $this->deleteJson(inventoryLotRoute('destroy', $lot))
        ->assertOk()
        ->assertJsonPath('message', 'Lot deleted successfully.');

    $this->assertDatabaseMissing('inventories_lots', ['id' => $lot->id]);
});

it('returns 404 when deleting a non-existent lot', function () {
    actingAsInventoryLotApiUser(['delete_inventory_lot']);

    $this->deleteJson(inventoryLotRoute('destroy', 999999))
        ->assertNotFound();
});
