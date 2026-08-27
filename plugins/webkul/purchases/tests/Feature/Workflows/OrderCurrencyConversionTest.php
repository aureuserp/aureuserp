<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Webkul\Partner\Models\Partner;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Package;
use Webkul\Product\Models\ProductSupplier;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\OrderResource\Schemas\OrderForm;
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
