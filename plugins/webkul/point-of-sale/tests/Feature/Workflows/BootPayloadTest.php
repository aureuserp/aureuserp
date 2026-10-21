<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Webkul\Account\Models\FiscalPosition;
use Webkul\PointOfSale\Models\Order;
use Webkul\PointOfSale\Services\BootLoader;
use Webkul\Product\Enums\PriceRuleType;

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
    $this->tax = PosHelper::taxWithAccounts(15.0);
    $this->product = InventoryHelper::product(['price' => 29.0, 'cost' => 12.0]);

    $this->product->update(['available_in_pos' => true]);

    InventoryHelper::stockUp($this->product, $this->warehouse->lotStockLocation, 40);
});

it('ships everything the terminal needs to compute a total without the server', function () {
    $payload = app(BootLoader::class)->load($this->config, $this->session);

    expect($payload)->toHaveKeys([
        'company', 'config', 'session', 'currencies', 'uoms', 'categories', 'products',
        'taxes', 'fiscal_positions', 'price_lists', 'prices', 'payment_methods',
        'bills', 'notes', 'partners', 'orders', 'stock',
    ]);

    expect($payload['company'])->toHaveKey('tax_calculation_rounding_method');

    expect($payload['currencies'])->not->toBeEmpty();

    expect($payload['currencies'][0])->toHaveKeys(['rounding', 'decimal_places']);
});

it('ships tax definitions rather than only tax ids', function () {
    $payload = app(BootLoader::class)->load($this->config, $this->session);

    $tax = collect($payload['taxes'])->firstWhere('id', $this->tax->id);

    expect($tax)->not->toBeNull()
        ->and($tax)->toHaveKeys([
            'amount', 'amount_type', 'price_include', 'include_base_amount',
            'is_base_affected', 'has_negative_factor', 'children_tax_ids',
        ])
        ->and($tax['amount'])->toBe(15.0)
        ->and($tax['amount_type'])->toBe('percent');
});

it('resolves a price for every product it ships', function () {
    $payload = app(BootLoader::class)->load($this->config, $this->session);

    expect($payload['products'])->not->toBeEmpty();

    foreach ($payload['products'] as $product) {
        expect($payload['prices']['0'])->toHaveKey((string) $product['id']);
    }
});

it('ships quantity break prices so the terminal reprices a line as its quantity grows', function () {
    $priceList = PosHelper::priceList();

    PosHelper::priceListItem($priceList, $this->product, [
        'type'         => PriceRuleType::FIXED,
        'fixed_price'  => 20.0,
        'min_quantity' => 5.0,
    ]);

    $this->config->update(['price_list_id' => $priceList->id]);

    $payload = app(BootLoader::class)->load($this->config->refresh(), $this->session);

    $tiers = $payload['price_tiers'][(string) $priceList->id][(string) $this->product->id] ?? [];

    expect($payload['prices'][(string) $priceList->id][(string) $this->product->id])->toBe(29.0)
        ->and($tiers)->toHaveCount(1)
        ->and($tiers[0]['min_qty'])->toBe(5.0)
        ->and($tiers[0]['price'])->toBe(20.0);
});

it('bounds the partner list so a large customer base cannot stall the boot', function () {
    $payload = app(BootLoader::class)->load($this->config, $this->session);

    expect(count($payload['partners']))->toBeLessThanOrEqual(BootLoader::PARTNER_LIMIT);
});

it('snapshots free quantity per product', function () {
    $payload = app(BootLoader::class)->load($this->config, $this->session);

    expect($payload['stock'])->toBeArray();

    expect((float) ($payload['stock'][$this->product->id] ?? 0))->toBe(40.0);
});

it('allows price editing when the register does not restrict it', function () {
    $this->config->forceFill(['enable_price_control' => false])->save();

    $payload = app(BootLoader::class)->load($this->config->refresh(), $this->session);

    expect($payload['config']['can_edit_price'])->toBeTrue();
});

it('allows a manager to edit prices on a restricted register', function () {
    $this->config->forceFill(['enable_price_control' => true])->save();

    $payload = app(BootLoader::class)->load($this->config->refresh(), $this->session);

    expect(Auth::user()->can('update', $this->config))->toBeTrue()
        ->and($payload['config']['can_edit_price'])->toBeTrue();
});

it('refuses price editing on a restricted register for a cashier who cannot manage it', function () {
    $this->config->forceFill(['enable_price_control' => true])->save();

    Gate::before(fn (): ?bool => false);

    $payload = app(BootLoader::class)->load($this->config->refresh(), $this->session);

    expect($payload['config']['can_edit_price'])->toBeFalse();
});

it('ships only the products of the picked categories when they are restricted', function () {
    $drinks = PosHelper::posCategory(['name' => 'Drinks']);
    $backOffice = PosHelper::posCategory(['name' => 'Back Office Only']);

    $coffee = InventoryHelper::product(['name' => 'Counter Coffee', 'price' => 3.0]);

    $coffee->update(['available_in_pos' => true]);

    $coffee->posCategories()->sync([$drinks->id]);
    $this->product->posCategories()->sync([$backOffice->id]);

    $this->config->update(['limit_categories' => true]);

    $this->config->categories()->sync([$drinks->id]);

    $payload = app(BootLoader::class)->load($this->config->refresh(), $this->session);

    $ids = collect($payload['products'])->pluck('id');

    expect($ids)->toContain($coffee->id)
        ->and($ids)->not->toContain($this->product->id);
});

