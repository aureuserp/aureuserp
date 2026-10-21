<?php

use Illuminate\Support\Str;
use Webkul\PointOfSale\Enums\OrderState;
use Webkul\PointOfSale\Models\Order;

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

it('syncs a batch of orders over the api', function () {
    $response = $this->postJson(route('admin.api.v1.point-of-sale.orders.sync'), [
        'orders' => [
            PosHelper::orderPayload(
                $this->config,
                $this->session,
                [PosHelper::line($this->product->id, 1, 50.0)],
                [PosHelper::payment($this->cash, 50.0)],
            ),
        ],
    ]);

    $response->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.state', OrderState::PAID->value);
});

it('is idempotent on the client uuid over the api', function () {
    $payload = PosHelper::orderPayload(
        $this->config,
        $this->session,
        [PosHelper::line($this->product->id, 1, 50.0)],
        [PosHelper::payment($this->cash, 50.0)],
    );

    $this->postJson(route('admin.api.v1.point-of-sale.orders.sync'), ['orders' => [$payload]])->assertOk();
    $this->postJson(route('admin.api.v1.point-of-sale.orders.sync'), ['orders' => [$payload]])->assertOk();

    expect(Order::query()->where('uuid', $payload['uuid'])->count())->toBe(1);
});

it('reports a failing order in the errors bag and commits the rest', function () {
    $good = PosHelper::orderPayload(
        $this->config,
        $this->session,
        [PosHelper::line($this->product->id, 1, 50.0)],
        [PosHelper::payment($this->cash, 50.0)],
    );

    $bad = PosHelper::orderPayload(
        $this->config,
        $this->session,
        [PosHelper::line($this->product->id, 2, 50.0)],
        [PosHelper::payment($this->cash, 10.0)],
    );

    $this->postJson(route('admin.api.v1.point-of-sale.orders.sync'), ['orders' => [$good, $bad]])
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonCount(1, 'errors');
});

it('rejects a payload without lines', function () {
    $this->postJson(route('admin.api.v1.point-of-sale.orders.sync'), [
        'orders' => [[
            'uuid'      => (string) Str::uuid(),
            'config_id' => $this->config->id,
            'lines'     => [],
            'payments'  => [],
        ]],
    ])->assertStatus(422);
});

it('explains a missing product in words the cashier understands', function () {
    $payload = PosHelper::orderPayload(
        $this->config,
        $this->session,
        [PosHelper::line(999999, 1, 50.0)],
        [PosHelper::payment($this->cash, 50.0)],
    );

    $this->postJson(route('admin.api.v1.point-of-sale.orders.sync'), ['orders' => [$payload]])
        ->assertStatus(422)
        ->assertJsonPath('errors', fn (array $errors): bool => collect($errors)->flatten()->contains(__('point-of-sale::system.order-sync.product-missing')));
});

it('returns the current session for a terminal', function () {
    $this->getJson(route('admin.api.v1.point-of-sale.configs.session', ['config' => $this->config->id]))
        ->assertOk()
        ->assertJsonPath('data.id', $this->session->id);
});

it('loads the terminal catalogue', function () {
    $this->getJson(route('admin.api.v1.point-of-sale.configs.catalog', ['config' => $this->config->id]))
        ->assertOk()
        ->assertJsonPath('data.config.id', $this->config->id)
        ->assertJsonStructure(['data' => ['config', 'categories', 'products', 'payment_methods', 'bills']]);
});

it('records a cash movement over the api', function () {
    $this->postJson(route('admin.api.v1.point-of-sale.sessions.cash-movements', ['session' => $this->session->id]), [
        'type'   => 'in',
        'amount' => 75.0,
        'reason' => 'Float top up',
    ])->assertOk();

    expect((float) $this->session->refresh()->cashMovements->sum('amount'))->toBe(75.0);
});
