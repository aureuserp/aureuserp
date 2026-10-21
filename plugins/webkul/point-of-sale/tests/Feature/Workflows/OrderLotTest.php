<?php

use Webkul\Inventory\Enums\OperationState;
use Webkul\Inventory\Enums\ProductTracking;
use Webkul\Inventory\Models\Lot;
use Webkul\PointOfSale\Enums\OrderState;
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
    $this->product = InventoryHelper::lotTrackedProduct();
    $this->lot = InventoryHelper::lot($this->product, 'POS-LOT-1');

    InventoryHelper::stockUp($this->product, $this->warehouse->lotStockLocation, 10, $this->lot->id);
});

it('stores the captured lots on the order line', function () {
    $order = PointOfSale::syncOrder(PosHelper::orderPayload(
        $this->config,
        $this->session,
        [PosHelper::line($this->product->id, 2, 50.0, [
            'lots' => [['lot_name' => $this->lot->name, 'qty' => 2]],
        ])],
        [PosHelper::payment($this->cash, 100.0)],
    ));

    $line = $order->lines()->first();

    expect($line->lots)->toHaveCount(1)
        ->and($line->lots->first()->lot_name)->toBe($this->lot->name)
        ->and((float) $line->lots->first()->qty)->toBe(2.0);
});

it('resolves the captured lot to an existing lot record', function () {
    $order = PointOfSale::syncOrder(PosHelper::orderPayload(
        $this->config,
        $this->session,
        [PosHelper::line($this->product->id, 1, 50.0, [
            'lots' => [['lot_name' => $this->lot->name, 'qty' => 1]],
        ])],
        [PosHelper::payment($this->cash, 50.0)],
    ));

    expect($order->lines()->first()->lots->first()->lot_id)->toBe($this->lot->id);
});

it('moves the tracked stock out with its lot', function () {
    $order = PointOfSale::syncOrder(PosHelper::orderPayload(
        $this->config,
        $this->session,
        [PosHelper::line($this->product->id, 2, 50.0, [
            'lots' => [['lot_name' => $this->lot->name, 'qty' => 2]],
        ])],
        [PosHelper::payment($this->cash, 100.0)],
    ));

    $operation = $order->operation;

    expect($operation?->state)->toBe(OperationState::DONE)
        ->and($operation->moves->first()->lines->first()->lot_id)->toBe($this->lot->id);
});

it('creates a missing lot when the operation type allows it', function () {
    $order = PointOfSale::syncOrder(PosHelper::orderPayload(
        $this->config,
        $this->session,
        [PosHelper::line($this->product->id, 1, 50.0, [
            'lots' => [['lot_name' => 'POS-NEW-LOT', 'qty' => 1]],
        ])],
        [PosHelper::payment($this->cash, 50.0)],
    ));

    expect(Lot::query()->where('name', 'POS-NEW-LOT')->exists())->toBeTrue()
        ->and($order->lines()->first()->lots->first()->lot_id)->not->toBeNull();
});

it('still settles a tracked sale when no lot was captured', function () {
    $order = PointOfSale::syncOrder(PosHelper::orderPayload(
        $this->config,
        $this->session,
        [PosHelper::line($this->product->id, 1, 50.0)],
        [PosHelper::payment($this->cash, 50.0)],
    ));

    expect($order->state)->toBe(OrderState::PAID);
});

it('ignores lots for an untracked product', function () {
    $product = InventoryHelper::product();

    InventoryHelper::stockUp($product, $this->warehouse->lotStockLocation, 5);

    $order = PointOfSale::syncOrder(PosHelper::orderPayload(
        $this->config,
        $this->session,
        [PosHelper::line($product->id, 1, 50.0, [
            'lots' => [['lot_name' => 'IGNORED', 'qty' => 1]],
        ])],
        [PosHelper::payment($this->cash, 50.0)],
    ));

    expect($order->operation?->state)->toBe(OperationState::DONE)
        ->and($product->tracking)->not->toBe(ProductTracking::LOT);
});
