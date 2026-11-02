<?php

use Webkul\Inventory\Enums\OperationState;
use Webkul\Inventory\Facades\Inventory;
use Webkul\Inventory\Models\Delivery;
use Webkul\Inventory\Models\Operation;
use Webkul\Inventory\Models\OperationType;
use Webkul\Inventory\Models\Product;
use Webkul\Inventory\Models\Receipt;
use Webkul\Security\Enums\PermissionType;
use Webkul\Security\Models\User;

require_once __DIR__.'/../../../../../support/tests/Helpers/SecurityHelper.php';
require_once __DIR__.'/../../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../Helpers/InventoryHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('inventories');
    SecurityHelper::disableUserEvents();
});

afterEach(function () {
    SecurityHelper::restoreUserEvents();

    InventoryHelper::returnSettings();
});

function actingAsInventoryReceiptApiUser(array $permissions = []): User
{
    $user = SecurityHelper::authenticateWithPermissions($permissions);

    $user->forceFill([
        'resource_permission' => PermissionType::GLOBAL,
    ])->saveQuietly();

    return $user;
}

function receiptRoute(string $action, mixed $receipt = null): string
{
    $name = "admin.api.v1.inventories.receipts.{$action}";

    return $receipt ? route($name, $receipt) : route($name);
}

function receiptPayload(array $overrides = []): array
{
    $product = Product::factory()->create();
    $operationType = OperationType::factory()->receipt()->create();

    return array_replace_recursive([
        'operation_type_id' => $operationType->id,
        'moves'             => [[
            'product_id'      => $product->id,
            'product_uom_qty' => 1,
            'uom_id'          => $product->uom_id,
        ]],
    ], $overrides);
}

function createReceiptRecord(array $overrides = []): Receipt
{
    $operation = Operation::factory()->receipt()->create($overrides);

    return Receipt::query()->findOrFail($operation->id);
}

it('requires authentication to list receipts', function () {
    $this->getJson(receiptRoute('index'))
        ->assertUnauthorized();
});

it('requires authentication to create a receipt', function () {
    $this->postJson(receiptRoute('store'), [])
        ->assertUnauthorized();
});

it('requires authentication to show a receipt', function () {
    $receipt = createReceiptRecord();

    $this->getJson(receiptRoute('show', $receipt->id))
        ->assertUnauthorized();
});

it('forbids creating a receipt without permission', function () {
    actingAsInventoryReceiptApiUser();

    $this->postJson(receiptRoute('store'), receiptPayload())
        ->assertForbidden();
});

it('forbids showing a receipt without permission', function () {
    actingAsInventoryReceiptApiUser();

    $receipt = createReceiptRecord();

    $this->getJson(receiptRoute('show', $receipt->id))
        ->assertForbidden();
});

it('forbids listing receipts without permission', function () {
    actingAsInventoryReceiptApiUser();

    $this->getJson(receiptRoute('index'))
        ->assertForbidden();
});

it('lists receipts for authorized users', function () {
    actingAsInventoryReceiptApiUser(['view_any_inventory_receipt']);

    $receipt = createReceiptRecord();

    $response = $this->getJson(receiptRoute('index'))
        ->assertOk();

    expect(collect($response->json('data'))->pluck('id'))->toContain($receipt->id);
});

it('creates a receipt with valid payload', function () {
    actingAsInventoryReceiptApiUser(['create_inventory_receipt']);

    $response = $this->postJson(receiptRoute('store'), receiptPayload())
        ->assertCreated()
        ->assertJsonPath('message', 'Receipt created successfully.');

    expect(Receipt::query()->whereKey($response->json('data.id'))->exists())->toBeTrue();
});

it('shows a receipt for authorized users', function () {
    actingAsInventoryReceiptApiUser(['view_inventory_receipt']);

    $receipt = createReceiptRecord();

    $this->getJson(receiptRoute('show', $receipt->id))
        ->assertOk()
        ->assertJsonPath('data.id', $receipt->id);
});

it('returns 404 for a non-existent receipt', function () {
    actingAsInventoryReceiptApiUser(['view_inventory_receipt']);

    $this->getJson(receiptRoute('show', 999999))
        ->assertNotFound();
});

it('validates required move product_id when creating a receipt', function () {
    actingAsInventoryReceiptApiUser(['create_inventory_receipt']);

    $payload = receiptPayload([
        'moves' => [[
            'product_id'      => null,
            'product_uom_qty' => 1,
        ]],
    ]);

    $this->postJson(receiptRoute('store'), $payload)
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['moves.0.product_id']);
});

