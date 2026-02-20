<?php

use Illuminate\Support\Facades\Schema;
use Webkul\Sale\Models\Order;
use Webkul\Sale\Models\OrderLine;

require_once __DIR__.'/../../../../../support/tests/Helpers/SecurityHelper.php';
require_once __DIR__.'/../../../../../support/tests/Helpers/TestBootstrapHelper.php';

uses(Illuminate\Foundation\Testing\LazilyRefreshDatabase::class);

const SALES_ORDER_LINE_JSON_STRUCTURE = [
    'id',
    'order_id',
    'product_id',
    'product_qty',
    'price_unit',
    'product_uom_id',
];

beforeEach(function () {
    if (! Schema::hasTable('sales_order_lines')) {
        $this->artisan('sales:install', ['--no-interaction' => true])->assertSuccessful();
    }

    TestBootstrapHelper::ensureBaseCurrencies();
    SecurityHelper::disableUserEvents();
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

function actingAsSalesOrderLineApiUser(array $permissions = []): void
{
    SecurityHelper::authenticateWithPermissions($permissions);
}

function salesOrderLineRoute(string $action, mixed $order, mixed $line = null): string
{
    $name = "admin.api.v1.sales.orders.lines.{$action}";

    $parameters = ['order' => $order];

    if ($line !== null) {
        $parameters['line'] = $line;
    }

    return route($name, $parameters);
}

function createSalesOrderWithLines(int $lineCount = 2): Order
{
    $order = Order::factory()->create();

    OrderLine::factory()->count($lineCount)->create([
        'order_id'         => $order->id,
        'company_id'       => $order->company_id,
        'currency_id'      => $order->currency_id,
        'order_partner_id' => $order->partner_id,
        'salesman_id'      => $order->user_id,
        'state'            => $order->state,
    ]);

    return $order->refresh();
}

it('requires authentication to list order lines', function () {
    $order = createSalesOrderWithLines();

    $this->getJson(salesOrderLineRoute('index', $order->id))
        ->assertUnauthorized();
});

it('forbids listing order lines without permission', function () {
    $order = createSalesOrderWithLines();

    actingAsSalesOrderLineApiUser();

    $this->getJson(salesOrderLineRoute('index', $order->id))
        ->assertForbidden();
});

it('lists order lines for authorized users', function () {
    $order = createSalesOrderWithLines(2);

    actingAsSalesOrderLineApiUser(['view_sale_order']);

    $this->getJson(salesOrderLineRoute('index', $order->id))
        ->assertOk()
        ->assertJsonCount(2, 'data')
        ->assertJsonStructure(['data' => ['*' => SALES_ORDER_LINE_JSON_STRUCTURE]]);
});

it('shows an order line for authorized users', function () {
    $order = createSalesOrderWithLines(1);
    $line = $order->lines()->firstOrFail();

    actingAsSalesOrderLineApiUser(['view_sale_order']);

    $this->getJson(salesOrderLineRoute('show', $order->id, $line->id))
        ->assertOk()
        ->assertJsonPath('data.id', $line->id)
        ->assertJsonPath('data.order_id', $order->id)
        ->assertJsonStructure(['data' => SALES_ORDER_LINE_JSON_STRUCTURE]);
});

it('returns 404 for a non-existent order when listing lines', function () {
    actingAsSalesOrderLineApiUser(['view_sale_order']);

    $this->getJson(salesOrderLineRoute('index', 999999))
        ->assertNotFound();
});

it('returns not found when line does not belong to the order', function () {
    $firstOrder = createSalesOrderWithLines(1);
    $secondOrder = createSalesOrderWithLines(1);
    $foreignLine = $secondOrder->lines()->firstOrFail();

    actingAsSalesOrderLineApiUser(['view_sale_order']);

    $this->getJson(salesOrderLineRoute('show', $firstOrder->id, $foreignLine->id))
        ->assertNotFound();
});
