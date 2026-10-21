<?php

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Webkul\Account\Models\FiscalPosition;
use Webkul\PointOfSale\Enums\OrderState;
use Webkul\PointOfSale\Enums\PriceType;
use Webkul\PointOfSale\Events\OrderPaid;
use Webkul\PointOfSale\Exceptions\InsufficientPaymentException;
use Webkul\PointOfSale\Exceptions\OrderAlreadyPaidException;
use Webkul\PointOfSale\Exceptions\PosConfigurationException;
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
    $this->session = PosHelper::openSession($this->warehouse, 100.0);
    $this->config = $this->session->config;
    $this->cash = $this->config->paymentMethods->first();
    $this->product = InventoryHelper::product();

    InventoryHelper::stockUp($this->product, $this->warehouse->lotStockLocation, 50);
});

it('pays an order and settles it', function () {
    $order = PointOfSale::syncOrder(PosHelper::orderPayload(
        $this->config,
        $this->session,
        [PosHelper::line($this->product->id, 2, 100.0)],
        [PosHelper::payment($this->cash, 200.0)],
    ));

    expect($order->state)->toBe(OrderState::PAID)
        ->and((float) $order->amount_untaxed)->toBe(200.0)
        ->and((float) $order->amount_total)->toBe(200.0)
        ->and((float) $order->amount_paid)->toBe(200.0)
        ->and($order->confirmed_at)->not->toBeNull();
});

it('draws the order name from the terminal sequence exactly once', function () {
    $order = PointOfSale::syncOrder(PosHelper::orderPayload(
        $this->config,
        $this->session,
        [PosHelper::line($this->product->id, 1, 50.0)],
        [PosHelper::payment($this->cash, 50.0)],
    ));

    $name = $order->name;

    expect($name)->not->toBeNull()
        ->and($name)->toStartWith($this->config->code.'/');

    expect(fn () => PointOfSale::payOrder($order))
        ->toThrow(OrderAlreadyPaidException::class);

    expect($order->refresh()->name)->toBe($name);
});

it('applies a line discount before tax', function () {
    $order = PointOfSale::syncOrder(PosHelper::orderPayload(
        $this->config,
        $this->session,
        [PosHelper::line($this->product->id, 2, 100.0, ['discount' => 10])],
        [PosHelper::payment($this->cash, 180.0)],
    ));

    expect((float) $order->amount_untaxed)->toBe(180.0)
        ->and((float) $order->amount_total)->toBe(180.0);
});

it('records change as a negative cash payment', function () {
    $order = PointOfSale::syncOrder(PosHelper::orderPayload(
        $this->config,
        $this->session,
        [PosHelper::line($this->product->id, 1, 80.0)],
        [PosHelper::payment($this->cash, 100.0)],
    ));

    $change = $order->payments()->where('is_change', true)->first();

    expect($change)->not->toBeNull()
        ->and((float) $change->amount)->toBe(-20.0)
        ->and((float) $order->amount_return)->toBe(20.0)
        ->and((float) $order->amount_paid)->toBe(80.0)
        ->and((float) $order->payments->where('is_change', false)->sum('amount'))->toBe(100.0);
});

it('refuses to settle an order that is not fully paid', function () {
    expect(fn () => PointOfSale::syncOrder(PosHelper::orderPayload(
        $this->config,
        $this->session,
        [PosHelper::line($this->product->id, 2, 100.0)],
        [PosHelper::payment($this->cash, 100.0)],
    )))->toThrow(InsufficientPaymentException::class);
});

it('moves stock out of the warehouse when the order is paid', function () {
    $before = PosHelper::quantityOnHand($this->product->id, $this->warehouse->lotStockLocation);

    $order = PointOfSale::syncOrder(PosHelper::orderPayload(
        $this->config,
        $this->session,
        [PosHelper::line($this->product->id, 3, 100.0)],
        [PosHelper::payment($this->cash, 300.0)],
    ));

    $after = PosHelper::quantityOnHand($this->product->id, $this->warehouse->lotStockLocation);

    expect($order->operation_id)->not->toBeNull()
        ->and($after)->toBe($before - 3.0)
        ->and($order->has_failed_operation)->toBeFalse();
});

