<?php

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

    $this->actingAs($this->user, 'sanctum');
});

it('lists active registers with live session and discardable flags', function () {
    $this->getJson(route('admin.api.v1.point-of-sale.registers'))
        ->assertOk()
        ->assertJsonPath('data.0.id', $this->config->id)
        ->assertJsonPath('data.0.live_session.id', $this->session->id)
        ->assertJsonStructure(['data' => [['id', 'name', 'is_active', 'live_session', 'is_discardable']], 'own_session_id']);
});

it('omits inactive registers', function () {
    $this->config->update(['is_active' => false]);

    $this->getJson(route('admin.api.v1.point-of-sale.registers'))
        ->assertOk()
        ->assertJsonCount(0, 'data');
});

it('reports the own single open session for auto-resume', function () {
    $this->getJson(route('admin.api.v1.point-of-sale.registers'))
        ->assertOk()
        ->assertJsonPath('own_session_id', $this->session->id);
});

it('discards a discardable session', function () {
    $this->postJson(route('admin.api.v1.point-of-sale.registers.discard', ['config' => $this->config->id]))
        ->assertOk();

    expect(PointOfSale::liveSessionFor($this->config->refresh()))->toBeNull();
});
