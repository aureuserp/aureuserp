<?php

use Illuminate\Support\Facades\Schema;
use Webkul\Inventory\Models\Operation;
use Webkul\Sale\Models\Order;

require_once __DIR__.'/../../../../../support/tests/Helpers/SecurityHelper.php';
require_once __DIR__.'/../../../../../support/tests/Helpers/TestBootstrapHelper.php';

uses(Illuminate\Foundation\Testing\LazilyRefreshDatabase::class);

const SALES_ORDER_DELIVERY_JSON_STRUCTURE = [
    'id',
    'sale_order_id',
];

beforeEach(function () {
    if (! Schema::hasTable('inventories_operations') || ! Schema::hasTable('sales_orders')) {
        $this->artisan('sales:install', ['--no-interaction' => true])->assertSuccessful();
    }

    TestBootstrapHelper::ensureBaseCurrencies();
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

    Operation::factory()->count($deliveryCount)->create([
        'sale_order_id' => $order->id,
        'company_id'    => $order->company_id,
        'partner_id'    => $order->partner_id,
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
    $order = createOrderWithDeliveries(2);

    actingAsSalesOrderDeliveryApiUser(['view_sale_order']);

    $this->getJson(salesOrderDeliveryRoute('index', $order->id))
        ->assertOk()
        ->assertJsonCount(2, 'data')
        ->assertJsonStructure(['data' => ['*' => SALES_ORDER_DELIVERY_JSON_STRUCTURE]]);
});

it('returns 404 for a non-existent order when listing deliveries', function () {
    actingAsSalesOrderDeliveryApiUser(['view_sale_order']);

    $this->getJson(salesOrderDeliveryRoute('index', 999999))
        ->assertNotFound();
});
