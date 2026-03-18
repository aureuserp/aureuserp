<?php

use Webkul\Inventory\Enums\CreateBackorder;
use Webkul\Inventory\Enums\OperationType as OperationTypeEnum;
use Webkul\Inventory\Models\Location;
use Webkul\Inventory\Models\OperationType;
use Webkul\Security\Enums\PermissionType;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

require_once __DIR__.'/../../../../../support/tests/Helpers/SecurityHelper.php';
require_once __DIR__.'/../../../../../support/tests/Helpers/TestBootstrapHelper.php';

const INVENTORY_OPERATION_TYPE_JSON_STRUCTURE = [
    'id',
    'name',
    'type',
];

const INVENTORY_OPERATION_TYPE_REQUIRED_FIELDS = [
    'name',
    'type',
    'sequence_code',
    'create_backorder',
    'source_location_id',
    'destination_location_id',
];

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('inventories');
    SecurityHelper::disableUserEvents();
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

function actingAsInventoryOperationTypeApiUser(array $permissions = []): User
{
    $user = SecurityHelper::authenticateWithPermissions($permissions);

    $user->forceFill([
        'resource_permission' => PermissionType::GLOBAL,
    ])->saveQuietly();

    return $user;
}

function inventoryOperationTypeRoute(string $action, mixed $operationType = null): string
{
    $name = "admin.api.v1.inventories.operation-types.{$action}";

    return $operationType ? route($name, $operationType) : route($name);
}

function inventoryOperationTypePayload(array $overrides = []): array
{
    $source = Location::factory()->create();
    $destination = Location::factory()->create();
    $company = Company::factory()->create();

    return array_replace_recursive([
        'name'                    => 'Test Operation',
        'type'                    => OperationTypeEnum::INTERNAL->value,
        'sequence_code'           => 'INT-'.uniqid(),
        'create_backorder'        => CreateBackorder::ASK->value,
        'source_location_id'      => $source->id,
        'destination_location_id' => $destination->id,
        'company_id'              => $company->id,
    ], $overrides);
}

// ── Authentication ────────────────────────────────────────────────────────────

it('requires authentication to list operation types', function () {
    $this->getJson(inventoryOperationTypeRoute('index'))
        ->assertUnauthorized();
});

it('requires authentication to create an operation type', function () {
    $this->postJson(inventoryOperationTypeRoute('store'), [])
        ->assertUnauthorized();
});

it('requires authentication to show an operation type', function () {
    $operationType = OperationType::factory()->create();

    $this->getJson(inventoryOperationTypeRoute('show', $operationType))
        ->assertUnauthorized();
});

it('requires authentication to update an operation type', function () {
    $operationType = OperationType::factory()->create();

    $this->patchJson(inventoryOperationTypeRoute('update', $operationType), [])
        ->assertUnauthorized();
});

it('requires authentication to delete an operation type', function () {
    $operationType = OperationType::factory()->create();

    $this->deleteJson(inventoryOperationTypeRoute('destroy', $operationType))
        ->assertUnauthorized();
});

it('requires authentication to restore an operation type', function () {
    $operationType = OperationType::factory()->create();
    $operationType->delete();

    $this->postJson(inventoryOperationTypeRoute('restore', $operationType))
        ->assertUnauthorized();
});

it('requires authentication to force-delete an operation type', function () {
    $operationType = OperationType::factory()->create();
    $operationType->delete();

    $this->deleteJson(inventoryOperationTypeRoute('force-destroy', $operationType))
        ->assertUnauthorized();
});

// ── Authorization ─────────────────────────────────────────────────────────────

it('forbids listing operation types without permission', function () {
    actingAsInventoryOperationTypeApiUser();

    $this->getJson(inventoryOperationTypeRoute('index'))
        ->assertForbidden();
});

it('forbids creating an operation type without permission', function () {
    actingAsInventoryOperationTypeApiUser();

    $this->postJson(inventoryOperationTypeRoute('store'), inventoryOperationTypePayload())
        ->assertForbidden();
});

it('forbids showing an operation type without permission', function () {
    actingAsInventoryOperationTypeApiUser();

    $operationType = OperationType::factory()->create();

    $this->getJson(inventoryOperationTypeRoute('show', $operationType))
        ->assertForbidden();
});

