<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Webkul\Partner\Models\Partner;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Package;
use Webkul\Product\Models\ProductSupplier;
use Webkul\Purchase\Enums\OrderState;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\OrderResource\Schemas\OrderForm;
use Webkul\Purchase\Models\Order;
use Webkul\Purchase\Models\OrderLine;
use Webkul\Support\Models\Currency;
use Webkul\Support\Models\CurrencyRate;
use Webkul\Support\Models\UOM;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../Helpers/PurchaseHelper.php';

beforeEach(function () {
    foreach (['inventories', 'purchases'] as $plugin) {
        TestBootstrapHelper::ensurePluginInstalled($plugin);

        DB::table('plugins')->updateOrInsert(
            ['name' => $plugin],
            ['is_installed' => true, 'is_active' => true, 'updated_at' => now()],
        );
    }

    Package::$plugins = Plugin::all()->keyBy('name');

    URL::resolveMissingNamedRoutesUsing(fn () => '#');

    PurchaseHelper::actingAsAdmin();

    $this->company = PurchaseHelper::company();

    $this->companyCurrency = Currency::factory()->create(['name' => 'PCC']);

    $this->orderCurrency = Currency::factory()->create(['name' => 'POC']);

    rateFor($this->companyCurrency, 1);

    rateFor($this->orderCurrency, 88);

    $this->company->update(['currency_id' => $this->companyCurrency->id]);

    $this->partner = PurchaseHelper::partner();

    $this->product = PurchaseHelper::product(['cost' => 100, 'price' => 250]);
});

function rateFor(Currency $currency, float $rate): CurrencyRate
{
    return CurrencyRate::factory()->create([
        'currency_id' => $currency->id,
        'company_id'  => null,
        'rate'        => $rate,
        'name'        => now()->toDateString(),
    ]);
}

function unitPriceFor(array $state): float
{
    $method = new ReflectionMethod(OrderForm::class, 'calculateUnitPrice');

    $method->setAccessible(true);

    return (float) $method->invoke(null, fn (string $key) => $state[$key] ?? null);
}

function seller(array $overrides): ProductSupplier
{
    return ProductSupplier::factory()->create(array_merge([
        'min_qty'    => 1,
        'sort'       => 1,
        'starts_at'  => now()->subMonth(),
        'ends_at'    => now()->addYear(),
        'company_id' => PurchaseHelper::company()->id,
    ], $overrides));
}

it('keeps the product cost as is when the order currency is the company currency', function () {
    $price = unitPriceFor([
        'product_id'         => $this->product->id,
        'product_qty'        => 1,
        'uom_id'             => $this->product->uom_id,
        '../../partner_id'   => $this->partner->id,
        '../../currency_id'  => $this->companyCurrency->id,
        '../../company_id'   => $this->company->id,
    ]);

    expect($price)->toEqualWithDelta(100.0, 0.01);
});

it('converts the product cost into the order currency', function () {
    $price = unitPriceFor([
        'product_id'         => $this->product->id,
        'product_qty'        => 1,
        'uom_id'             => $this->product->uom_id,
        '../../partner_id'   => $this->partner->id,
        '../../currency_id'  => $this->orderCurrency->id,
        '../../company_id'   => $this->company->id,
    ]);

    expect($price)->toEqualWithDelta(8800.0, 0.01);
});

it('falls back to the product sales price when the cost is zero', function () {
    $this->product->update(['cost' => 0]);

    $price = unitPriceFor([
        'product_id'         => $this->product->id,
        'product_qty'        => 1,
        'uom_id'             => $this->product->uom_id,
        '../../partner_id'   => $this->partner->id,
        '../../currency_id'  => $this->companyCurrency->id,
        '../../company_id'   => $this->company->id,
    ]);

    expect($price)->toEqualWithDelta(250.0, 0.01);
});

it('converts a vendor price from the vendor currency into the order currency', function () {
    seller([
        'product_id'  => $this->product->id,
        'partner_id'  => $this->partner->id,
        'currency_id' => $this->companyCurrency->id,
        'price'       => 90,
    ]);

    $price = unitPriceFor([
        'product_id'         => $this->product->id,
        'product_qty'        => 1,
        'uom_id'             => $this->product->uom_id,
        '../../partner_id'   => $this->partner->id,
        '../../currency_id'  => $this->orderCurrency->id,
        '../../company_id'   => $this->company->id,
    ]);

    expect($price)->toEqualWithDelta(7920.0, 0.01);
});