it('snapshots the product cost on the line', function () {
    $this->product->update(['cost' => 40.0]);

    $order = PointOfSale::syncOrder(PosHelper::orderPayload(
        $this->config,
        $this->session,
        [PosHelper::line($this->product->id, 2, 100.0)],
        [PosHelper::payment($this->cash, 200.0)],
    ));

    $this->product->update(['cost' => 90.0]);

    $line = $order->lines()->first();

    expect((float) $line->unit_cost)->toBe(40.0)
        ->and((float) $line->total_cost)->toBe(80.0)
        ->and((float) $order->refresh()->total_cost)->toBe(80.0)
        ->and((float) $order->margin)->toBe(120.0);
});

it('is idempotent on the client uuid', function () {
    $payload = PosHelper::orderPayload(
        $this->config,
        $this->session,
        [PosHelper::line($this->product->id, 1, 60.0)],
        [PosHelper::payment($this->cash, 60.0)],
    );

    $first = PointOfSale::syncOrder($payload);
    $second = PointOfSale::syncOrder($payload);

    expect($second->id)->toBe($first->id)
        ->and(Order::query()->where('uuid', $payload['uuid'])->count())->toBe(1)
        ->and($first->lines()->count())->toBe(1);
});

it('stamps a receipt reference and tracking number', function () {
    $order = PointOfSale::syncOrder(PosHelper::orderPayload(
        $this->config,
        $this->session,
        [PosHelper::line($this->product->id, 1, 10.0)],
        [PosHelper::payment($this->cash, 10.0)],
    ));

    expect($order->reference)->not->toBeNull()
        ->and($order->tracking_number)->not->toBeNull()
        ->and($order->receipt_code)->not->toBeNull()
        ->and($order->sequence_number)->toBeGreaterThan(0);
});

it('refreshes the session totals when an order is paid', function () {
    PointOfSale::syncOrder(PosHelper::orderPayload(
        $this->config,
        $this->session,
        [PosHelper::line($this->product->id, 1, 120.0)],
        [PosHelper::payment($this->cash, 120.0)],
    ));

    $session = $this->session->refresh();

    expect($session->order_count)->toBe(1)
        ->and((float) $session->total_payments_amount)->toBe(120.0)
        ->and((float) $session->cash_transaction_total)->toBe(120.0);
});

it('marks a paid order as done', function () {
    $order = PointOfSale::syncOrder(PosHelper::orderPayload(
        $this->config,
        $this->session,
        [PosHelper::line($this->product->id, 1, 15.0)],
        [PosHelper::payment($this->cash, 15.0)],
    ));

    expect(PointOfSale::completeOrder($order)->state)->toBe(OrderState::DONE);
});

it('dispatches the order paid event', function () {
    Event::fake([OrderPaid::class]);

    PointOfSale::syncOrder(PosHelper::orderPayload(
        $this->config,
        $this->session,
        [PosHelper::line($this->product->id, 1, 25.0)],
        [PosHelper::payment($this->cash, 25.0)],
    ));

    Event::assertDispatched(OrderPaid::class);
});

it('returns the change in cash even when the overpayment came from a card', function () {
    $bank = PosHelper::bankMethod();

    $this->config->paymentMethods()->attach($bank);

    $order = PointOfSale::syncOrder(PosHelper::orderPayload(
        $this->config->refresh(),
        $this->session,
        [PosHelper::line($this->product->id, 1, 450.0)],
        [PosHelper::payment($bank, 500.0)],
    ));

    $change = $order->payments->firstWhere('is_change', true);

    expect((float) $order->amount_return)->toBe(50.0)
        ->and((float) $change->amount)->toBe(-50.0)
        ->and($change->payment_method_id)->toBe($this->cash->id);
});

it('splits the tender across two payment methods', function () {
    $bank = PosHelper::bankMethod();

    $this->config->paymentMethods()->attach($bank);

    $order = PointOfSale::syncOrder(PosHelper::orderPayload(
        $this->config->refresh(),
        $this->session,
        [PosHelper::line($this->product->id, 1, 450.0)],
        [PosHelper::payment($bank, 400.0), PosHelper::payment($this->cash, 100.0)],
    ));

    expect((float) $order->amount_paid)->toBe(450.0)
        ->and((float) $order->amount_return)->toBe(50.0)
        ->and($order->payments)->toHaveCount(3)
        ->and((float) $order->payments->where('is_change', false)->sum('amount'))->toBe(500.0);
});

