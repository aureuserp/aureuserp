<?php

use Webkul\Inventory\Models\PackageType;
use Webkul\Security\Enums\PermissionType;
use Webkul\Security\Models\User;

require_once __DIR__.'/../../../../../support/tests/Helpers/SecurityHelper.php';
require_once __DIR__.'/../../../../../support/tests/Helpers/TestBootstrapHelper.php';

const INVENTORY_PACKAGE_TYPE_JSON_STRUCTURE = [
    'id',
    'name',
];

const INVENTORY_PACKAGE_TYPE_REQUIRED_FIELDS = [
    'name',
    'length',
    'width',
    'height',
    'base_weight',
    'max_weight',
];

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('inventories');
    SecurityHelper::disableUserEvents();
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

function actingAsInventoryPackageTypeApiUser(array $permissions = []): User
{
    $user = SecurityHelper::authenticateWithPermissions($permissions);

    $user->forceFill([
        'resource_permission' => PermissionType::GLOBAL,
    ])->saveQuietly();

    return $user;
}

function inventoryPackageTypeRoute(string $action, mixed $packageType = null): string
{
    $name = "admin.api.v1.inventories.package-types.{$action}";

    return $packageType ? route($name, $packageType) : route($name);
}

function inventoryPackageTypePayload(array $overrides = []): array
{
    return array_replace_recursive([
        'name'        => 'Box L',
        'length'      => 40.0,
        'width'       => 30.0,
        'height'      => 20.0,
        'base_weight' => 1.5,
        'max_weight'  => 25.0,
    ], $overrides);
}

// ── Authentication ────────────────────────────────────────────────────────────

it('requires authentication to list package types', function () {
    $this->getJson(inventoryPackageTypeRoute('index'))
        ->assertUnauthorized();
});

it('requires authentication to create a package type', function () {
    $this->postJson(inventoryPackageTypeRoute('store'), [])
        ->assertUnauthorized();
});

it('requires authentication to show a package type', function () {
    $packageType = PackageType::factory()->withDimensions()->create();

    $this->getJson(inventoryPackageTypeRoute('show', $packageType))
        ->assertUnauthorized();
});

it('requires authentication to update a package type', function () {
    $packageType = PackageType::factory()->withDimensions()->create();

    $this->patchJson(inventoryPackageTypeRoute('update', $packageType), [])
        ->assertUnauthorized();
});

it('requires authentication to delete a package type', function () {
    $packageType = PackageType::factory()->withDimensions()->create();

    $this->deleteJson(inventoryPackageTypeRoute('destroy', $packageType))
        ->assertUnauthorized();
});

// ── Authorization ─────────────────────────────────────────────────────────────

it('forbids listing package types without permission', function () {
    actingAsInventoryPackageTypeApiUser();

    $this->getJson(inventoryPackageTypeRoute('index'))
        ->assertForbidden();
});

it('forbids creating a package type without permission', function () {
    actingAsInventoryPackageTypeApiUser();

    $this->postJson(inventoryPackageTypeRoute('store'), inventoryPackageTypePayload())
        ->assertForbidden();
});

it('forbids showing a package type without permission', function () {
    actingAsInventoryPackageTypeApiUser();

    $packageType = PackageType::factory()->withDimensions()->create();

    $this->getJson(inventoryPackageTypeRoute('show', $packageType))
        ->assertForbidden();
});

it('forbids updating a package type without permission', function () {
    actingAsInventoryPackageTypeApiUser();

    $packageType = PackageType::factory()->withDimensions()->create();

    $this->patchJson(inventoryPackageTypeRoute('update', $packageType), [])
        ->assertForbidden();
});

it('forbids deleting a package type without permission', function () {
    actingAsInventoryPackageTypeApiUser();

    $packageType = PackageType::factory()->withDimensions()->create();

    $this->deleteJson(inventoryPackageTypeRoute('destroy', $packageType))
        ->assertForbidden();
});

// ── Index ─────────────────────────────────────────────────────────────────────

it('lists package types for authorized users', function () {
    actingAsInventoryPackageTypeApiUser(['view_any_inventory_package::type']);

    PackageType::factory()->withDimensions()->count(3)->create();

    $this->getJson(inventoryPackageTypeRoute('index'))
        ->assertOk()
        ->assertJsonStructure(['data', 'meta', 'links']);
});