it('uses a vendor price stated in another currency instead of falling back to the product cost', function () {
    seller([
        'product_id'  => $this->product->id,
        'partner_id'  => $this->partner->id,
        'currency_id' => $this->companyCurrency->id,
        'price'       => 90,
    ]);

    $price = unitPriceFor([
        'product_id'         => $this->product->id,
        'product_qty'        => 1,
        'uom_id'             => $this->product->uom_id,
        '../../partner_id'   => $this->partner->id,
        '../../currency_id'  => $this->orderCurrency->id,
        '../../company_id'   => $this->company->id,
    ]);

    expect($price)->not->toEqualWithDelta(8800.0, 0.01);
});

it('leaves a vendor price already stated in the order currency untouched', function () {
    seller([
        'product_id'  => $this->product->id,
        'partner_id'  => $this->partner->id,
        'currency_id' => $this->orderCurrency->id,
        'price'       => 7000,
    ]);

    $price = unitPriceFor([
        'product_id'         => $this->product->id,
        'product_qty'        => 1,
        'uom_id'             => $this->product->uom_id,
        '../../partner_id'   => $this->partner->id,
        '../../currency_id'  => $this->orderCurrency->id,
        '../../company_id'   => $this->company->id,
    ]);

    expect($price)->toEqualWithDelta(7000.0, 0.01);
});

it('ignores vendor prices belonging to another vendor', function () {
    seller([
        'product_id'  => $this->product->id,
        'partner_id'  => $this->partner->id,
        'currency_id' => $this->companyCurrency->id,
        'price'       => 90,
    ]);

    $otherVendor = Partner::factory()->create();

    $price = unitPriceFor([
        'product_id'         => $this->product->id,
        'product_qty'        => 1,
        'uom_id'             => $this->product->uom_id,
        '../../partner_id'   => $otherVendor->id,
        '../../currency_id'  => $this->orderCurrency->id,
        '../../company_id'   => $this->company->id,
    ]);

    expect($price)->toEqualWithDelta(8800.0, 0.01);
});

it('applies the uom factor on top of the currency conversion', function () {
    $dozens = UOM::query()->where('name', 'Dozens')->firstOrFail();

    $price = unitPriceFor([
        'product_id'         => $this->product->id,
        'product_qty'        => 1,
        'uom_id'             => $dozens->id,
        '../../partner_id'   => $this->partner->id,
        '../../currency_id'  => $this->orderCurrency->id,
        '../../company_id'   => $this->company->id,
    ]);

    expect($price)->toEqualWithDelta(8800.0 * 12, 1);
});

function storedAmounts(Order $order): array
{
    $order = Order::query()->with('lines')->findOrFail($order->id);

    $lines = $order->lines->sortBy('id')->values();

    return [
        'header' => [
            'subtotal' => (float) $order->untaxed_amount,
            'tax'      => (float) $order->tax_amount,
            'total'    => (float) $order->total_amount,
            'total_cc' => (float) $order->total_cc_amount,
        ],
        'lines' => $lines->map(fn ($line) => [
            'price_unit' => (float) $line->price_unit,
            'subtotal'   => (float) $line->price_subtotal,
            'tax'        => (float) $line->price_tax,
            'total'      => (float) $line->price_total,
            'total_cc'   => (float) $line->price_total_cc,
        ])->all(),
        'sums' => [
            'subtotal' => $lines->sum(fn ($line) => (float) $line->price_subtotal),
            'tax'      => $lines->sum(fn ($line) => (float) $line->price_tax),
            'total'    => $lines->sum(fn ($line) => (float) $line->price_total),
            'total_cc' => $lines->sum(fn ($line) => (float) $line->price_total_cc),
        ],
    ];
}

function expectAtCurrencyPrecision(array $amounts, Currency $currency): void
{
    foreach ($amounts as $amount) {
        expect($amount)->toEqualWithDelta($currency->round($amount), 0.000001);
    }
}