it('returns 404 when showing a non-receipt operation id', function () {
    actingAsInventoryReceiptApiUser(['view_inventory_receipt']);

    $delivery = Delivery::query()->findOrFail(Operation::factory()->delivery()->create()->id);

    $this->getJson(receiptRoute('show', $delivery->id))
        ->assertNotFound();
});

it('rejects check availability when receipt is not confirmed or assigned', function () {
    actingAsInventoryReceiptApiUser(['update_inventory_receipt']);

    $receipt = createReceiptRecord();

    $this->postJson(receiptRoute('check-availability', $receipt->id))
        ->assertUnprocessable()
        ->assertJsonPath('message', 'Only waiting, confirmed or assigned operations can check availability.');
});

it('forbids check availability without update permission', function () {
    actingAsInventoryReceiptApiUser();

    $receipt = createReceiptRecord();

    $this->postJson(receiptRoute('check-availability', $receipt->id))
        ->assertForbidden();
});

it('returns todo validation error when receipt has no moves', function () {
    actingAsInventoryReceiptApiUser(['update_inventory_receipt']);

    $receipt = createReceiptRecord();

    $this->postJson(receiptRoute('todo', $receipt->id))
        ->assertUnprocessable()
        ->assertJsonPath('message', 'Cannot set operation to todo without moves.');
});

it('returns validate validation error for done receipt', function () {
    actingAsInventoryReceiptApiUser(['update_inventory_receipt']);

    $receipt = createReceiptRecord(['state' => OperationState::DONE]);

    $this->postJson(receiptRoute('validate', $receipt->id))
        ->assertUnprocessable()
        ->assertJsonPath('message', 'Only non-done and non-canceled operations can be validated.');
});

it('returns cancel validation error for canceled receipt', function () {
    actingAsInventoryReceiptApiUser(['update_inventory_receipt']);

    $receipt = createReceiptRecord(['state' => OperationState::CANCELED]);

    $this->postJson(receiptRoute('cancel', $receipt->id))
        ->assertUnprocessable()
        ->assertJsonPath('message', 'Only non-done and non-canceled operations can be canceled.');
});

it('returns return validation error when receipt is not done', function () {
    actingAsInventoryReceiptApiUser(['update_inventory_receipt']);

    $receipt = createReceiptRecord(['state' => OperationState::DRAFT]);

    $this->postJson(receiptRoute('return', $receipt->id))
        ->assertUnprocessable()
        ->assertJsonPath('message', 'Only done operations can be returned.');
});

function doneApiReceipt(float $quantity): Operation
{
    $receipt = InventoryHelper::receipt(InventoryHelper::warehouse(), [[InventoryHelper::product(), $quantity]]);

    Inventory::confirmTransfer($receipt);

    Inventory::completeTransfer($receipt->refresh());

    return $receipt->refresh();
}

it('returns the remaining quantity of a receipt through the api', function () {
    actingAsInventoryReceiptApiUser(['update_inventory_receipt']);

    $receipt = doneApiReceipt(10);

    Inventory::createReturn($receipt, [$receipt->moves->first()->id => 3]);

    $this->postJson(receiptRoute('return', $receipt->id))
        ->assertSuccessful()
        ->assertJsonPath('message', 'Receipt return created successfully.');

    $returns = Operation::query()->where('return_id', $receipt->id)->orderBy('id')->get();

    expect($returns)->toHaveCount(2)
        ->and((float) $returns->last()->moves->first()->product_uom_qty)->toBe(7.0);
});

it('returns a validation error when the receipt has nothing left to return', function () {
    actingAsInventoryReceiptApiUser(['update_inventory_receipt']);

    $receipt = doneApiReceipt(10);

    Inventory::createReturn($receipt, [$receipt->moves->first()->id => 10]);

    $this->postJson(receiptRoute('return', $receipt->id))
        ->assertUnprocessable()
        ->assertJsonPath('message', __('inventories::system.inventory-manager.return.nothing-to-return'));
});

it('returns a validation error when returning a return through the api', function () {
    actingAsInventoryReceiptApiUser(['update_inventory_receipt']);

    $receipt = doneApiReceipt(10);

    $return = Inventory::createReturn($receipt, [$receipt->moves->first()->id => 4]);

    Inventory::completeTransfer($return->refresh());

    $this->postJson(receiptRoute('return', $return->id))
        ->assertUnprocessable()
        ->assertJsonPath('message', __('inventories::system.inventory-manager.return.return-of-return', ['operation' => $return->refresh()->name]));

    expect(Operation::query()->where('return_id', $return->id)->exists())->toBeFalse();
});
