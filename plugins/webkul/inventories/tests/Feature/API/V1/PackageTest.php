<?php

use Webkul\Inventory\Models\Location;
use Webkul\Inventory\Models\Package;
use Webkul\Inventory\Models\PackageType;
use Webkul\Security\Enums\PermissionType;
use Webkul\Security\Models\User;

require_once __DIR__.'/../../../../../support/tests/Helpers/SecurityHelper.php';
require_once __DIR__.'/../../../../../support/tests/Helpers/TestBootstrapHelper.php';

const INVENTORY_PACKAGE_JSON_STRUCTURE = [
    'id',
    'name',
];

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('inventories');
    SecurityHelper::disableUserEvents();
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

function actingAsInventoryPackageApiUser(array $permissions = []): User
{
    $user = SecurityHelper::authenticateWithPermissions($permissions);

    $user->forceFill([
        'resource_permission' => PermissionType::GLOBAL,
    ])->saveQuietly();

    return $user;
}

function inventoryPackageRoute(string $action, mixed $package = null): string
{
    $name = "admin.api.v1.inventories.packages.{$action}";

    return $package ? route($name, $package) : route($name);
}

function inventoryPackagePayload(array $overrides = []): array
{
    return array_replace_recursive([
        'name' => 'PKG-'.uniqid(),
    ], $overrides);
}

// ── Authentication ────────────────────────────────────────────────────────────

it('requires authentication to list packages', function () {
    $this->getJson(inventoryPackageRoute('index'))
        ->assertUnauthorized();
});

it('requires authentication to create a package', function () {
    $this->postJson(inventoryPackageRoute('store'), [])
        ->assertUnauthorized();
});

it('requires authentication to show a package', function () {
    $package = Package::factory()->create();

    $this->getJson(inventoryPackageRoute('show', $package))
        ->assertUnauthorized();
});

it('requires authentication to update a package', function () {
    $package = Package::factory()->create();

    $this->patchJson(inventoryPackageRoute('update', $package), [])
        ->assertUnauthorized();
});

it('requires authentication to delete a package', function () {
    $package = Package::factory()->create();

    $this->deleteJson(inventoryPackageRoute('destroy', $package))
        ->assertUnauthorized();
});

// ── Authorization ─────────────────────────────────────────────────────────────

it('forbids listing packages without permission', function () {
    actingAsInventoryPackageApiUser();

    $this->getJson(inventoryPackageRoute('index'))
        ->assertForbidden();
});

it('forbids creating a package without permission', function () {
    actingAsInventoryPackageApiUser();

    $this->postJson(inventoryPackageRoute('store'), inventoryPackagePayload())
        ->assertForbidden();
});

it('forbids showing a package without permission', function () {
    actingAsInventoryPackageApiUser();

    $package = Package::factory()->create();

    $this->getJson(inventoryPackageRoute('show', $package))
        ->assertForbidden();
});

it('forbids updating a package without permission', function () {
    actingAsInventoryPackageApiUser();

    $package = Package::factory()->create();

    $this->patchJson(inventoryPackageRoute('update', $package), [])
        ->assertForbidden();
});

it('forbids deleting a package without permission', function () {
    actingAsInventoryPackageApiUser();

    $package = Package::factory()->create();

    $this->deleteJson(inventoryPackageRoute('destroy', $package))
        ->assertForbidden();
});

// ── Index ─────────────────────────────────────────────────────────────────────

it('lists packages for authorized users', function () {
    actingAsInventoryPackageApiUser(['view_any_inventory_package']);

    Package::factory()->count(3)->create();

    $this->getJson(inventoryPackageRoute('index'))
        ->assertOk()
        ->assertJsonStructure(['data', 'meta', 'links']);
});

it('filters packages by name', function () {
    actingAsInventoryPackageApiUser(['view_any_inventory_package']);

    $package = Package::factory()->create(['name' => 'UNIQUE-PKG-XYZ']);
    Package::factory()->count(2)->create();

    $response = $this->getJson(inventoryPackageRoute('index').'?filter[name]=UNIQUE-PKG-XYZ')
        ->assertOk();

    $ids = collect($response->json('data'))->pluck('id');

    expect($ids)->toContain($package->id);
});

