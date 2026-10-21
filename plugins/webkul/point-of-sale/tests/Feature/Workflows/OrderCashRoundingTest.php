<?php

use Webkul\Account\Enums\RoundingMethod;
use Webkul\PointOfSale\Facades\PointOfSale;

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

    InventoryHelper::stockUp($this->product, $this->warehouse->lotStockLocation, 50);
});

it('leaves the total untouched when cash rounding is disabled', function () {
    $order = PointOfSale::syncOrder(PosHelper::orderPayload(
        $this->config,
        $this->session,
        [PosHelper::line($this->product->id, 1, 10.02)],
        [PosHelper::payment($this->cash, 10.02)],
    ));

    expect((float) $order->amount_total)->toBe(10.02)
        ->and((float) $order->amount_rounding)->toBe(0.0);
});

it('rounds the total to the nearest denomination', function () {
    $this->config->forceFill([
        'enable_cash_rounding' => true,
        'cash_rounding_id'     => PosHelper::cashRounding()->id,
    ])->save();

    $order = PointOfSale::syncOrder(PosHelper::orderPayload(
        $this->config->refresh(),
        $this->session,
        [PosHelper::line($this->product->id, 1, 10.02)],
        [PosHelper::payment($this->cash, 10.0)],
    ));

    expect((float) $order->amount_rounding)->toBe(-0.02)
        ->and((float) $order->amount_total)->toBe(10.0);
});

it('rounds a total up when the rounding method says so', function () {
    $this->config->forceFill([
        'enable_cash_rounding' => true,
        'cash_rounding_id'     => PosHelper::cashRounding(['rounding_method' => RoundingMethod::UP])->id,
    ])->save();

    $order = PointOfSale::syncOrder(PosHelper::orderPayload(
        $this->config->refresh(),
        $this->session,
        [PosHelper::line($this->product->id, 1, 10.02)],
        [PosHelper::payment($this->cash, 10.05)],
    ));

    expect((float) $order->amount_rounding)->toBe(0.03)
        ->and((float) $order->amount_total)->toBe(10.05);
});

it('skips rounding on a card only order when only cash methods round', function () {
    $bank = PosHelper::bankMethod();

    $this->config->paymentMethods()->attach($bank);

    $this->config->forceFill([
        'enable_cash_rounding'          => true,
        'enable_only_round_cash_method' => true,
        'cash_rounding_id'              => PosHelper::cashRounding()->id,
    ])->save();

    $order = PointOfSale::syncOrder(PosHelper::orderPayload(
        $this->config->refresh(),
        $this->session,
        [PosHelper::line($this->product->id, 1, 10.02)],
        [PosHelper::payment($bank, 10.02)],
    ));

    expect((float) $order->amount_rounding)->toBe(0.0)
        ->and((float) $order->amount_total)->toBe(10.02);
});

it('still rounds a cash order when only cash methods round', function () {
    $this->config->forceFill([
        'enable_cash_rounding'          => true,
        'enable_only_round_cash_method' => true,
        'cash_rounding_id'              => PosHelper::cashRounding()->id,
    ])->save();

    $order = PointOfSale::syncOrder(PosHelper::orderPayload(
        $this->config->refresh(),
        $this->session,
        [PosHelper::line($this->product->id, 1, 10.02)],
        [PosHelper::payment($this->cash, 10.0)],
    ));

    expect((float) $order->amount_rounding)->toBe(-0.02)
        ->and((float) $order->amount_total)->toBe(10.0);
});

it('registers the change against the rounded total', function () {
    $this->config->forceFill([
        'enable_cash_rounding' => true,
        'cash_rounding_id'     => PosHelper::cashRounding()->id,
    ])->save();

    $order = PointOfSale::syncOrder(PosHelper::orderPayload(
        $this->config->refresh(),
        $this->session,
        [PosHelper::line($this->product->id, 1, 10.02)],
        [PosHelper::payment($this->cash, 20.0)],
    ));

    expect((float) $order->amount_total)->toBe(10.0)
        ->and((float) $order->amount_return)->toBe(10.0)
        ->and((float) $order->payments->where('is_change', true)->sum('amount'))->toBe(-10.0);
});