it('filters package types by name', function () {
    actingAsInventoryPackageTypeApiUser(['view_any_inventory_package::type']);

    $packageType = PackageType::factory()->withDimensions()->create(['name' => 'UniqueBoxXYZ']);
    PackageType::factory()->withDimensions()->count(2)->create();

    $response = $this->getJson(inventoryPackageTypeRoute('index').'?filter[name]=UniqueBoxXYZ')
        ->assertOk();

    $ids = collect($response->json('data'))->pluck('id');

    expect($ids)->toContain($packageType->id);
});

// ── Store ─────────────────────────────────────────────────────────────────────

it('creates a package type', function () {
    actingAsInventoryPackageTypeApiUser(['create_inventory_package::type']);

    $payload = inventoryPackageTypePayload();

    $this->postJson(inventoryPackageTypeRoute('store'), $payload)
        ->assertCreated()
        ->assertJsonPath('message', 'Package type created successfully.')
        ->assertJsonPath('data.name', $payload['name'])
        ->assertJsonStructure(['data' => INVENTORY_PACKAGE_TYPE_JSON_STRUCTURE]);

    $this->assertDatabaseHas('inventories_package_types', ['name' => $payload['name']]);
});

it('validates required fields when creating a package type', function (string $field) {
    actingAsInventoryPackageTypeApiUser(['create_inventory_package::type']);

    $payload = inventoryPackageTypePayload();
    unset($payload[$field]);

    $this->postJson(inventoryPackageTypeRoute('store'), $payload)
        ->assertUnprocessable()
        ->assertJsonValidationErrors([$field]);
})->with(INVENTORY_PACKAGE_TYPE_REQUIRED_FIELDS);

it('rejects negative dimension values', function () {
    actingAsInventoryPackageTypeApiUser(['create_inventory_package::type']);

    $this->postJson(inventoryPackageTypeRoute('store'), inventoryPackageTypePayload(['length' => -1]))
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['length']);
});

// ── Show ──────────────────────────────────────────────────────────────────────

it('shows a package type for authorized users', function () {
    actingAsInventoryPackageTypeApiUser(['view_inventory_package::type']);

    $packageType = PackageType::factory()->withDimensions()->create();

    $this->getJson(inventoryPackageTypeRoute('show', $packageType))
        ->assertOk()
        ->assertJsonPath('data.id', $packageType->id)
        ->assertJsonPath('data.name', $packageType->name)
        ->assertJsonStructure(['data' => INVENTORY_PACKAGE_TYPE_JSON_STRUCTURE]);
});

it('returns 404 for a non-existent package type', function () {
    actingAsInventoryPackageTypeApiUser(['view_inventory_package::type']);

    $this->getJson(inventoryPackageTypeRoute('show', 999999))
        ->assertNotFound();
});

// ── Update ────────────────────────────────────────────────────────────────────

it('updates a package type', function () {
    actingAsInventoryPackageTypeApiUser(['update_inventory_package::type']);

    $packageType = PackageType::factory()->withDimensions()->create();

    $this->patchJson(inventoryPackageTypeRoute('update', $packageType), ['name' => 'Updated Box'])
        ->assertOk()
        ->assertJsonPath('message', 'Package type updated successfully.')
        ->assertJsonPath('data.name', 'Updated Box');

    $this->assertDatabaseHas('inventories_package_types', [
        'id'   => $packageType->id,
        'name' => 'Updated Box',
    ]);
});

it('returns 404 when updating a non-existent package type', function () {
    actingAsInventoryPackageTypeApiUser(['update_inventory_package::type']);

    $this->patchJson(inventoryPackageTypeRoute('update', 999999), ['name' => 'X'])
        ->assertNotFound();
});

// ── Destroy ───────────────────────────────────────────────────────────────────

it('deletes a package type', function () {
    actingAsInventoryPackageTypeApiUser(['delete_inventory_package::type']);

    $packageType = PackageType::factory()->withDimensions()->create();

    $this->deleteJson(inventoryPackageTypeRoute('destroy', $packageType))
        ->assertOk()
        ->assertJsonPath('message', 'Package type deleted successfully.');

    $this->assertDatabaseMissing('inventories_package_types', ['id' => $packageType->id]);
});

it('returns 404 when deleting a non-existent package type', function () {
    actingAsInventoryPackageTypeApiUser(['delete_inventory_package::type']);

    $this->deleteJson(inventoryPackageTypeRoute('destroy', 999999))
        ->assertNotFound();
});
