<?php

use Webkul\Inventory\Enums\LocationType;
use Webkul\Inventory\Models\Location;
use Webkul\Security\Enums\PermissionType;
use Webkul\Security\Models\User;

require_once __DIR__.'/../../../../../support/tests/Helpers/SecurityHelper.php';
require_once __DIR__.'/../../../../../support/tests/Helpers/TestBootstrapHelper.php';

const INVENTORY_LOCATION_JSON_STRUCTURE = [
    'id',
    'name',
    'type',
];

const INVENTORY_LOCATION_REQUIRED_FIELDS = [
    'name',
    'type',
];

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('inventories');
    SecurityHelper::disableUserEvents();
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

function actingAsInventoryLocationApiUser(array $permissions = []): User
{
    $user = SecurityHelper::authenticateWithPermissions($permissions);

    $user->forceFill([
        'resource_permission' => PermissionType::GLOBAL,
    ])->saveQuietly();

    return $user;
}

function inventoryLocationRoute(string $action, mixed $location = null): string
{
    $name = "admin.api.v1.inventories.locations.{$action}";

    return $location ? route($name, $location) : route($name);
}

function inventoryLocationPayload(array $overrides = []): array
{
    return array_replace_recursive([
        'name' => 'Test Location',
        'type' => LocationType::INTERNAL->value,
    ], $overrides);
}

// ── Authentication ────────────────────────────────────────────────────────────

it('requires authentication to list locations', function () {
    $this->getJson(inventoryLocationRoute('index'))
        ->assertUnauthorized();
});

it('requires authentication to create a location', function () {
    $this->postJson(inventoryLocationRoute('store'), [])
        ->assertUnauthorized();
});

it('requires authentication to show a location', function () {
    $location = Location::factory()->create();

    $this->getJson(inventoryLocationRoute('show', $location))
        ->assertUnauthorized();
});

it('requires authentication to update a location', function () {
    $location = Location::factory()->create();

    $this->patchJson(inventoryLocationRoute('update', $location), [])
        ->assertUnauthorized();
});

it('requires authentication to delete a location', function () {
    $location = Location::factory()->create();

    $this->deleteJson(inventoryLocationRoute('destroy', $location))
        ->assertUnauthorized();
});

it('requires authentication to restore a location', function () {
    $location = Location::factory()->create();
    $location->delete();

    $this->postJson(inventoryLocationRoute('restore', $location))
        ->assertUnauthorized();
});

it('requires authentication to force-delete a location', function () {
    $location = Location::factory()->create();
    $location->delete();

    $this->deleteJson(inventoryLocationRoute('force-destroy', $location))
        ->assertUnauthorized();
});

// ── Authorization ─────────────────────────────────────────────────────────────

it('forbids listing locations without permission', function () {
    actingAsInventoryLocationApiUser();

    $this->getJson(inventoryLocationRoute('index'))
        ->assertForbidden();
});

it('forbids creating a location without permission', function () {
    actingAsInventoryLocationApiUser();

    $this->postJson(inventoryLocationRoute('store'), inventoryLocationPayload())
        ->assertForbidden();
});

it('forbids showing a location without permission', function () {
    actingAsInventoryLocationApiUser();

    $location = Location::factory()->create();

    $this->getJson(inventoryLocationRoute('show', $location))
        ->assertForbidden();
});

it('forbids updating a location without permission', function () {
    actingAsInventoryLocationApiUser();

    $location = Location::factory()->create();

    $this->patchJson(inventoryLocationRoute('update', $location), [])
        ->assertForbidden();
});

it('forbids deleting a location without permission', function () {
    actingAsInventoryLocationApiUser();

    $location = Location::factory()->create();

    $this->deleteJson(inventoryLocationRoute('destroy', $location))
        ->assertForbidden();
});

