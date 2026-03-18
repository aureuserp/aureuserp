<?php

use Webkul\Inventory\Enums\OperationState;
use Webkul\Inventory\Models\Dropship;
use Webkul\Inventory\Models\InternalTransfer;
use Webkul\Inventory\Models\Operation;
use Webkul\Inventory\Models\OperationType;
use Webkul\Inventory\Models\Product;
use Webkul\Security\Enums\PermissionType;
use Webkul\Security\Models\User;

require_once __DIR__.'/../../../../../support/tests/Helpers/SecurityHelper.php';
require_once __DIR__.'/../../../../../support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('inventories');
    SecurityHelper::disableUserEvents();
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

function actingAsInventoryInternalTransferApiUser(array $permissions = []): User
{
    $user = SecurityHelper::authenticateWithPermissions($permissions);

    $user->forceFill([
        'resource_permission' => PermissionType::GLOBAL,
    ])->saveQuietly();

    return $user;
}

function internalTransferRoute(string $action, mixed $internalTransfer = null): string
{
    $name = "admin.api.v1.inventories.internal-transfers.{$action}";

    return $internalTransfer ? route($name, $internalTransfer) : route($name);
}

function internalTransferPayload(array $overrides = []): array
{
    $product = Product::factory()->create();
    $operationType = OperationType::factory()->internal()->create();

    return array_replace_recursive([
        'operation_type_id' => $operationType->id,
        'moves'             => [[
            'product_id'      => $product->id,
            'product_uom_qty' => 1,
            'uom_id'          => $product->uom_id,
        ]],
    ], $overrides);
}

function createInternalTransferRecord(array $overrides = []): InternalTransfer
{
    $operation = Operation::factory()->internal()->create($overrides);

    return InternalTransfer::query()->findOrFail($operation->id);
}

it('requires authentication to list internal transfers', function () {
    $this->getJson(internalTransferRoute('index'))
        ->assertUnauthorized();
});

it('forbids creating an internal transfer without permission', function () {
    actingAsInventoryInternalTransferApiUser();

    $this->postJson(internalTransferRoute('store'), internalTransferPayload())
        ->assertForbidden();
});

it('forbids showing an internal transfer without permission', function () {
    actingAsInventoryInternalTransferApiUser();

    $internalTransfer = createInternalTransferRecord();

    $this->getJson(internalTransferRoute('show', $internalTransfer->id))
        ->assertForbidden();
});

it('forbids listing internal transfers without permission', function () {
    actingAsInventoryInternalTransferApiUser();

    $this->getJson(internalTransferRoute('index'))
        ->assertForbidden();
});

it('lists internal transfers for authorized users', function () {
    actingAsInventoryInternalTransferApiUser(['view_any_inventory_internal']);

    $internalTransfer = createInternalTransferRecord();

    $response = $this->getJson(internalTransferRoute('index'))
        ->assertOk();

    expect(collect($response->json('data'))->pluck('id'))->toContain($internalTransfer->id);
});

it('creates an internal transfer with valid payload', function () {
    actingAsInventoryInternalTransferApiUser(['create_inventory_internal']);

    $response = $this->postJson(internalTransferRoute('store'), internalTransferPayload())
        ->assertCreated()
        ->assertJsonPath('message', 'Internal transfer created successfully.');

    expect(InternalTransfer::query()->whereKey($response->json('data.id'))->exists())->toBeTrue();
});

it('shows an internal transfer for authorized users', function () {
    actingAsInventoryInternalTransferApiUser(['view_inventory_internal']);

    $internalTransfer = createInternalTransferRecord();

    $this->getJson(internalTransferRoute('show', $internalTransfer->id))
        ->assertOk()
        ->assertJsonPath('data.id', $internalTransfer->id);
});

it('returns 404 for a non-existent internal transfer', function () {
    actingAsInventoryInternalTransferApiUser(['view_inventory_internal']);

    $this->getJson(internalTransferRoute('show', 999999))
        ->assertNotFound();
});

it('validates required move product_id when creating an internal transfer', function () {
    actingAsInventoryInternalTransferApiUser(['create_inventory_internal']);

    $payload = internalTransferPayload([
        'moves' => [[
            'product_id'      => null,
            'product_uom_qty' => 1,
        ]],
    ]);

    $this->postJson(internalTransferRoute('store'), $payload)
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['moves.0.product_id']);
});

it('returns 404 when showing a non-internal operation id', function () {
    actingAsInventoryInternalTransferApiUser(['view_inventory_internal']);

    $dropship = Dropship::query()->findOrFail(Operation::factory()->dropship()->create()->id);

    $this->getJson(internalTransferRoute('show', $dropship->id))
        ->assertNotFound();
});

it('rejects check availability when internal transfer is not confirmed or assigned', function () {
    actingAsInventoryInternalTransferApiUser(['update_inventory_internal']);

    $internalTransfer = createInternalTransferRecord();

    $this->postJson(internalTransferRoute('check-availability', $internalTransfer->id))
        ->assertUnprocessable()
        ->assertJsonPath('message', 'Only confirmed or assigned operations can check availability.');
});

it('forbids check availability without update permission', function () {
    actingAsInventoryInternalTransferApiUser();

    $internalTransfer = createInternalTransferRecord();

    $this->postJson(internalTransferRoute('check-availability', $internalTransfer->id))
        ->assertForbidden();
});

it('returns todo validation error when internal transfer has no moves', function () {
    actingAsInventoryInternalTransferApiUser(['update_inventory_internal']);

    $internalTransfer = createInternalTransferRecord();

    $this->postJson(internalTransferRoute('todo', $internalTransfer->id))
        ->assertUnprocessable()
        ->assertJsonPath('message', 'Cannot set operation to todo without moves.');
});

it('returns validate validation error for done internal transfer', function () {
    actingAsInventoryInternalTransferApiUser(['update_inventory_internal']);

    $internalTransfer = createInternalTransferRecord(['state' => OperationState::DONE]);

    $this->postJson(internalTransferRoute('validate', $internalTransfer->id))
        ->assertUnprocessable()
        ->assertJsonPath('message', 'Only non-done and non-canceled operations can be validated.');
});

it('returns cancel validation error for canceled internal transfer', function () {
    actingAsInventoryInternalTransferApiUser(['update_inventory_internal']);

    $internalTransfer = createInternalTransferRecord(['state' => OperationState::CANCELED]);

    $this->postJson(internalTransferRoute('cancel', $internalTransfer->id))
        ->assertUnprocessable()
        ->assertJsonPath('message', 'Only non-done and non-canceled operations can be canceled.');
});

it('returns return validation error when internal transfer is not done', function () {
    actingAsInventoryInternalTransferApiUser(['update_inventory_internal']);

    $internalTransfer = createInternalTransferRecord(['state' => OperationState::DRAFT]);

    $this->postJson(internalTransferRoute('return', $internalTransfer->id))
        ->assertUnprocessable()
        ->assertJsonPath('message', 'Only done operations can be returned.');
});
