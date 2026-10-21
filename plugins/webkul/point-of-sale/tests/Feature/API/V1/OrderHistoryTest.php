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

it('lists the orders of a session with lines and payments', function () {
    $order = PointOfSale::syncOrder(PosHelper::orderPayload(
        $this->config,
        $this->session,
        [PosHelper::line($this->product->id, 1, 50.0)],
        [PosHelper::payment($this->cash, 50.0)],
    ));

    $this->getJson(route('admin.api.v1.point-of-sale.sessions.orders', ['session' => $this->session->id]))
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $order->id)
        ->assertJsonPath('data.0.state', OrderState::PAID->value)
        ->assertJsonStructure(['data' => [['id', 'uuid', 'state', 'lines', 'payments']]]);
});

it('hides canceled orders by default and lists them on request', function () {
    $draft = PointOfSale::saveDraftOrder(PosHelper::orderPayload(
        $this->config,
        $this->session,
        [PosHelper::line($this->product->id, 1, 50.0)],
    ));

    PointOfSale::cancelOrder($draft);

    $this->getJson(route('admin.api.v1.point-of-sale.sessions.orders', ['session' => $this->session->id]))
        ->assertOk()
        ->assertJsonCount(0, 'data');

    $this->getJson(route('admin.api.v1.point-of-sale.sessions.orders', [
        'session' => $this->session->id,
        'status'  => OrderState::CANCELED->value,
    ]))
        ->assertOk()
        ->assertJsonCount(1, 'data');
});

it('searches orders by name', function () {
    $order = PointOfSale::syncOrder(PosHelper::orderPayload(
        $this->config,
        $this->session,
        [PosHelper::line($this->product->id, 1, 50.0)],
        [PosHelper::payment($this->cash, 50.0)],
    ));

    $this->getJson(route('admin.api.v1.point-of-sale.sessions.orders', [
        'session' => $this->session->id,
        'search'  => $order->refresh()->name,
    ]))
        ->assertOk()
        ->assertJsonCount(1, 'data');

    $this->getJson(route('admin.api.v1.point-of-sale.sessions.orders', [
        'session' => $this->session->id,
        'search'  => 'no-such-order',
    ]))
        ->assertOk()
        ->assertJsonCount(0, 'data');
});