it('forbids restoring a location without permission', function () {
    actingAsInventoryLocationApiUser();

    $location = Location::factory()->create();
    $location->delete();

    $this->postJson(inventoryLocationRoute('restore', $location))
        ->assertForbidden();
});

it('forbids force-deleting a location without permission', function () {
    actingAsInventoryLocationApiUser();

    $location = Location::factory()->create();
    $location->delete();

    $this->deleteJson(inventoryLocationRoute('force-destroy', $location))
        ->assertForbidden();
});

// ── Index ─────────────────────────────────────────────────────────────────────

it('lists locations for authorized users', function () {
    actingAsInventoryLocationApiUser(['view_any_inventory_location']);

    Location::factory()->count(3)->create();

    $this->getJson(inventoryLocationRoute('index'))
        ->assertOk()
        ->assertJsonStructure(['data', 'meta', 'links']);
});

it('filters locations by name', function () {
    actingAsInventoryLocationApiUser(['view_any_inventory_location']);

    $location = Location::factory()->create(['name' => 'UniqueLocationXYZ']);
    Location::factory()->count(2)->create();

    $response = $this->getJson(inventoryLocationRoute('index').'?filter[name]=UniqueLocationXYZ')
        ->assertOk();

    $ids = collect($response->json('data'))->pluck('id');

    expect($ids)->toContain($location->id);
});

it('filters locations by type', function () {
    actingAsInventoryLocationApiUser(['view_any_inventory_location']);

    $internal = Location::factory()->create(['type' => LocationType::INTERNAL]);
    Location::factory()->create(['type' => LocationType::SUPPLIER]);

    $response = $this->getJson(inventoryLocationRoute('index').'?filter[type]=internal')
        ->assertOk();

    $ids = collect($response->json('data'))->pluck('id');

    expect($ids)->toContain($internal->id);
});

it('excludes soft-deleted locations from default listing', function () {
    actingAsInventoryLocationApiUser(['view_any_inventory_location']);

    $active = Location::factory()->create();
    $deleted = Location::factory()->create();
    $deleted->delete();

    $response = $this->getJson(inventoryLocationRoute('index'))
        ->assertOk();

    $ids = collect($response->json('data'))->pluck('id');

    expect($ids)->toContain($active->id)
        ->and($ids)->not->toContain($deleted->id);
});

// ── Store ─────────────────────────────────────────────────────────────────────

it('creates a location', function () {
    actingAsInventoryLocationApiUser(['create_inventory_location']);

    $payload = inventoryLocationPayload();

    $this->postJson(inventoryLocationRoute('store'), $payload)
        ->assertCreated()
        ->assertJsonPath('message', 'Location created successfully.')
        ->assertJsonPath('data.name', $payload['name'])
        ->assertJsonStructure(['data' => INVENTORY_LOCATION_JSON_STRUCTURE]);

    $this->assertDatabaseHas('inventories_locations', [
        'name' => $payload['name'],
    ]);
});

it('validates required fields when creating a location', function (string $field) {
    actingAsInventoryLocationApiUser(['create_inventory_location']);

    $payload = inventoryLocationPayload();
    unset($payload[$field]);

    $this->postJson(inventoryLocationRoute('store'), $payload)
        ->assertUnprocessable()
        ->assertJsonValidationErrors([$field]);
})->with(INVENTORY_LOCATION_REQUIRED_FIELDS);

it('rejects an invalid location type', function () {
    actingAsInventoryLocationApiUser(['create_inventory_location']);

    $this->postJson(inventoryLocationRoute('store'), inventoryLocationPayload(['type' => 'invalid_type']))
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['type']);
});

it('creates a location with a parent', function () {
    actingAsInventoryLocationApiUser(['create_inventory_location']);

    $parent = Location::factory()->create();
    $payload = inventoryLocationPayload(['parent_id' => $parent->id]);

    $this->postJson(inventoryLocationRoute('store'), $payload)
        ->assertCreated();

    $this->assertDatabaseHas('inventories_locations', [
        'name'      => $payload['name'],
        'parent_id' => $parent->id,
    ]);
});

