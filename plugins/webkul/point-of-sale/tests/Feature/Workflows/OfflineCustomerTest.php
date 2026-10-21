<?php

use Illuminate\Support\Str;
use Webkul\Partner\Enums\AccountType;
use Webkul\Partner\Models\Partner;
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
    $this->product = InventoryHelper::product(['price' => 50.0, 'cost' => 20.0]);

    InventoryHelper::stockUp($this->product, $this->warehouse->lotStockLocation, 20);
});

function offlineOrderPayload(array $partner, $config, $session, $product, $cash, float $total = 50.0): array
{
    return [
        'uuid'       => Str::uuid()->toString(),
        'config_id'  => $config->id,
        'session_id' => $session->id,
        'partner'    => $partner,
        'lines'      => [[
            'uuid'       => Str::uuid()->toString(),
            'product_id' => $product->id,
            'qty'        => 1,
            'price_unit' => $total,
        ]],
        'payments'   => [[
            'uuid'              => Str::uuid()->toString(),
            'payment_method_id' => $cash->id,
            'amount'            => $total,
        ]],
    ];
}

it('creates a customer captured while the terminal was offline', function () {
    $before = Partner::query()->count();

    $order = PointOfSale::syncOrder(offlineOrderPayload(
        ['name' => 'Offline Buyer', 'phone' => '0700111222'],
        $this->config,
        $this->session,
        $this->product,
        $this->cash,
    ));

    expect(Partner::query()->count())->toBe($before + 1);

    expect($order->partner_id)->not->toBeNull();

    expect($order->partner->name)->toBe('Offline Buyer');

    expect($order->partner->company_id)->toBe($this->config->company_id);
});

it('reuses an existing customer instead of duplicating them', function () {
    $existing = Partner::create([
        'account_type' => AccountType::INDIVIDUAL,
        'sub_type'     => 'customer',
        'name'         => 'Known Buyer',
        'email'        => 'known@example.test',
        'company_id'   => $this->config->company_id,
    ]);

    $before = Partner::query()->count();

    $order = PointOfSale::syncOrder(offlineOrderPayload(
        ['name' => 'Known Buyer Typo', 'email' => 'known@example.test'],
        $this->config,
        $this->session,
        $this->product,
        $this->cash,
    ));

    expect(Partner::query()->count())->toBe($before);

    expect($order->partner_id)->toBe($existing->id);
});

it('leaves the order without a customer when no draft is sent', function () {
    $order = PointOfSale::syncOrder(offlineOrderPayload(
        [],
        $this->config,
        $this->session,
        $this->product,
        $this->cash,
    ));

    expect($order->partner_id)->toBeNull();
});

it('prefers an explicit partner id over a draft', function () {
    $partner = Partner::create([
        'account_type' => AccountType::INDIVIDUAL,
        'sub_type'     => 'customer',
        'name'         => 'Picked At Till',
        'company_id'   => $this->config->company_id,
    ]);

    $payload = offlineOrderPayload(
        ['name' => 'Should Be Ignored'],
        $this->config,
        $this->session,
        $this->product,
        $this->cash,
    );

    $payload['partner_id'] = $partner->id;

    $order = PointOfSale::syncOrder($payload);

    expect($order->partner_id)->toBe($partner->id);
});