it('keeps the time the till took the payment as the order date', function () {
    $order = PointOfSale::syncOrder(PosHelper::orderPayload(
        $this->config,
        $this->session,
        [PosHelper::line($this->product->id, 1, 100.0)],
        [PosHelper::payment($this->cash, 100.0)],
        ['ordered_at' => '2026-09-20T04:30:00.000Z'],
    ));

    expect($order->refresh()->ordered_at->equalTo(Date::parse('2026-09-20T04:30:00Z')))->toBeTrue();
});

it('rejects a line discount when line discounts are off', function () {
    $this->config->forceFill(['enable_line_discount' => false])->save();

    expect(fn () => PointOfSale::syncOrder(PosHelper::orderPayload(
        $this->config->refresh(),
        $this->session,
        [PosHelper::line($this->product->id, 1, 100.0, ['discount' => 10])],
        [PosHelper::payment($this->cash, 90.0)],
    )))->toThrow(PosConfigurationException::class, __('point-of-sale::system.order-processor.line-discount-disabled'));
});

it('rejects a changed price from a cashier on a price controlled register', function () {
    $this->product->update(['price' => 100.0]);

    $this->config->forceFill(['enable_price_control' => true])->save();

    Gate::before(fn (): ?bool => false);

    expect(fn () => PointOfSale::syncOrder(PosHelper::orderPayload(
        $this->config->refresh(),
        $this->session,
        [PosHelper::line($this->product->id, 1, 1.0)],
        [PosHelper::payment($this->cash, 1.0)],
    )))->toThrow(PosConfigurationException::class);
});

it('lets a cashier pay a price a manager already set on a shared draft', function () {
    $this->product->update(['price' => 100.0]);

    $this->config->forceFill(['enable_price_control' => true])->save();

    $line = PosHelper::line($this->product->id, 1, 80.0, ['price_type' => PriceType::MANUAL->value]);

    $draft = PosHelper::orderPayload($this->config->refresh(), $this->session, [$line]);

    unset($draft['payments']);

    PointOfSale::saveDraftOrders([$draft]);

    Gate::before(fn (): ?bool => false);

    $order = PointOfSale::syncOrder(PosHelper::orderPayload(
        $this->config->refresh(),
        $this->session,
        [$line],
        [PosHelper::payment($this->cash, 80.0)],
        ['uuid' => $draft['uuid']],
    ));

    expect($order->refresh()->state)->not->toBe(OrderState::DRAFT)
        ->and((float) $order->lines->first()->price_unit)->toBe(80.0);

    expect(fn () => PointOfSale::syncOrder(PosHelper::orderPayload(
        $this->config->refresh(),
        $this->session,
        [PosHelper::line($this->product->id, 1, 70.0)],
        [PosHelper::payment($this->cash, 70.0)],
    )))->toThrow(PosConfigurationException::class);
});

it('puts a line back on the list price once the till stops sending a manual price', function () {
    $line = PosHelper::line($this->product->id, 1, 80.0);

    $draft = PosHelper::orderPayload($this->config->refresh(), $this->session, [$line]);

    unset($draft['payments']);

    PointOfSale::saveDraftOrders([$draft]);

    $draft['lines'] = [Arr::except($line, ['price_unit'])];

    PointOfSale::saveDraftOrders([$draft]);

    expect(Order::where('uuid', $draft['uuid'])->firstOrFail()->lines->first()->price_type)->toBe(PriceType::ORIGINAL);
});

it('drops a fiscal position the register does not allow', function () {
    $position = FiscalPosition::create([
        'name'       => 'Export',
        'company_id' => PosHelper::company()->id,
    ]);

    $this->config->forceFill(['enable_fiscal_position' => false])->save();

    $order = PointOfSale::syncOrder(PosHelper::orderPayload(
        $this->config->refresh(),
        $this->session,
        [PosHelper::line($this->product->id, 1, 100.0)],
        [PosHelper::payment($this->cash, 100.0)],
        ['fiscal_position_id' => $position->id],
    ));

    expect($order->refresh()->fiscal_position_id)->toBeNull();
});