// ── Show ──────────────────────────────────────────────────────────────────────

it('shows a location for authorized users', function () {
    actingAsInventoryLocationApiUser(['view_inventory_location']);

    $location = Location::factory()->create();

    $this->getJson(inventoryLocationRoute('show', $location))
        ->assertOk()
        ->assertJsonPath('data.id', $location->id)
        ->assertJsonPath('data.name', $location->name)
        ->assertJsonStructure(['data' => INVENTORY_LOCATION_JSON_STRUCTURE]);
});

it('returns 404 for a non-existent location', function () {
    actingAsInventoryLocationApiUser(['view_inventory_location']);

    $this->getJson(inventoryLocationRoute('show', 999999))
        ->assertNotFound();
});

// ── Update ────────────────────────────────────────────────────────────────────

it('updates a location', function () {
    actingAsInventoryLocationApiUser(['update_inventory_location']);

    $location = Location::factory()->create();

    $this->patchJson(inventoryLocationRoute('update', $location), ['name' => 'Updated Location'])
        ->assertOk()
        ->assertJsonPath('message', 'Location updated successfully.')
        ->assertJsonPath('data.name', 'Updated Location');

    $this->assertDatabaseHas('inventories_locations', [
        'id'   => $location->id,
        'name' => 'Updated Location',
    ]);
});

it('returns 404 when updating a non-existent location', function () {
    actingAsInventoryLocationApiUser(['update_inventory_location']);

    $this->patchJson(inventoryLocationRoute('update', 999999), ['name' => 'X'])
        ->assertNotFound();
});

// ── Destroy ───────────────────────────────────────────────────────────────────

it('soft deletes a location', function () {
    actingAsInventoryLocationApiUser(['delete_inventory_location']);

    $location = Location::factory()->create();

    $this->deleteJson(inventoryLocationRoute('destroy', $location))
        ->assertOk()
        ->assertJsonPath('message', 'Location deleted successfully.');

    $this->assertSoftDeleted('inventories_locations', ['id' => $location->id]);
});

it('returns 404 when deleting a non-existent location', function () {
    actingAsInventoryLocationApiUser(['delete_inventory_location']);

    $this->deleteJson(inventoryLocationRoute('destroy', 999999))
        ->assertNotFound();
});

// ── Restore ───────────────────────────────────────────────────────────────────

it('restores a soft-deleted location', function () {
    actingAsInventoryLocationApiUser(['restore_inventory_location']);

    $location = Location::factory()->create();
    $location->delete();

    $this->postJson(inventoryLocationRoute('restore', $location))
        ->assertOk()
        ->assertJsonPath('message', 'Location restored successfully.');

    $this->assertDatabaseHas('inventories_locations', [
        'id'         => $location->id,
        'deleted_at' => null,
    ]);
});

it('returns 404 when restoring a non-existent location', function () {
    actingAsInventoryLocationApiUser(['restore_inventory_location']);

    $this->postJson(inventoryLocationRoute('restore', 999999))
        ->assertNotFound();
});

// ── Force Delete ──────────────────────────────────────────────────────────────

it('permanently deletes a location', function () {
    actingAsInventoryLocationApiUser(['force_delete_inventory_location']);

    $location = Location::factory()->create();
    $location->delete();

    $this->deleteJson(inventoryLocationRoute('force-destroy', $location))
        ->assertOk()
        ->assertJsonPath('message', 'Location permanently deleted successfully.');

    $this->assertDatabaseMissing('inventories_locations', ['id' => $location->id]);
});

it('returns 404 when force-deleting a non-existent location', function () {
    actingAsInventoryLocationApiUser(['force_delete_inventory_location']);

    $this->deleteJson(inventoryLocationRoute('force-destroy', 999999))
        ->assertNotFound();
});