it('ships no floors outside restaurant mode', function () {
    $payload = app(BootLoader::class)->load($this->config, $this->session);

    expect($payload['floors'])->toBe([]);
});

it('ships the floor plan of a restaurant terminal', function () {
    $floor = PosHelper::floor(['name' => 'Patio']);

    $table = PosHelper::table($floor, ['table_number' => '7', 'position_h' => 120, 'position_v' => 40, 'seats' => 6]);

    $this->config->floors()->attach($floor->id);

    $this->config->update(['is_restaurant' => true]);

    $payload = app(BootLoader::class)->load($this->config->refresh(), $this->session);

    $shipped = collect($payload['floors'])->firstWhere('id', $floor->id);

    expect($shipped['name'])->toBe('Patio')
        ->and($shipped['tables'])->toHaveCount(1)
        ->and($shipped['tables'][0])->toMatchArray([
            'id'           => $table->id,
            'floor_id'     => $floor->id,
            'table_number' => '7',
            'position_h'   => 120.0,
            'position_v'   => 40.0,
            'seats'        => 6,
        ]);
});

it('ships the table and guest count of an open order', function () {
    $floor = PosHelper::floor();

    $table = PosHelper::table($floor);

    $order = Order::create([
        'session_id'     => $this->session->id,
        'config_id'      => $this->config->id,
        'table_id'       => $table->id,
        'customer_count' => 4,
    ]);

    $payload = app(BootLoader::class)->load($this->config, $this->session);

    $shipped = collect($payload['orders'])->firstWhere('uuid', $order->uuid);

    expect($shipped['table_id'])->toBe($table->id)
        ->and($shipped['customer_count'])->toBe(4);
});

it('ships the tip product hidden from the catalogue', function () {
    $tip = InventoryHelper::product(['name' => 'Tip', 'price' => 0.0]);

    $this->config->update([
        'enable_tip'     => true,
        'tip_product_id' => $tip->id,
    ]);

    $payload = app(BootLoader::class)->load($this->config->refresh(), $this->session);

    $products = collect($payload['products']);

    expect($products->firstWhere('id', $tip->id)['is_hidden'])->toBeTrue()
        ->and($products->firstWhere('id', $this->product->id)['is_hidden'])->toBeFalse();
});

it('ships no tip product while tips are off', function () {
    $tip = InventoryHelper::product(['name' => 'Tip', 'price' => 0.0]);

    $this->config->update([
        'enable_tip'     => false,
        'tip_product_id' => $tip->id,
    ]);

    $payload = app(BootLoader::class)->load($this->config->refresh(), $this->session);

    expect(collect($payload['products'])->firstWhere('id', $tip->id))->toBeNull();
});

it('ships payment methods in their sort order', function () {
    $late = PosHelper::bankMethod(['name' => 'Late']);

    $early = PosHelper::bankMethod(['name' => 'Early']);

    $late->update(['sort' => 20]);

    $early->update(['sort' => 1]);

    $this->config->paymentMethods()->sync([$late->id, $early->id]);

    $payload = app(BootLoader::class)->load($this->config->refresh(), $this->session);

    expect(collect($payload['payment_methods'])->pluck('id')->all())->toBe([$early->id, $late->id]);
});

it('ships the discount product hidden with the default percentage', function () {
    $discount = InventoryHelper::product(['name' => 'Discount', 'price' => 0.0]);

    $this->config->update([
        'enable_global_discount'     => true,
        'discount_product_id'        => $discount->id,
        'global_discount_percentage' => 15,
    ]);

    $payload = app(BootLoader::class)->load($this->config->refresh(), $this->session);

    expect(collect($payload['products'])->firstWhere('id', $discount->id)['is_hidden'])->toBeTrue()
        ->and($payload['config']['global_discount_percentage'])->toBe(15.0);
});

it('ships no default fiscal position while flexible taxes are off', function () {
    $position = FiscalPosition::create([
        'name'       => 'Export',
        'company_id' => PosHelper::company()->id,
    ]);

    $this->config->forceFill([
        'enable_fiscal_position' => false,
        'fiscal_position_id'     => $position->id,
    ])->save();

    $payload = app(BootLoader::class)->load($this->config->refresh(), $this->session);

    expect($payload['config']['fiscal_position_id'])->toBeNull()
        ->and($payload['fiscal_positions'])->toBe([]);
});

it('ships the takeaway fiscal position of a restaurant without flexible taxes', function () {
    $takeaway = FiscalPosition::create([
        'name'       => 'Takeaway',
        'company_id' => PosHelper::company()->id,
    ]);

    $this->config->forceFill([
        'is_restaurant'               => true,
        'enable_takeaway'             => true,
        'enable_fiscal_position'      => false,
        'takeaway_fiscal_position_id' => $takeaway->id,
    ])->save();

    $payload = app(BootLoader::class)->load($this->config->refresh(), $this->session);

    expect(collect($payload['fiscal_positions'])->pluck('id')->all())->toContain($takeaway->id);
});