it('stores header totals equal to the sum of the stored lines and keeps them on recompute', function (int $decimalPlaces, float $rounding) {
    $currency = Currency::factory()->create([
        'decimal_places' => $decimalPlaces,
        'rounding'       => $rounding,
    ]);

    rateFor($currency, 88);

    $tax = PurchaseHelper::tax(15);

    $order = PurchaseHelper::order([
        'currency_id'   => $currency->id,
        'partner_id'    => $this->partner->id,
        'currency_rate' => 0,
    ]);

    PurchaseHelper::line($order, $this->product, qty: 3, priceUnit: 33.335, discount: 7.5, taxes: [$tax]);
    PurchaseHelper::line($order, $this->product, qty: 7, priceUnit: 12.3456, taxes: [$tax]);
    PurchaseHelper::line($order, $this->product, qty: 1, priceUnit: 0.3333, discount: 12.5);
    PurchaseHelper::line($order, $this->product, qty: 2.5, priceUnit: 1234.5678, discount: 3, taxes: [$tax]);

    PurchaseHelper::compute($order->refresh());

    $first = storedAmounts($order);

    expect((float) $order->refresh()->currency_rate)->toEqualWithDelta(88.0, 0.000001)
        ->and($first['header'])->toEqualWithDelta($first['sums'], 0.000001)
        ->and($first['header']['total'])->toEqualWithDelta($first['header']['subtotal'] + $first['header']['tax'], 0.000001);

    expectAtCurrencyPrecision([$first['header']['subtotal'], $first['header']['tax'], $first['header']['total']], $currency);

    expectAtCurrencyPrecision([$first['header']['total_cc']], $this->companyCurrency);

    foreach ($first['lines'] as $line) {
        expectAtCurrencyPrecision([$line['subtotal'], $line['tax'], $line['total']], $currency);

        expectAtCurrencyPrecision([$line['total_cc']], $this->companyCurrency);
    }

    PurchaseHelper::compute($order->refresh());

    expect(storedAmounts($order))->toEqualWithDelta($first, 0.000001);
})->with([
    'two decimals'   => [2, 0.01],
    'no decimals'    => [0, 1],
    'three decimals' => [3, 0.001],
]);

it('fills a missing currency rate from the rate on the order date', function () {
    CurrencyRate::factory()->create([
        'currency_id' => $this->orderCurrency->id,
        'company_id'  => null,
        'rate'        => 70,
        'name'        => now()->subDays(30)->toDateString(),
    ]);

    $order = PurchaseHelper::order([
        'currency_id'   => $this->orderCurrency->id,
        'partner_id'    => $this->partner->id,
        'ordered_at'    => now()->subDays(30),
        'currency_rate' => 0,
    ]);

    expect((float) $order->refresh()->currency_rate)->toEqualWithDelta(70.0, 0.000001);
});

it('refreshes the currency rate of a request for quotation when its order date changes', function (OrderState $state) {
    CurrencyRate::factory()->create([
        'currency_id' => $this->orderCurrency->id,
        'company_id'  => null,
        'rate'        => 70,
        'name'        => now()->subDays(30)->toDateString(),
    ]);

    $order = PurchaseHelper::order([
        'state'         => $state,
        'currency_id'   => $this->orderCurrency->id,
        'partner_id'    => $this->partner->id,
        'currency_rate' => 0,
    ]);

    expect((float) $order->refresh()->currency_rate)->toEqualWithDelta(88.0, 0.000001);

    Order::query()->findOrFail($order->id)->update(['ordered_at' => now()->subDays(30)]);

    expect((float) $order->refresh()->currency_rate)->toEqualWithDelta(70.0, 0.000001);
})->with([
    'draft' => [OrderState::DRAFT],
    'sent'  => [OrderState::SENT],
]);

it('keeps the currency rate and totals of a confirmed order when its order date changes', function () {
    CurrencyRate::factory()->create([
        'currency_id' => $this->orderCurrency->id,
        'company_id'  => null,
        'rate'        => 70,
        'name'        => now()->subDays(30)->toDateString(),
    ]);

    $order = PurchaseHelper::order([
        'currency_id'   => $this->orderCurrency->id,
        'partner_id'    => $this->partner->id,
        'currency_rate' => 0,
    ]);

    PurchaseHelper::line($order, $this->product, qty: 3, priceUnit: 33.335, discount: 7.5, taxes: [PurchaseHelper::tax(15)]);

    Order::query()->findOrFail($order->id)->update(['state' => OrderState::PURCHASE]);

    PurchaseHelper::compute($order->refresh());

    $confirmed = storedAmounts($order);

    Order::query()->findOrFail($order->id)->update(['ordered_at' => now()->subDays(30)]);

    expect((float) $order->refresh()->currency_rate)->toEqualWithDelta(88.0, 0.000001);

    PurchaseHelper::compute($order->refresh());

    expect(storedAmounts($order))->toEqualWithDelta($confirmed, 0.000001);
});

it('does not mark a whole unit price as changed when a confirmed order is recomputed', function () {
    $order = PurchaseHelper::order([
        'partner_id' => $this->partner->id,
    ]);

    PurchaseHelper::line($order, $this->product, qty: 2, priceUnit: 145);

    Order::query()->findOrFail($order->id)->update(['state' => OrderState::PURCHASE]);

    $changedPriceUnits = [];

    Event::listen('eloquent.updated: '.OrderLine::class, function (OrderLine $line) use (&$changedPriceUnits) {
        if ($line->wasChanged('price_unit')) {
            $changedPriceUnits[] = $line->id;
        }
    });

    PurchaseHelper::compute($order->refresh());

    expect($changedPriceUnits)->toBe([])
        ->and((float) $order->refresh()->lines->first()->price_unit)->toEqualWithDelta(145.0, 0.000001);
});
