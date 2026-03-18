<?php

use Webkul\Inventory\Models\Operation;
use Webkul\PluginManager\Package;
use Webkul\Sale\Models\Order;

require_once __DIR__.'/../../../../../support/tests/Helpers/SecurityHelper.php';
require_once __DIR__.'/../../../../../support/tests/Helpers/TestBootstrapHelper.php';

const SALES_ORDER_DELIVERY_JSON_STRUCTURE = [
    'id',
    'sale_order_id',
];

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('sales');
    SecurityHelper::disableUserEvents();
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

function actingAsSalesOrderDeliveryApiUser(array $permissions = []): void
{
    SecurityHelper::authenticateWithPermissions($permissions);
}

function salesOrderDeliveryRoute(string $action, mixed $order): string
{
    return route("admin.api.v1.sales.orders.deliveries.{$action}", ['order' => $order]);
}

function createOrderWithDeliveries(int $deliveryCount = 2): Order
{
    $order = Order::factory()->create();

    if (! Package::isPluginInstalled('inventories')) {
        return $order;
    }

    Operation::factory()->count($deliveryCount)->create([
        'sale_order_id' => $order->id,
        'company_id'    => $order->company_id,
    ]);

    return $order->refresh();
}

it('requires authentication to list order deliveries', function () {
    $order = createOrderWithDeliveries();

    $this->getJson(salesOrderDeliveryRoute('index', $order->id))
        ->assertUnauthorized();
});

it('forbids listing order deliveries without permission', function () {
    $order = createOrderWithDeliveries();

    actingAsSalesOrderDeliveryApiUser();

    $this->getJson(salesOrderDeliveryRoute('index', $order->id))
        ->assertForbidden();
});

it('lists order deliveries for authorized users', function () {
    $order = createOrderWithDeliveries();

    actingAsSalesOrderDeliveryApiUser(['view_sale_order']);

    $response = $this->getJson(salesOrderDeliveryRoute('index', $order->id))
        ->assertOk()
        ->assertJsonStructure(['data']);

    if (! Package::isPluginInstalled('inventories')) {
        $response->assertJsonCount(0, 'data');
    }
});

it('returns 404 for a non-existent order when listing deliveries', function () {
    actingAsSalesOrderDeliveryApiUser(['view_sale_order']);

    $this->getJson(salesOrderDeliveryRoute('index', 999999))
        ->assertNotFound();
});

it('does not return deliveries from other orders', function () {
    actingAsSalesOrderDeliveryApiUser(['view_sale_order']);

    $order = createOrderWithDeliveries(1);
    $otherOrder = createOrderWithDeliveries(2);

    $response = $this->getJson(salesOrderDeliveryRoute('index', $order->id))
        ->assertOk();

    if (! Package::isPluginInstalled('inventories')) {
        $response->assertJsonCount(0, 'data');

        return;
    }

    $otherOrderDeliveryIds = $otherOrder->operations()->pluck('id');
    $returnedIds = collect($response->json('data'))->pluck('id');

    expect($returnedIds->intersect($otherOrderDeliveryIds))->toHaveCount(0);
});

it('filters deliveries by state when inventories plugin is installed', function () {
    actingAsSalesOrderDeliveryApiUser(['view_sale_order']);

    $order = createOrderWithDeliveries(0);

    if (! Package::isPluginInstalled('inventories')) {
        $this->getJson(salesOrderDeliveryRoute('index', $order->id))
            ->assertOk()
            ->assertJsonCount(0, 'data');

        return;
    }

    Operation::factory()->confirmed()->create([
        'sale_order_id' => $order->id,
        'company_id'    => $order->company_id,
    ]);
    Operation::factory()->done()->create([
        'sale_order_id' => $order->id,
        'company_id'    => $order->company_id,
    ]);

    $response = $this->getJson(salesOrderDeliveryRoute('index', $order->id).'?filter[state]=confirmed')
        ->assertOk();

    $states = collect($response->json('data'))->pluck('state')->unique()->values()->all();

    expect($states)->toBe(['confirmed']);
});

it('includes operation type for deliveries when requested', function () {
    actingAsSalesOrderDeliveryApiUser(['view_sale_order']);

    $order = createOrderWithDeliveries(1);

    $response = $this->getJson(salesOrderDeliveryRoute('index', $order->id).'?include=operationType')
        ->assertOk();

    if (! Package::isPluginInstalled('inventories')) {
        $response->assertJsonCount(0, 'data');

        return;
    }

    $response->assertJsonPath('data.0.operation_type.id', fn ($id) => is_int($id));
});