it('forbids updating an operation type without permission', function () {
    actingAsInventoryOperationTypeApiUser();

    $operationType = OperationType::factory()->create();

    $this->patchJson(inventoryOperationTypeRoute('update', $operationType), [])
        ->assertForbidden();
});

it('forbids deleting an operation type without permission', function () {
    actingAsInventoryOperationTypeApiUser();

    $operationType = OperationType::factory()->create();

    $this->deleteJson(inventoryOperationTypeRoute('destroy', $operationType))
        ->assertForbidden();
});

it('forbids restoring an operation type without permission', function () {
    actingAsInventoryOperationTypeApiUser();

    $operationType = OperationType::factory()->create();
    $operationType->delete();

    $this->postJson(inventoryOperationTypeRoute('restore', $operationType))
        ->assertForbidden();
});

it('forbids force-deleting an operation type without permission', function () {
    actingAsInventoryOperationTypeApiUser();

    $operationType = OperationType::factory()->create();
    $operationType->delete();

    $this->deleteJson(inventoryOperationTypeRoute('force-destroy', $operationType))
        ->assertForbidden();
});

// ── Index ─────────────────────────────────────────────────────────────────────

it('lists operation types for authorized users', function () {
    actingAsInventoryOperationTypeApiUser(['view_any_inventory_operation::type']);

    OperationType::factory()->count(3)->create();

    $this->getJson(inventoryOperationTypeRoute('index'))
        ->assertOk()
        ->assertJsonStructure(['data', 'meta', 'links']);
});

it('filters operation types by name', function () {
    actingAsInventoryOperationTypeApiUser(['view_any_inventory_operation::type']);

    $operationType = OperationType::factory()->create(['name' => 'UniqueOperationXYZ']);
    OperationType::factory()->count(2)->create();

    $response = $this->getJson(inventoryOperationTypeRoute('index').'?filter[name]=UniqueOperationXYZ')
        ->assertOk();

    $ids = collect($response->json('data'))->pluck('id');

    expect($ids)->toContain($operationType->id);
});

it('filters operation types by type', function () {
    actingAsInventoryOperationTypeApiUser(['view_any_inventory_operation::type']);

    $receipt = OperationType::factory()->receipt()->create();

    $response = $this->getJson(inventoryOperationTypeRoute('index').'?filter[type]=incoming')
        ->assertOk();

    $ids = collect($response->json('data'))->pluck('id');

    expect($ids)->toContain($receipt->id);
});

it('excludes soft-deleted operation types from default listing', function () {
    actingAsInventoryOperationTypeApiUser(['view_any_inventory_operation::type']);

    $active = OperationType::factory()->create();
    $deleted = OperationType::factory()->create();
    $deleted->delete();

    $response = $this->getJson(inventoryOperationTypeRoute('index'))
        ->assertOk();

    $ids = collect($response->json('data'))->pluck('id');

    expect($ids)->toContain($active->id)
        ->and($ids)->not->toContain($deleted->id);
});

// ── Store ─────────────────────────────────────────────────────────────────────

it('creates an operation type', function () {
    actingAsInventoryOperationTypeApiUser(['create_inventory_operation::type']);

    $payload = inventoryOperationTypePayload();

    $this->postJson(inventoryOperationTypeRoute('store'), $payload)
        ->assertCreated()
        ->assertJsonPath('message', 'Operation type created successfully.')
        ->assertJsonPath('data.name', $payload['name'])
        ->assertJsonStructure(['data' => INVENTORY_OPERATION_TYPE_JSON_STRUCTURE]);

    $this->assertDatabaseHas('inventories_operation_types', [
        'name' => $payload['name'],
    ]);
});

it('validates required fields when creating an operation type', function (string $field) {
    actingAsInventoryOperationTypeApiUser(['create_inventory_operation::type']);

    $payload = inventoryOperationTypePayload();
    unset($payload[$field]);

    $this->postJson(inventoryOperationTypeRoute('store'), $payload)
        ->assertUnprocessable()
        ->assertJsonValidationErrors([$field]);
})->with(INVENTORY_OPERATION_TYPE_REQUIRED_FIELDS);

it('rejects an invalid operation type enum', function () {
    actingAsInventoryOperationTypeApiUser(['create_inventory_operation::type']);

    $this->postJson(inventoryOperationTypeRoute('store'), inventoryOperationTypePayload(['type' => 'invalid_type']))
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['type']);
});

