<?php

use Webkul\PointOfSale\Enums\OrderState;
use Webkul\PointOfSale\Facades\PointOfSale;

require_once __DIR__.'/../../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../../inventories/tests/Helpers/InventoryHelper.php';
require_once __DIR__.'/../../../Helpers/PosHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('inventories');
    TestBootstrapHelper::ensurePluginInstalled('accounts');
    TestBootstrapHelper::ensurePluginInstalled('point-of-sale');

    $this->user = InventoryHelper::actingAsAdmin();

    $this->warehouse = InventoryHelper::warehouse();
    $this->session = PosHelper::openSession($this->warehouse);
    $this->config = $this->session->config;
    $this->cash = $this->config->paymentMethods->first();
    $this->product = InventoryHelper::product(['price' => 50.0]);

    InventoryHelper::stockUp($this->product, $this->warehouse->lotStockLocation, 30);

    $this->actingAs($this->user, 'sanctum');
});

it('builds the closing control report over the api', function () {
    $this->getJson(route('admin.api.v1.point-of-sale.sessions.closing-control.report', ['session' => $this->session->id]))
        ->assertOk()
        ->assertJsonStructure(['data' => ['orders_details', 'non_cash_payment_methods', 'amount_authorized_diff']]);
});

it('cancels the draft orders of a session over the api', function () {
    $draft = PointOfSale::saveDraftOrder(PosHelper::orderPayload(
        $this->config,
        $this->session,
        [PosHelper::line($this->product->id, 1, 50.0)],
    ));

    $this->postJson(route('admin.api.v1.point-of-sale.sessions.cancel-draft-orders', ['session' => $this->session->id]))
        ->assertOk()
        ->assertJsonPath('data.canceled', 1);

    expect($draft->refresh()->state)->toBe(OrderState::CANCELED);
});

it('streams the sales details pdf over the api', function () {
    $this->get(route('admin.api.v1.point-of-sale.sessions.sales-details', ['session' => $this->session->id]))
        ->assertStatus(200);
});