// ── Store ─────────────────────────────────────────────────────────────────────

it('creates a package', function () {
    actingAsInventoryPackageApiUser(['create_inventory_package']);

    $payload = inventoryPackagePayload();

    $this->postJson(inventoryPackageRoute('store'), $payload)
        ->assertCreated()
        ->assertJsonPath('message', 'Package created successfully.')
        ->assertJsonPath('data.name', $payload['name'])
        ->assertJsonStructure(['data' => INVENTORY_PACKAGE_JSON_STRUCTURE]);

    $this->assertDatabaseHas('inventories_packages', ['name' => $payload['name']]);
});

it('validates that name is required when creating a package', function () {
    actingAsInventoryPackageApiUser(['create_inventory_package']);

    $this->postJson(inventoryPackageRoute('store'), [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['name']);
});

it('creates a package with an optional package type and location', function () {
    actingAsInventoryPackageApiUser(['create_inventory_package']);

    $packageType = PackageType::factory()->withDimensions()->create();
    $location = Location::factory()->create();

    $payload = inventoryPackagePayload([
        'package_type_id' => $packageType->id,
        'location_id'     => $location->id,
        'pack_date'       => now()->toDateString(),
    ]);

    $this->postJson(inventoryPackageRoute('store'), $payload)
        ->assertCreated();

    $this->assertDatabaseHas('inventories_packages', [
        'name'            => $payload['name'],
        'package_type_id' => $packageType->id,
        'location_id'     => $location->id,
    ]);
});

it('rejects an invalid package_type_id', function () {
    actingAsInventoryPackageApiUser(['create_inventory_package']);

    $this->postJson(inventoryPackageRoute('store'), inventoryPackagePayload(['package_type_id' => 999999]))
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['package_type_id']);
});

// ── Show ──────────────────────────────────────────────────────────────────────

it('shows a package for authorized users', function () {
    actingAsInventoryPackageApiUser(['view_inventory_package']);

    $package = Package::factory()->create();

    $this->getJson(inventoryPackageRoute('show', $package))
        ->assertOk()
        ->assertJsonPath('data.id', $package->id)
        ->assertJsonPath('data.name', $package->name)
        ->assertJsonStructure(['data' => INVENTORY_PACKAGE_JSON_STRUCTURE]);
});

it('returns 404 for a non-existent package', function () {
    actingAsInventoryPackageApiUser(['view_inventory_package']);

    $this->getJson(inventoryPackageRoute('show', 999999))
        ->assertNotFound();
});

// ── Update ────────────────────────────────────────────────────────────────────

it('updates a package', function () {
    actingAsInventoryPackageApiUser(['update_inventory_package']);

    $package = Package::factory()->create();

    $this->patchJson(inventoryPackageRoute('update', $package), ['name' => 'PKG-UPDATED'])
        ->assertOk()
        ->assertJsonPath('message', 'Package updated successfully.')
        ->assertJsonPath('data.name', 'PKG-UPDATED');

    $this->assertDatabaseHas('inventories_packages', [
        'id'   => $package->id,
        'name' => 'PKG-UPDATED',
    ]);
});

it('returns 404 when updating a non-existent package', function () {
    actingAsInventoryPackageApiUser(['update_inventory_package']);

    $this->patchJson(inventoryPackageRoute('update', 999999), ['name' => 'X'])
        ->assertNotFound();
});

// ── Destroy ───────────────────────────────────────────────────────────────────

it('deletes a package', function () {
    actingAsInventoryPackageApiUser(['delete_inventory_package']);

    $package = Package::factory()->create();

    $this->deleteJson(inventoryPackageRoute('destroy', $package))
        ->assertOk()
        ->assertJsonPath('message', 'Package deleted successfully.');

    $this->assertDatabaseMissing('inventories_packages', ['id' => $package->id]);
});

it('returns 404 when deleting a non-existent package', function () {
    actingAsInventoryPackageApiUser(['delete_inventory_package']);

    $this->deleteJson(inventoryPackageRoute('destroy', 999999))
        ->assertNotFound();
});
