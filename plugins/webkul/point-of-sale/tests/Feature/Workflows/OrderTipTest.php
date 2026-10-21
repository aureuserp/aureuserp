<?php

use Webkul\PointOfSale\Exceptions\InsufficientPaymentException;
use Webkul\PointOfSale\Facades\PointOfSale;
use Webkul\PointOfSale\Models\Order;

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
    $this->product = InventoryHelper::product();
    $this->tipProduct = InventoryHelper::product(['name' => 'Tip', 'price' => 0.0]);

    InventoryHelper::stockUp($this->product, $this->warehouse->lotStockLocation, 50);

    $this->config->forceFill([
        'enable_tip'     => true,
        'tip_product_id' => $this->tipProduct->id,
    ])->save();
});

it('adds the tip as a line on the tip product', function () {
    $order = Order::create([
        'config_id'  => $this->config->id,
        'session_id' => $this->session->id,
    ]);

    PointOfSale::addOrderTip($order, 5.0);

    $line = $order->refresh()->lines->firstWhere('product_id', $this->tipProduct->id);

    expect($line)->not->toBeNull()
        ->and((float) $line->qty)->toBe(1.0)
        ->and((float) $line->price_unit)->toBe(5.0)
        ->and($line->taxes)->toHaveCount(0);
});

it('stamps the tip amount and flags the order as tipped', function () {
    $order = Order::create([
        'config_id'  => $this->config->id,
        'session_id' => $this->session->id,
    ]);

    $order = PointOfSale::addOrderTip($order, 5.0);

    expect((bool) $order->is_tipped)->toBeTrue()
        ->and((float) $order->tip_amount)->toBe(5.0);
});

it('adds the tip to the order total', function () {
    $order = PointOfSale::syncOrder(PosHelper::orderPayload(
        $this->config,
        $this->session,
        [PosHelper::line($this->product->id, 1, 20.0)],
        [PosHelper::payment($this->cash, 20.0)],
    ));

    $tipped = PointOfSale::addOrderTip($order->refresh(), 3.0);

    expect((float) $tipped->amount_total)->toBe(23.0)
        ->and((float) $tipped->tip_amount)->toBe(3.0);
});

it('replaces an earlier tip on the same order', function () {
    $order = Order::create([
        'config_id'  => $this->config->id,
        'session_id' => $this->session->id,
    ]);

    PointOfSale::addOrderTip($order, 2.0);

    $order = PointOfSale::addOrderTip($order->refresh(), 3.0);

    $lines = $order->lines()->where('product_id', $this->tipProduct->id)->get();

    expect((float) $order->tip_amount)->toBe(3.0)
        ->and($lines)->toHaveCount(1)
        ->and((float) $lines->first()->price_unit)->toBe(3.0);
});

it('refuses a tip when the terminal has no tip product', function () {
    $this->config->forceFill([
        'enable_tip'     => false,
        'tip_product_id' => null,
    ])->save();

    $order = Order::create([
        'config_id'  => $this->config->refresh()->id,
        'session_id' => $this->session->id,
    ]);

    expect(fn () => PointOfSale::addOrderTip($order, 5.0))
        ->toThrow(InsufficientPaymentException::class);
});

it('records the tip from a synced tip line', function () {
    $order = PointOfSale::syncOrder(PosHelper::orderPayload(
        $this->config->refresh(),
        $this->session,
        [
            PosHelper::line($this->product->id, 1, 20.0),
            PosHelper::line($this->tipProduct->id, 1, 3.0),
        ],
        [PosHelper::payment($this->cash, 23.0)],
    ));

    expect($order->refresh()->is_tipped)->toBeTrue()
        ->and((float) $order->tip_amount)->toBe(3.0);
});

it('leaves an order without a tip line untipped', function () {
    $order = PointOfSale::syncOrder(PosHelper::orderPayload(
        $this->config->refresh(),
        $this->session,
        [PosHelper::line($this->product->id, 1, 20.0)],
        [PosHelper::payment($this->cash, 20.0)],
    ));

    expect($order->refresh()->is_tipped)->toBeFalse()
        ->and((float) $order->tip_amount)->toBe(0.0);
});
