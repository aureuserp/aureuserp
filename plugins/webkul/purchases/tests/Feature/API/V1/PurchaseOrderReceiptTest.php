<?php

use Webkul\Inventory\Models\Operation;
use Webkul\PluginManager\Package;
use Webkul\Purchase\Models\Order;
use Webkul\Security\Enums\PermissionType;
use Webkul\Security\Models\User;

require_once __DIR__.'/../../../../../support/tests/Helpers/SecurityHelper.php';
require_once __DIR__.'/../../../../../support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('purchases');
    SecurityHelper::disableUserEvents();
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

function actingAsPurchaseOrderReceiptApiUser(array $permissions = []): User
{
    $user = SecurityHelper::authenticateWithPermissions($permissions);

    $user->forceFill([
        'resource_permission' => PermissionType::GLOBAL,
    ])->saveQuietly();

    return $user;
}

function purchaseOrderReceiptRoute(mixed $order): string
{
    return route('admin.api.v1.purchases.purchase-orders.receipts.index', ['purchase_order' => $order]);
}

it('requires authentication to list purchase order receipts', function () {
    $order = Order::factory()->create();

    $this->getJson(purchaseOrderReceiptRoute($order->id))
        ->assertUnauthorized();
});

it('forbids listing purchase order receipts without permission', function () {
    actingAsPurchaseOrderReceiptApiUser();

    $order = Order::factory()->create();

    $this->getJson(purchaseOrderReceiptRoute($order->id))
        ->assertForbidden();
});

it('lists purchase order receipts for authorized users (empty when inventories not installed)', function () {
    actingAsPurchaseOrderReceiptApiUser(['view_purchase_purchase::order']);

    $order = Order::factory()->create();
    $otherOrder = Order::factory()->create();

    if (Package::isPluginInstalled('inventories')) {
        $operation = Operation::factory()->create(['company_id' => $order->company_id]);
        $order->operations()->attach($operation->id);

        $otherOrderOperation = Operation::factory()->create(['company_id' => $otherOrder->company_id]);
        $otherOrder->operations()->attach($otherOrderOperation->id);
    }

    $response = $this->getJson(purchaseOrderReceiptRoute($order->id))
        ->assertOk()
        ->assertJsonStructure(['data']);

    if (! Package::isPluginInstalled('inventories')) {
        $response->assertJsonCount(0, 'data');

        return;
    }

    $returnedIds = collect($response->json('data'))->pluck('id');
    $otherOrderOperationIds = $otherOrder->operations()->pluck('id');

    expect($returnedIds->intersect($otherOrderOperationIds))->toHaveCount(0);
});

it('returns 404 for a non-existent purchase order when listing receipts', function () {
    actingAsPurchaseOrderReceiptApiUser(['view_purchase_purchase::order']);

    $this->getJson(purchaseOrderReceiptRoute(999999))
        ->assertNotFound();
});

it('returns an empty list for purchase orders without receipts', function () {
    actingAsPurchaseOrderReceiptApiUser(['view_purchase_purchase::order']);

    $order = Order::factory()->create();

    $this->getJson(purchaseOrderReceiptRoute($order->id))
        ->assertOk()
        ->assertJsonCount(0, 'data');
});

it('filters purchase order receipts by state when inventories plugin is installed', function () {
    actingAsPurchaseOrderReceiptApiUser(['view_purchase_purchase::order']);

    $order = Order::factory()->create();

    if (! Package::isPluginInstalled('inventories')) {
        $this->getJson(purchaseOrderReceiptRoute($order->id))
            ->assertOk()
            ->assertJsonCount(0, 'data');

        return;
    }

    $confirmedOperation = Operation::factory()->confirmed()->create(['company_id' => $order->company_id]);
    $doneOperation = Operation::factory()->done()->create(['company_id' => $order->company_id]);

    $order->operations()->attach([$confirmedOperation->id, $doneOperation->id]);

    $response = $this->getJson(purchaseOrderReceiptRoute($order->id).'?filter[state]=confirmed')
        ->assertOk();

    $states = collect($response->json('data'))->pluck('state')->unique()->values()->all();

    expect($states)->toBe(['confirmed']);
});

it('includes operation type relationship for purchase order receipts', function () {
    actingAsPurchaseOrderReceiptApiUser(['view_purchase_purchase::order']);

    $order = Order::factory()->create();

    if (! Package::isPluginInstalled('inventories')) {
        $this->getJson(purchaseOrderReceiptRoute($order->id).'?include=operationType')
            ->assertOk()
            ->assertJsonCount(0, 'data');

        return;
    }

    $operation = Operation::factory()->create(['company_id' => $order->company_id]);
    $order->operations()->attach($operation->id);

    $this->getJson(purchaseOrderReceiptRoute($order->id).'?include=operationType')
        ->assertOk()
        ->assertJsonPath('data.0.operation_type.id', fn ($id) => is_int($id));
});
