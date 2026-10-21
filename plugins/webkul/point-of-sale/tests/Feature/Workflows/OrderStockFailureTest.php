<?php

use Illuminate\Support\Facades\Event;
use Webkul\Inventory\Enums\OperationState;
use Webkul\PointOfSale\Enums\OrderState;
use Webkul\PointOfSale\Events\OrderOperationFailed;
use Webkul\PointOfSale\Facades\PointOfSale;
use Webkul\Product\Enums\ProductType;

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
    $this->tracked = InventoryHelper::lotTrackedProduct();
});

it('settles the sale even when there is no stock on hand', function () {
    $order = PointOfSale::syncOrder(PosHelper::orderPayload(
        $this->config,
        $this->session,
        [PosHelper::line($this->product->id, 5, 20.0)],
        [PosHelper::payment($this->cash, 100.0)],
    ));

    expect($order->state)->toBe(OrderState::PAID)
        ->and((float) $order->amount_total)->toBe(100.0);
});

it('drives the stock negative when more is sold than is on hand', function () {
    InventoryHelper::stockUp($this->product, $this->warehouse->lotStockLocation, 2);

    $order = PointOfSale::syncOrder(PosHelper::orderPayload(
        $this->config,
        $this->session,
        [PosHelper::line($this->product->id, 9, 20.0)],
        [PosHelper::payment($this->cash, 180.0)],
    ));

    expect($order->has_failed_operation)->toBeFalse()
        ->and($order->operation->state)->toBe(OperationState::DONE)
        ->and(PosHelper::quantityOnHand($this->product->id, $this->warehouse->lotStockLocation))->toBe(-7.0);
});

it('flags the order and the session when the operation cannot complete', function () {
    $order = PointOfSale::syncOrder(PosHelper::orderPayload(
        $this->config,
        $this->session,
        [PosHelper::line($this->tracked->id, 5, 20.0)],
        [PosHelper::payment($this->cash, 100.0)],
    ));

    expect($order->has_failed_operation)->toBeTrue()
        ->and($this->session->refresh()->has_failed_operations)->toBeTrue();
});

it('settles the sale even when the operation could not complete', function () {
    $order = PointOfSale::syncOrder(PosHelper::orderPayload(
        $this->config,
        $this->session,
        [PosHelper::line($this->tracked->id, 5, 20.0)],
        [PosHelper::payment($this->cash, 100.0)],
    ));

    expect($order->state)->toBe(OrderState::PAID);
});

it('keeps the operation record behind for the back office', function () {
    $order = PointOfSale::syncOrder(PosHelper::orderPayload(
        $this->config,
        $this->session,
        [PosHelper::line($this->tracked->id, 5, 20.0)],
        [PosHelper::payment($this->cash, 100.0)],
    ));

    $operation = $order->operation;

    expect($operation)->not->toBeNull()
        ->and($operation->state)->not->toBe(OperationState::DONE)
        ->and($operation->moves()->count())->toBe(1);
});

it('leaves stock untouched when the operation failed', function () {
    $lot = InventoryHelper::lot($this->tracked, 'POS-FAIL-1');

    InventoryHelper::stockUp($this->tracked, $this->warehouse->lotStockLocation, 2, $lot->id);

    $before = PosHelper::quantityOnHand($this->tracked->id, $this->warehouse->lotStockLocation);

    PointOfSale::syncOrder(PosHelper::orderPayload(
        $this->config,
        $this->session,
        [PosHelper::line($this->tracked->id, 9, 20.0)],
        [PosHelper::payment($this->cash, 180.0)],
    ));

    expect(PosHelper::quantityOnHand($this->tracked->id, $this->warehouse->lotStockLocation))->toBe($before);
});

it('keeps the order lines and payments after a failed operation', function () {
    $order = PointOfSale::syncOrder(PosHelper::orderPayload(
        $this->config,
        $this->session,
        [PosHelper::line($this->tracked->id, 4, 20.0)],
        [PosHelper::payment($this->cash, 80.0)],
    ));

    expect($order->lines()->count())->toBe(1)
        ->and($order->payments()->count())->toBe(1);
});

it('dispatches the failed operation event with the order', function () {
    Event::fake([OrderOperationFailed::class]);

    PointOfSale::syncOrder(PosHelper::orderPayload(
        $this->config,
        $this->session,
        [PosHelper::line($this->tracked->id, 6, 20.0)],
        [PosHelper::payment($this->cash, 120.0)],
    ));

    Event::assertDispatched(OrderOperationFailed::class);
});

it('completes the operation on retry once the lot arrives in stock', function () {
    $order = PointOfSale::syncOrder(PosHelper::orderPayload(
        $this->config,
        $this->session,
        [PosHelper::line($this->tracked->id, 2, 20.0)],
        [PosHelper::payment($this->cash, 40.0)],
    ));

    expect($order->has_failed_operation)->toBeTrue();

    $lot = InventoryHelper::lot($this->tracked, 'POS-RETRY-1');

    InventoryHelper::stockUp($this->tracked, $this->warehouse->lotStockLocation, 10, $lot->id);

    PointOfSale::retryOrderPicking($order);

    expect($order->refresh()->has_failed_operation)->toBeFalse()
        ->and($order->operation->refresh()->state)->toBe(OperationState::DONE);
});

it('reserves an available lot when the till captured none', function () {
    $lot = InventoryHelper::lot($this->tracked, 'POS-AUTO-1');

    InventoryHelper::stockUp($this->tracked, $this->warehouse->lotStockLocation, 10, $lot->id);

    $order = PointOfSale::syncOrder(PosHelper::orderPayload(
        $this->config,
        $this->session,
        [PosHelper::line($this->tracked->id, 2, 20.0)],
        [PosHelper::payment($this->cash, 40.0)],
    ));

    expect($order->has_failed_operation)->toBeFalse()
        ->and($order->operation->state)->toBe(OperationState::DONE)
        ->and(PosHelper::quantityOnHand($this->tracked->id, $this->warehouse->lotStockLocation))->toBe(8.0);
});

it('creates no operation for a service product', function () {
    $service = InventoryHelper::product(['type' => ProductType::SERVICE, 'is_storable' => false]);

    $order = PointOfSale::syncOrder(PosHelper::orderPayload(
        $this->config,
        $this->session,
        [PosHelper::line($service->id, 1, 500.0)],
        [PosHelper::payment($this->cash, 500.0)],
    ));

    expect($order->state)->toBe(OrderState::PAID)
        ->and($order->operation_id)->toBeNull()
        ->and($order->has_failed_operation)->toBeFalse();
});