// ── Show ──────────────────────────────────────────────────────────────────────

it('shows an operation type for authorized users', function () {
    actingAsInventoryOperationTypeApiUser(['view_inventory_operation::type']);

    $operationType = OperationType::factory()->create();

    $this->getJson(inventoryOperationTypeRoute('show', $operationType))
        ->assertOk()
        ->assertJsonPath('data.id', $operationType->id)
        ->assertJsonPath('data.name', $operationType->name)
        ->assertJsonStructure(['data' => INVENTORY_OPERATION_TYPE_JSON_STRUCTURE]);
});

it('returns 404 for a non-existent operation type', function () {
    actingAsInventoryOperationTypeApiUser(['view_inventory_operation::type']);

    $this->getJson(inventoryOperationTypeRoute('show', 999999))
        ->assertNotFound();
});

// ── Update ────────────────────────────────────────────────────────────────────

it('updates an operation type', function () {
    actingAsInventoryOperationTypeApiUser(['update_inventory_operation::type']);

    $operationType = OperationType::factory()->create();

    $this->patchJson(inventoryOperationTypeRoute('update', $operationType), ['name' => 'Updated Operation'])
        ->assertOk()
        ->assertJsonPath('message', 'Operation type updated successfully.')
        ->assertJsonPath('data.name', 'Updated Operation');

    $this->assertDatabaseHas('inventories_operation_types', [
        'id'   => $operationType->id,
        'name' => 'Updated Operation',
    ]);
});

it('returns 404 when updating a non-existent operation type', function () {
    actingAsInventoryOperationTypeApiUser(['update_inventory_operation::type']);

    $this->patchJson(inventoryOperationTypeRoute('update', 999999), ['name' => 'X'])
        ->assertNotFound();
});

// ── Destroy ───────────────────────────────────────────────────────────────────

it('soft deletes an operation type', function () {
    actingAsInventoryOperationTypeApiUser(['delete_inventory_operation::type']);

    $operationType = OperationType::factory()->create();

    $this->deleteJson(inventoryOperationTypeRoute('destroy', $operationType))
        ->assertOk()
        ->assertJsonPath('message', 'Operation type deleted successfully.');

    $this->assertSoftDeleted('inventories_operation_types', ['id' => $operationType->id]);
});

it('returns 404 when deleting a non-existent operation type', function () {
    actingAsInventoryOperationTypeApiUser(['delete_inventory_operation::type']);

    $this->deleteJson(inventoryOperationTypeRoute('destroy', 999999))
        ->assertNotFound();
});

// ── Restore ───────────────────────────────────────────────────────────────────

it('restores a soft-deleted operation type', function () {
    actingAsInventoryOperationTypeApiUser(['restore_inventory_operation::type']);

    $operationType = OperationType::factory()->create();
    $operationType->delete();

    $this->postJson(inventoryOperationTypeRoute('restore', $operationType))
        ->assertOk()
        ->assertJsonPath('message', 'Operation type restored successfully.');

    $this->assertDatabaseHas('inventories_operation_types', [
        'id'         => $operationType->id,
        'deleted_at' => null,
    ]);
});

it('returns 404 when restoring a non-existent operation type', function () {
    actingAsInventoryOperationTypeApiUser(['restore_inventory_operation::type']);

    $this->postJson(inventoryOperationTypeRoute('restore', 999999))
        ->assertNotFound();
});

// ── Force Delete ──────────────────────────────────────────────────────────────

it('permanently deletes an operation type', function () {
    actingAsInventoryOperationTypeApiUser(['force_delete_inventory_operation::type']);

    $operationType = OperationType::factory()->create();
    $operationType->delete();

    $this->deleteJson(inventoryOperationTypeRoute('force-destroy', $operationType))
        ->assertOk()
        ->assertJsonPath('message', 'Operation type permanently deleted successfully.');

    $this->assertDatabaseMissing('inventories_operation_types', ['id' => $operationType->id]);
});

it('returns 404 when force-deleting a non-existent operation type', function () {
    actingAsInventoryOperationTypeApiUser(['force_delete_inventory_operation::type']);

    $this->deleteJson(inventoryOperationTypeRoute('force-destroy', 999999))
        ->assertNotFound();
});
