<?php

use Webkul\Inventory\Enums\OperationState;
use Webkul\Inventory\Models\Delivery;
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

function actingAsInventoryDeliveryApiUser(array $permissions = []): User
{
    $user = SecurityHelper::authenticateWithPermissions($permissions);

    $user->forceFill([
        'resource_permission' => PermissionType::GLOBAL,
    ])->saveQuietly();

    return $user;
}

function deliveryRoute(string $action, mixed $delivery = null): string
{
    $name = "admin.api.v1.inventories.deliveries.{$action}";

    return $delivery ? route($name, $delivery) : route($name);
}

function deliveryPayload(array $overrides = []): array
{
    $product = Product::factory()->create();
    $operationType = OperationType::factory()->delivery()->create();

    return array_replace_recursive([
        'operation_type_id' => $operationType->id,
        'moves'             => [[
            'product_id'      => $product->id,
            'product_uom_qty' => 1,
            'uom_id'          => $product->uom_id,
        ]],
    ], $overrides);
}

function createDeliveryRecord(array $overrides = []): Delivery
{
    $operation = Operation::factory()->delivery()->create($overrides);

    return Delivery::query()->findOrFail($operation->id);
}

it('requires authentication to list deliveries', function () {
    $this->getJson(deliveryRoute('index'))
        ->assertUnauthorized();
});

it('forbids creating a delivery without permission', function () {
    actingAsInventoryDeliveryApiUser();

    $this->postJson(deliveryRoute('store'), deliveryPayload())
        ->assertForbidden();
});

it('forbids showing a delivery without permission', function () {
    actingAsInventoryDeliveryApiUser();

    $delivery = createDeliveryRecord();

    $this->getJson(deliveryRoute('show', $delivery->id))
        ->assertForbidden();
});

it('forbids listing deliveries without permission', function () {
    actingAsInventoryDeliveryApiUser();

    $this->getJson(deliveryRoute('index'))
        ->assertForbidden();
});

it('lists deliveries for authorized users', function () {
    actingAsInventoryDeliveryApiUser(['view_any_inventory_delivery']);

    $delivery = createDeliveryRecord();

    $response = $this->getJson(deliveryRoute('index'))
        ->assertOk();

    expect(collect($response->json('data'))->pluck('id'))->toContain($delivery->id);
});

it('creates a delivery with valid payload', function () {
    actingAsInventoryDeliveryApiUser(['create_inventory_delivery']);

    $response = $this->postJson(deliveryRoute('store'), deliveryPayload())
        ->assertCreated()
        ->assertJsonPath('message', 'Delivery created successfully.');

    expect(Delivery::query()->whereKey($response->json('data.id'))->exists())->toBeTrue();
});

it('shows a delivery for authorized users', function () {
    actingAsInventoryDeliveryApiUser(['view_inventory_delivery']);

    $delivery = createDeliveryRecord();

    $this->getJson(deliveryRoute('show', $delivery->id))
        ->assertOk()
        ->assertJsonPath('data.id', $delivery->id);
});

it('returns 404 for a non-existent delivery', function () {
    actingAsInventoryDeliveryApiUser(['view_inventory_delivery']);

    $this->getJson(deliveryRoute('show', 999999))
        ->assertNotFound();
});

it('validates required move product_id when creating a delivery', function () {
    actingAsInventoryDeliveryApiUser(['create_inventory_delivery']);

    $payload = deliveryPayload([
        'moves' => [[
            'product_id'      => null,
            'product_uom_qty' => 1,
        ]],
    ]);

    $this->postJson(deliveryRoute('store'), $payload)
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['moves.0.product_id']);
});

it('returns 404 when showing a non-delivery operation id', function () {
    actingAsInventoryDeliveryApiUser(['view_inventory_delivery']);

    $internalTransfer = InternalTransfer::query()->findOrFail(Operation::factory()->internal()->create()->id);

    $this->getJson(deliveryRoute('show', $internalTransfer->id))
        ->assertNotFound();
});

it('rejects check availability when delivery is not confirmed or assigned', function () {
    actingAsInventoryDeliveryApiUser(['update_inventory_delivery']);

    $delivery = createDeliveryRecord();

    $this->postJson(deliveryRoute('check-availability', $delivery->id))
        ->assertUnprocessable()
        ->assertJsonPath('message', 'Only confirmed or assigned operations can check availability.');
});

it('forbids check availability without update permission', function () {
    actingAsInventoryDeliveryApiUser();

    $delivery = createDeliveryRecord();

    $this->postJson(deliveryRoute('check-availability', $delivery->id))
        ->assertForbidden();
});

it('returns todo validation error when delivery has no moves', function () {
    actingAsInventoryDeliveryApiUser(['update_inventory_delivery']);

    $delivery = createDeliveryRecord();

    $this->postJson(deliveryRoute('todo', $delivery->id))
        ->assertUnprocessable()
        ->assertJsonPath('message', 'Cannot set operation to todo without moves.');
});

it('returns validate validation error for done delivery', function () {
    actingAsInventoryDeliveryApiUser(['update_inventory_delivery']);

    $delivery = createDeliveryRecord(['state' => OperationState::DONE]);

    $this->postJson(deliveryRoute('validate', $delivery->id))
        ->assertUnprocessable()
        ->assertJsonPath('message', 'Only non-done and non-canceled operations can be validated.');
});

it('returns cancel validation error for canceled delivery', function () {
    actingAsInventoryDeliveryApiUser(['update_inventory_delivery']);

    $delivery = createDeliveryRecord(['state' => OperationState::CANCELED]);

    $this->postJson(deliveryRoute('cancel', $delivery->id))
        ->assertUnprocessable()
        ->assertJsonPath('message', 'Only non-done and non-canceled operations can be canceled.');
});

it('returns return validation error when delivery is not done', function () {
    actingAsInventoryDeliveryApiUser(['update_inventory_delivery']);

    $delivery = createDeliveryRecord(['state' => OperationState::DRAFT]);

    $this->postJson(deliveryRoute('return', $delivery->id))
        ->assertUnprocessable()
        ->assertJsonPath('message', 'Only done operations can be returned.');
});
