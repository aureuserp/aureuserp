<?php

use Webkul\Inventory\Enums\OperationState;
use Webkul\Inventory\Models\Dropship;
use Webkul\Inventory\Models\Operation;
use Webkul\Inventory\Models\OperationType;
use Webkul\Inventory\Models\Product;
use Webkul\Inventory\Models\Receipt;
use Webkul\Security\Enums\PermissionType;
use Webkul\Security\Models\User;

require_once __DIR__.'/../../../../../support/tests/Helpers/SecurityHelper.php';
require_once __DIR__.'/../../../../../support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('inventories');
    SecurityHelper::disableUserEvents();
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

function actingAsInventoryDropshipApiUser(array $permissions = []): User
{
    $user = SecurityHelper::authenticateWithPermissions($permissions);

    $user->forceFill([
        'resource_permission' => PermissionType::GLOBAL,
    ])->saveQuietly();

    return $user;
}

function dropshipRoute(string $action, mixed $dropship = null): string
{
    $name = "admin.api.v1.inventories.dropships.{$action}";

    return $dropship ? route($name, $dropship) : route($name);
}

function dropshipPayload(array $overrides = []): array
{
    $product = Product::factory()->create();
    $operationType = OperationType::factory()->dropship()->create();

    return array_replace_recursive([
        'operation_type_id' => $operationType->id,
        'moves'             => [[
            'product_id'      => $product->id,
            'product_uom_qty' => 1,
            'uom_id'          => $product->uom_id,
        ]],
    ], $overrides);
}

function createDropshipRecord(array $overrides = []): Dropship
{
    $operation = Operation::factory()->dropship()->create($overrides);

    return Dropship::query()->findOrFail($operation->id);
}

it('requires authentication to list dropships', function () {
    $this->getJson(dropshipRoute('index'))
        ->assertUnauthorized();
});

it('forbids creating a dropship without permission', function () {
    actingAsInventoryDropshipApiUser();

    $this->postJson(dropshipRoute('store'), dropshipPayload())
        ->assertForbidden();
});

it('forbids showing a dropship without permission', function () {
    actingAsInventoryDropshipApiUser();

    $dropship = createDropshipRecord();

    $this->getJson(dropshipRoute('show', $dropship->id))
        ->assertForbidden();
});

it('forbids listing dropships without permission', function () {
    actingAsInventoryDropshipApiUser();

    $this->getJson(dropshipRoute('index'))
        ->assertForbidden();
});

it('lists dropships for authorized users', function () {
    actingAsInventoryDropshipApiUser(['view_any_inventory_dropship']);

    $dropship = createDropshipRecord();

    $response = $this->getJson(dropshipRoute('index'))
        ->assertOk();

    expect(collect($response->json('data'))->pluck('id'))->toContain($dropship->id);
});

it('creates a dropship with valid payload', function () {
    actingAsInventoryDropshipApiUser(['create_inventory_dropship']);

    $response = $this->postJson(dropshipRoute('store'), dropshipPayload())
        ->assertCreated()
        ->assertJsonPath('message', 'Dropship created successfully.');

    expect(Dropship::query()->whereKey($response->json('data.id'))->exists())->toBeTrue();
});

it('shows a dropship for authorized users', function () {
    actingAsInventoryDropshipApiUser(['view_inventory_dropship']);

    $dropship = createDropshipRecord();

    $this->getJson(dropshipRoute('show', $dropship->id))
        ->assertOk()
        ->assertJsonPath('data.id', $dropship->id);
});

it('returns 404 for a non-existent dropship', function () {
    actingAsInventoryDropshipApiUser(['view_inventory_dropship']);

    $this->getJson(dropshipRoute('show', 999999))
        ->assertNotFound();
});

it('validates required move product_id when creating a dropship', function () {
    actingAsInventoryDropshipApiUser(['create_inventory_dropship']);

    $payload = dropshipPayload([
        'moves' => [[
            'product_id'      => null,
            'product_uom_qty' => 1,
        ]],
    ]);

    $this->postJson(dropshipRoute('store'), $payload)
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['moves.0.product_id']);
});

it('returns 404 when showing a non-dropship operation id', function () {
    actingAsInventoryDropshipApiUser(['view_inventory_dropship']);

    $receipt = Receipt::query()->findOrFail(Operation::factory()->receipt()->create()->id);

    $this->getJson(dropshipRoute('show', $receipt->id))
        ->assertNotFound();
});

it('rejects check availability when dropship is not confirmed or assigned', function () {
    actingAsInventoryDropshipApiUser(['update_inventory_dropship']);

    $dropship = createDropshipRecord();

    $this->postJson(dropshipRoute('check-availability', $dropship->id))
        ->assertUnprocessable()
        ->assertJsonPath('message', 'Only confirmed or assigned operations can check availability.');
});

it('forbids check availability without update permission', function () {
    actingAsInventoryDropshipApiUser();

    $dropship = createDropshipRecord();

    $this->postJson(dropshipRoute('check-availability', $dropship->id))
        ->assertForbidden();
});

it('returns todo validation error when dropship has no moves', function () {
    actingAsInventoryDropshipApiUser(['update_inventory_dropship']);

    $dropship = createDropshipRecord();

    $this->postJson(dropshipRoute('todo', $dropship->id))
        ->assertUnprocessable()
        ->assertJsonPath('message', 'Cannot set operation to todo without moves.');
});

it('returns validate validation error for done dropship', function () {
    actingAsInventoryDropshipApiUser(['update_inventory_dropship']);

    $dropship = createDropshipRecord(['state' => OperationState::DONE]);

    $this->postJson(dropshipRoute('validate', $dropship->id))
        ->assertUnprocessable()
        ->assertJsonPath('message', 'Only non-done and non-canceled operations can be validated.');
});

it('returns cancel validation error for canceled dropship', function () {
    actingAsInventoryDropshipApiUser(['update_inventory_dropship']);

    $dropship = createDropshipRecord(['state' => OperationState::CANCELED]);

    $this->postJson(dropshipRoute('cancel', $dropship->id))
        ->assertUnprocessable()
        ->assertJsonPath('message', 'Only non-done and non-canceled operations can be canceled.');
});

it('returns return validation error when dropship is not done', function () {
    actingAsInventoryDropshipApiUser(['update_inventory_dropship']);

    $dropship = createDropshipRecord(['state' => OperationState::DRAFT]);

    $this->postJson(dropshipRoute('return', $dropship->id))
        ->assertUnprocessable()
        ->assertJsonPath('message', 'Only done operations can be returned.');
});
