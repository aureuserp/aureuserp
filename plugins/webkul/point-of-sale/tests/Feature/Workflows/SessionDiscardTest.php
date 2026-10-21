<?php

use Webkul\PointOfSale\Enums\SessionState;
use Webkul\PointOfSale\Exceptions\SessionNotDiscardableException;
use Webkul\PointOfSale\Facades\PointOfSale;
use Webkul\PointOfSale\Models\Session;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../inventories/tests/Helpers/InventoryHelper.php';
require_once __DIR__.'/../../Helpers/PosHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('inventories');
    TestBootstrapHelper::ensurePluginInstalled('accounts');
    TestBootstrapHelper::ensurePluginInstalled('point-of-sale');

    InventoryHelper::actingAsAdmin();

    $this->warehouse = InventoryHelper::warehouse();
    $this->session = PosHelper::openSession($this->warehouse);
    $this->config = $this->session->config;
    $this->cash = $this->config->paymentMethods->first();
    $this->product = InventoryHelper::product(['price' => 25.0, 'cost' => 10.0]);

    InventoryHelper::stockUp($this->product, $this->warehouse->lotStockLocation, 10);
});

it('discards a register that was opened by mistake', function () {
    expect(PointOfSale::isSessionDiscardable($this->session))->toBeTrue();

    PointOfSale::discardSession($this->session);

    expect(Session::find($this->session->getKey()))->toBeNull();
});

it('refuses to discard a session that has taken an order', function () {
    PointOfSale::syncOrder(PosHelper::orderPayload(
        $this->config,
        $this->session,
        [PosHelper::line($this->product->id, 1, 25.0)],
        [PosHelper::payment($this->cash, 25.0)],
    ));

    expect(PointOfSale::isSessionDiscardable($this->session->refresh()))->toBeFalse();

    PointOfSale::discardSession($this->session);
})->throws(SessionNotDiscardableException::class);

it('refuses to discard a session that has a cash movement', function () {
    PointOfSale::cashIn($this->session, 20.0, 'Float top up');

    expect(PointOfSale::isSessionDiscardable($this->session->refresh()))->toBeFalse();

    PointOfSale::discardSession($this->session);
})->throws(SessionNotDiscardableException::class);

it('refuses to discard a session that is already closed', function () {
    $closed = PointOfSale::closeSession($this->session);

    expect($closed->state)->toBe(SessionState::CLOSED);

    expect(PointOfSale::isSessionDiscardable($closed))->toBeFalse();

    PointOfSale::discardSession($closed);
})->throws(SessionNotDiscardableException::class);

it('leaves the order count untouched when a discard is refused', function () {
    PointOfSale::syncOrder(PosHelper::orderPayload(
        $this->config,
        $this->session,
        [PosHelper::line($this->product->id, 2, 25.0)],
        [PosHelper::payment($this->cash, 50.0)],
    ));

    $before = $this->session->orders()->count();

    try {
        PointOfSale::discardSession($this->session);
    } catch (SessionNotDiscardableException) {
        $refused = true;
    }

    expect($refused ?? false)->toBeTrue();

    expect(Session::find($this->session->getKey()))->not->toBeNull();

    expect($this->session->orders()->count())->toBe($before);
});
