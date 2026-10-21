<?php

use Webkul\Partner\Models\Partner;
use Webkul\PointOfSale\Facades\PointOfSale;

require_once __DIR__.'/../../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../../inventories/tests/Helpers/InventoryHelper.php';
require_once __DIR__.'/../../../Helpers/PosHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('inventories');
    TestBootstrapHelper::ensurePluginInstalled('accounts');
    TestBootstrapHelper::ensurePluginInstalled('point-of-sale');

    PosHelper::ensureRoutesRegistered();

    $this->user = InventoryHelper::actingAsAdmin();

    $this->warehouse = InventoryHelper::warehouse();
    $this->session = PosHelper::openSession($this->warehouse);
    $this->config = $this->session->config;
    $this->cash = $this->config->paymentMethods->first();
    $this->product = InventoryHelper::product(['price' => 50.0]);
    $this->partner = Partner::factory()->create(['company_id' => PosHelper::company()->id]);

    InventoryHelper::stockUp($this->product, $this->warehouse->lotStockLocation, 40);

    $this->order = PointOfSale::syncOrder(PosHelper::orderPayload(
        $this->config->refresh(),
        $this->session,
        [PosHelper::line($this->product->id, 4, 50.0)],
        [PosHelper::payment($this->cash, 200.0)],
        ['partner_id' => $this->partner->id],
    ));

    $this->actingAs($this->user, 'sanctum');
});

it('reports the refundable lines of an order', function () {
    $line = $this->order->lines()->first();

    $this->getJson(route('admin.api.v1.point-of-sale.orders.refundable', ['order' => $this->order->id]))
        ->assertOk()
        ->assertJsonPath("data.{$line->id}", fn ($quantity): bool => (float) $quantity === 4.0);
});

it('prepares a pending refund over the api', function () {
    $line = $this->order->lines()->first();

    $this->postJson(route('admin.api.v1.point-of-sale.orders.refund', ['order' => $this->order->id]), [
        'quantities' => [$line->id => 2],
    ])
        ->assertOk()
        ->assertJsonPath('data.refunded_order_id', $this->order->id)
        ->assertJsonPath('data.lines.0.qty', fn ($qty): bool => (float) $qty === -2.0)
        ->assertJsonPath('data.lines.0.refunded_order_line_id', $line->id);
});

it('rejects a refund above the sold quantity', function () {
    $line = $this->order->lines()->first();

    $this->postJson(route('admin.api.v1.point-of-sale.orders.refund', ['order' => $this->order->id]), [
        'quantities' => [$line->id => 9],
    ])->assertStatus(422);
});

it('rejects a refund with no quantities', function () {
    $this->postJson(route('admin.api.v1.point-of-sale.orders.refund', ['order' => $this->order->id]), [
        'quantities' => [],
    ])->assertStatus(422);
});

it('invoices a settled order over the api', function () {
    $this->postJson(route('admin.api.v1.point-of-sale.orders.invoice', ['order' => $this->order->id]))
        ->assertOk()
        ->assertJsonPath('data.is_invoiced', true);

    expect($this->order->refresh()->is_invoiced)->toBeTrue();
});

it('streams the invoice pdf for an invoiced order', function () {
    PointOfSale::invoiceOrder($this->order);

    expect($this->order->refresh()->account_move_id)->not->toBeNull();

    $this->get(route('admin.api.v1.point-of-sale.orders.invoice-pdf', ['order' => $this->order->id]))
        ->assertOk()
        ->assertHeader('content-type', 'application/pdf');
});

it('rejects the invoice pdf for an order that has not been invoiced', function () {
    expect($this->order->refresh()->account_move_id)->toBeNull();

    $this->get(route('admin.api.v1.point-of-sale.orders.invoice-pdf', ['order' => $this->order->id]))
        ->assertNotFound();
});

it('returns a receipt for an order', function () {
    $this->getJson(route('admin.api.v1.point-of-sale.orders.receipt', ['order' => $this->order->id]))
        ->assertOk()
        ->assertJsonStructure(['data']);
});
