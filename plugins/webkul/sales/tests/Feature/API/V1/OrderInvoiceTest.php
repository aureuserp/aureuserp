<?php

use Webkul\Account\Enums\MoveType;
use Webkul\Account\Models\Move;
use Webkul\Sale\Models\Order;

require_once __DIR__.'/../../../../../support/tests/Helpers/SecurityHelper.php';
require_once __DIR__.'/../../../../../support/tests/Helpers/TestBootstrapHelper.php';

uses(Illuminate\Foundation\Testing\LazilyRefreshDatabase::class);

const SALES_ORDER_INVOICE_JSON_STRUCTURE = [
    'id',
    'move_type',
    'company_id',
];

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('sales');
    SecurityHelper::disableUserEvents();
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

function actingAsSalesOrderInvoiceApiUser(array $permissions = []): void
{
    SecurityHelper::authenticateWithPermissions($permissions);
}

function salesOrderInvoiceRoute(string $action, mixed $order): string
{
    return route("admin.api.v1.sales.orders.invoices.{$action}", ['order' => $order]);
}

function createOrderWithInvoices(): array
{
    $order = Order::factory()->create();

    $customerInvoice = Move::factory()->invoice()->create([
        'company_id' => $order->company_id,
    ]);

    $vendorBill = Move::factory()->vendorBill()->create([
        'company_id' => $order->company_id,
    ]);

    $order->accountMoves()->attach([$customerInvoice->id, $vendorBill->id]);

    return [$order, $customerInvoice, $vendorBill];
}

it('requires authentication to list order invoices', function () {
    [$order] = createOrderWithInvoices();

    $this->getJson(salesOrderInvoiceRoute('index', $order->id))
        ->assertUnauthorized();
});

it('forbids listing order invoices without permission', function () {
    [$order] = createOrderWithInvoices();

    actingAsSalesOrderInvoiceApiUser();

    $this->getJson(salesOrderInvoiceRoute('index', $order->id))
        ->assertForbidden();
});

it('lists only customer invoices for the order when authorized', function () {
    [$order, $customerInvoice] = createOrderWithInvoices();

    actingAsSalesOrderInvoiceApiUser(['view_sale_order']);

    $response = $this->getJson(salesOrderInvoiceRoute('index', $order->id));

    $response
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $customerInvoice->id)
        ->assertJsonPath('data.0.move_type', MoveType::OUT_INVOICE->value)
        ->assertJsonStructure(['data' => ['*' => SALES_ORDER_INVOICE_JSON_STRUCTURE]]);
});

it('returns 404 for a non-existent order when listing invoices', function () {
    actingAsSalesOrderInvoiceApiUser(['view_sale_order']);

    $this->getJson(salesOrderInvoiceRoute('index', 999999))
        ->assertNotFound();
});
