<?php

use Illuminate\Support\Str;
use Webkul\PointOfSale\Enums\OrderState;
use Webkul\PointOfSale\Enums\PriceType;
use Webkul\PointOfSale\Enums\SessionState;
use Webkul\PointOfSale\Exceptions\OrderAlreadyPaidException;
use Webkul\PointOfSale\Facades\PointOfSale;
use Webkul\PointOfSale\Models\Order;
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
    $this->product = InventoryHelper::product(['price' => 40.0]);
    $this->floor = PosHelper::floor();
    $this->table = PosHelper::table($this->floor, ['table_number' => '4']);

    $this->config->update(['is_restaurant' => true]);

    $this->config->floors()->attach($this->floor->id);

    InventoryHelper::stockUp($this->product, $this->warehouse->lotStockLocation, 50);
});

function posDraftPayload($test, array $lines, array $overrides = []): array
{
    $payload = PosHelper::orderPayload($test->config->refresh(), $test->session, $lines, [], $overrides);

    unset($payload['payments']);

    return $payload;
}

it('saves a booked table draft without lines or payments', function () {
    $payload = posDraftPayload($this, [], [
        'table_id'       => $this->table->id,
        'is_booked'      => true,
        'customer_count' => 2,
    ]);

    $result = PointOfSale::saveDraftOrders([$payload]);

    $order = Order::where('uuid', $payload['uuid'])->firstOrFail();

    expect($result['errors'])->toBe([])
        ->and($order->state)->toBe(OrderState::DRAFT)
        ->and($order->table_id)->toBe($this->table->id)
        ->and($order->is_booked)->toBeTrue()
        ->and($order->customer_count)->toBe(2)
        ->and($order->lines)->toHaveCount(0);
});

it('updates a saved draft and prunes the lines removed since', function () {
    $first = PosHelper::line($this->product->id, 1, 40.0);
    $second = PosHelper::line($this->product->id, 2, 40.0);

    $payload = posDraftPayload($this, [$first, $second], ['floating_name' => 'Patio 2']);

    PointOfSale::saveDraftOrders([$payload]);

    $payload['lines'] = [$first];
    $payload['customer_count'] = 3;

    PointOfSale::saveDraftOrders([$payload]);

    $order = Order::where('uuid', $payload['uuid'])->firstOrFail();

    expect($order->lines)->toHaveCount(1)
        ->and($order->lines->first()->uuid)->toBe($first['uuid'])
        ->and($order->floating_name)->toBe('Patio 2')
        ->and($order->customer_count)->toBe(3);
});

it('settles a shared draft with only the lines the till still has', function () {
    $kept = PosHelper::line($this->product->id, 1, 40.0);
    $dropped = PosHelper::line($this->product->id, 1, 40.0);

    $payload = posDraftPayload($this, [$kept, $dropped], ['table_id' => $this->table->id]);

    PointOfSale::saveDraftOrders([$payload]);

    $order = PointOfSale::syncOrder(PosHelper::orderPayload(
        $this->config->refresh(),
        $this->session,
        [$kept],
        [PosHelper::payment($this->cash, 40.0)],
        ['uuid' => $payload['uuid'], 'table_id' => $this->table->id],
    ));

    expect($order->refresh()->state)->not->toBe(OrderState::DRAFT)
        ->and($order->lines)->toHaveCount(1)
        ->and((float) $order->amount_total)->toBe(40.0);
});

it('lists shared drafts with their table, name, booking and manual prices', function () {
    $line = PosHelper::line($this->product->id, 1, 35.0, ['price_type' => PriceType::MANUAL->value]);

    $payload = posDraftPayload($this, [$line], [
        'table_id'      => $this->table->id,
        'floating_name' => null,
        'is_booked'     => true,
    ]);

    PointOfSale::saveDraftOrders([$payload]);

    $draft = collect(app(BootLoader::class)->drafts($this->session))->firstWhere('uuid', $payload['uuid']);

    expect($draft['table_id'])->toBe($this->table->id)
        ->and($draft['is_booked'])->toBeTrue()
        ->and($draft['lines'][0]['price_overridden'])->toBeTrue()
        ->and($draft['lines'][0]['price_unit'])->toBe(35.0);
});

it('refuses to save a draft into a closed session', function () {
    $this->session->forceFill(['state' => SessionState::CLOSED])->save();

    $payload = posDraftPayload($this, [], ['table_id' => $this->table->id, 'is_booked' => true]);

    $result = PointOfSale::saveDraftOrders([$payload]);

    expect($result['errors'])->toHaveCount(1)
        ->and(Order::where('uuid', $payload['uuid'])->exists())->toBeFalse();
});

it('leaves a paid order untouched when another till saves it as a draft', function () {
    $line = PosHelper::line($this->product->id, 1, 40.0);

    $payload = posDraftPayload($this, [$line], ['table_id' => $this->table->id]);

    PointOfSale::saveDraftOrders([$payload]);

    $paid = PointOfSale::syncOrder(PosHelper::orderPayload(
        $this->config->refresh(),
        $this->session,
        [$line],
        [PosHelper::payment($this->cash, 40.0)],
        ['uuid' => $payload['uuid'], 'table_id' => $this->table->id],
    ));

    $payload['lines'] = [$line, PosHelper::line($this->product->id, 3, 40.0)];

    PointOfSale::saveDraftOrders([$payload]);

    $order = Order::where('uuid', $payload['uuid'])->firstOrFail();

    expect($order->id)->toBe($paid->id)
        ->and($order->state)->not->toBe(OrderState::DRAFT)
        ->and($order->lines)->toHaveCount(1)
        ->and($order->payments)->toHaveCount(1)
        ->and((float) $order->amount_total)->toBe(40.0);
});

