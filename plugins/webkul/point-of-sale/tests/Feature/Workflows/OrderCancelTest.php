<?php

use Illuminate\Support\Facades\Event;
use Webkul\PointOfSale\Enums\OrderState;
use Webkul\PointOfSale\Events\OrderCanceled;
use Webkul\PointOfSale\Exceptions\OrderAlreadyPaidException;
use Webkul\PointOfSale\Facades\PointOfSale;
use Webkul\PointOfSale\Models\Order;
use Webkul\PointOfSale\Models\OrderLine;
use Webkul\PointOfSale\Services\BootLoader;

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

it('cancels a draft order', function () {
    $order = Order::create([
        'config_id'  => $this->config->id,
        'session_id' => $this->session->id,
    ]);

    expect(PointOfSale::cancelOrder($order)->state)->toBe(OrderState::CANCELED);
});

it('refuses to cancel a settled order', function () {
    $order = PointOfSale::syncOrder(PosHelper::orderPayload(
        $this->config,
        $this->session,
        [PosHelper::line($this->product->id, 1, 50.0)],
        [PosHelper::payment($this->cash, 50.0)],
    ));

    expect(fn () => PointOfSale::cancelOrder($order))
        ->toThrow(OrderAlreadyPaidException::class);
});

it('dispatches the cancelled event', function () {
    Event::fake([OrderCanceled::class]);

    $order = Order::create([
        'config_id'  => $this->config->id,
        'session_id' => $this->session->id,
    ]);

    PointOfSale::cancelOrder($order);

    Event::assertDispatched(OrderCanceled::class);
});

it('releases the refunded quantity when the refund is cancelled', function () {
    $order = PointOfSale::syncOrder(PosHelper::orderPayload(
        $this->config,
        $this->session,
        [PosHelper::line($this->product->id, 3, 50.0)],
        [PosHelper::payment($this->cash, 150.0)],
    ));

    $line = $order->lines->first();

    $refund = PointOfSale::refundOrder($order, [$line->id => 2]);

    expect((float) $line->refresh()->refunded_qty)->toBe(2.0);

    PointOfSale::cancelOrder($refund);

    expect((float) $line->refresh()->refunded_qty)->toBe(0.0)
        ->and($line->refundableQty())->toBe(3.0);
});

it('keeps the refunded quantity while the refund is still live', function () {
    $order = PointOfSale::syncOrder(PosHelper::orderPayload(
        $this->config,
        $this->session,
        [PosHelper::line($this->product->id, 3, 50.0)],
        [PosHelper::payment($this->cash, 150.0)],
    ));

    $line = $order->lines->first();

    PointOfSale::refundOrder($order, [$line->id => 1]);

    expect((float) $line->refresh()->refunded_qty)->toBe(1.0)
        ->and($line->refundableQty())->toBe(2.0);
});

it('allows refunding the released quantity again after a cancellation', function () {
    $order = PointOfSale::syncOrder(PosHelper::orderPayload(
        $this->config,
        $this->session,
        [PosHelper::line($this->product->id, 2, 50.0)],
        [PosHelper::payment($this->cash, 100.0)],
    ));

    $line = $order->lines->first();

    $refund = PointOfSale::refundOrder($order, [$line->id => 2]);

    PointOfSale::cancelOrder($refund);

    $second = PointOfSale::refundOrder($order->refresh(), [$line->id => 2]);

    expect($second->lines)->toHaveCount(1)
        ->and((float) $second->lines->first()->qty)->toBe(-2.0)
        ->and((float) $line->refresh()->refunded_qty)->toBe(2.0);
});

it('counts only live refund lines on the original line', function () {
    $order = PointOfSale::syncOrder(PosHelper::orderPayload(
        $this->config,
        $this->session,
        [PosHelper::line($this->product->id, 5, 50.0)],
        [PosHelper::payment($this->cash, 250.0)],
    ));

    $line = $order->lines->first();

    $first = PointOfSale::refundOrder($order, [$line->id => 1]);

    PointOfSale::refundOrder($order->refresh(), [$line->id => 2]);

    PointOfSale::cancelOrder($first);

    expect((float) $line->refresh()->refunded_qty)->toBe(2.0)
        ->and(OrderLine::withoutGlobalScopes()->where('refunded_order_line_id', $line->id)->count())->toBe(2);
});

it('cancels a server draft order from the till', function () {
    $order = Order::create([
        'config_id'  => $this->config->id,
        'session_id' => $this->session->id,
    ]);

    $this->postJson(route('point-of-sale.till.orders.cancel'), ['uuid' => $order->uuid])
        ->assertOk()
        ->assertJsonPath('data.state', OrderState::CANCELED->value);

    expect($order->refresh()->state)->toBe(OrderState::CANCELED);
});

it('treats cancelling an already cancelled order from the till as done', function () {
    $order = Order::create([
        'config_id'  => $this->config->id,
        'session_id' => $this->session->id,
    ]);

    PointOfSale::cancelOrder($order);

    $this->postJson(route('point-of-sale.till.orders.cancel'), ['uuid' => $order->uuid])
        ->assertOk()
        ->assertJsonPath('data.state', OrderState::CANCELED->value);
});

it('refuses to cancel a settled order from the till', function () {
    $order = PointOfSale::syncOrder(PosHelper::orderPayload(
        $this->config,
        $this->session,
        [PosHelper::line($this->product->id, 1, 50.0)],
        [PosHelper::payment($this->cash, 50.0)],
    ));

    $this->postJson(route('point-of-sale.till.orders.cancel'), ['uuid' => $order->uuid])
        ->assertUnprocessable()
        ->assertJsonPath('message', __('point-of-sale::system.order-workflow.cancel.not-draft', ['order' => $order->reference]));

    expect($order->refresh()->state)->not->toBe(OrderState::CANCELED);
});

it('ships the cancel endpoint to the till', function () {
    $payload = app(BootLoader::class)->load($this->config, $this->session);

    expect($payload['config']['order_cancel_endpoint'])->toBe(route('point-of-sale.till.orders.cancel'));
});