it('accepts a replayed payment but refuses a new payment on an order another till already paid', function () {
    $line = PosHelper::line($this->product->id, 1, 40.0);

    $payload = posDraftPayload($this, [$line], ['table_id' => $this->table->id]);

    PointOfSale::saveDraftOrders([$payload]);

    $first = PosHelper::orderPayload(
        $this->config->refresh(),
        $this->session,
        [$line],
        [PosHelper::payment($this->cash, 40.0)],
        ['uuid' => $payload['uuid'], 'table_id' => $this->table->id],
    );

    $paid = PointOfSale::syncOrder($first);

    expect(PointOfSale::syncOrder($first)->id)->toBe($paid->id);

    $second = array_merge($first, ['payments' => [PosHelper::payment($this->cash, 40.0)]]);

    expect(fn () => PointOfSale::syncOrder($second))
        ->toThrow(OrderAlreadyPaidException::class, Str::before(__('point-of-sale::system.order-workflow.mark-paid.already-settled'), ':'));

    $result = PointOfSale::syncOrders([$second]);

    expect($result['errors'][0]['code'])->toBe('already-settled')
        ->and($paid->refresh()->payments)->toHaveCount(1);
});

it('reads a refund draft back with its lots and refund links and keeps the order link when it is paid', function () {
    $sold = PointOfSale::syncOrder(PosHelper::orderPayload(
        $this->config->refresh(),
        $this->session,
        [PosHelper::line($this->product->id, 1, 40.0)],
        [PosHelper::payment($this->cash, 40.0)],
    ));

    $original = $sold->lines()->first();

    $line = PosHelper::line($this->product->id, -1, 40.0, [
        'refunded_order_line_id' => $original->id,
        'lots'                   => [['lot_name' => 'LOT-7', 'qty' => 1]],
    ]);

    $payload = posDraftPayload($this, [$line], ['refunded_order_id' => $sold->id]);

    PointOfSale::saveDraftOrders([$payload]);

    $draft = collect(app(BootLoader::class)->drafts($this->session))->firstWhere('uuid', $payload['uuid']);

    expect($draft['refunded_order_id'])->toBe($sold->id)
        ->and($draft['lines'][0]['refunded_order_line_id'])->toBe($original->id)
        ->and($draft['lines'][0]['lots'][0]['lot_name'])->toBe('LOT-7');

    $refund = PointOfSale::syncOrder(PosHelper::orderPayload(
        $this->config->refresh(),
        $this->session,
        [$line],
        [PosHelper::payment($this->cash, -40.0)],
        ['uuid' => $payload['uuid'], 'refunded_order_id' => null],
    ));

    expect($refund->refresh()->refunded_order_id)->toBe($sold->id);
});

it('refuses to move a line that belongs to another order', function () {
    $line = PosHelper::line($this->product->id, 1, 40.0);

    $paid = PointOfSale::syncOrder(PosHelper::orderPayload(
        $this->config->refresh(),
        $this->session,
        [$line],
        [PosHelper::payment($this->cash, 40.0)],
    ));

    $result = PointOfSale::saveDraftOrders([posDraftPayload($this, [$line], ['table_id' => $this->table->id])]);

    expect($result['errors'])->toHaveCount(1)
        ->and($result['errors'][0]['message'])->toBe(__('point-of-sale::system.order-processor.foreign-line'))
        ->and($paid->refresh()->lines)->toHaveCount(1);
});

it('refuses to change an order of another register', function () {
    $payload = posDraftPayload($this, [PosHelper::line($this->product->id, 1, 40.0)], ['table_id' => $this->table->id]);

    PointOfSale::saveDraftOrders([$payload]);

    $other = PosHelper::configWithCashMethod($this->warehouse);

    $result = PointOfSale::saveDraftOrders([array_merge($payload, ['config_id' => $other->id, 'lines' => []])]);

    expect($result['errors'])->toHaveCount(1)
        ->and($result['errors'][0]['message'])->toBe(__('point-of-sale::system.order-processor.foreign-order'))
        ->and(Order::where('uuid', $payload['uuid'])->firstOrFail()->lines)->toHaveCount(1);
});

it('refuses a draft for a register whose session it does not carry and opens no session there', function () {
    $other = PosHelper::configWithCashMethod($this->warehouse);

    $payload = posDraftPayload($this, [], ['config_id' => $other->id, 'is_booked' => true]);

    $result = PointOfSale::saveDraftOrders([$payload]);

    expect($result['errors'])->toHaveCount(1)
        ->and($result['errors'][0]['message'])->toBe(__('point-of-sale::system.order-processor.foreign-order'))
        ->and(Order::where('uuid', $payload['uuid'])->exists())->toBeFalse()
        ->and(PointOfSale::liveSessionFor($other))->toBeNull();
});
