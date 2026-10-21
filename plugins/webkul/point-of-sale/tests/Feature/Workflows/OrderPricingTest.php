<?php

use Illuminate\Support\Str;
use Webkul\Account\Models\Tax;
use Webkul\PointOfSale\Facades\PointOfSale;
use Webkul\Product\Enums\PriceRuleBase;
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
    $this->cash = $this->config->paymentMethods->first();
    $this->product = InventoryHelper::product(['price' => 100.0, 'cost' => 60.0]);

    InventoryHelper::stockUp($this->product, $this->warehouse->lotStockLocation, 100);
});

it('falls back to the catalogue price without a price list', function () {
    expect(PointOfSale::resolvePrice($this->product))->toBe(100.0);
});

it('applies a fixed price list item', function () {
    $priceList = PosHelper::priceList();

    PosHelper::priceListItem($priceList, $this->product, [
        'type'        => PriceRuleType::FIXED,
        'fixed_price' => 75.0,
    ]);

    expect(PointOfSale::resolvePrice($this->product, $priceList))->toBe(75.0);
});

it('applies a percentage price list item', function () {
    $priceList = PosHelper::priceList();

    PosHelper::priceListItem($priceList, $this->product, [
        'type'          => PriceRuleType::PERCENTAGE,
        'percent_price' => 20.0,
    ]);

    expect(PointOfSale::resolvePrice($this->product, $priceList))->toBe(80.0);
});

it('applies a formula discount with a surcharge', function () {
    $priceList = PosHelper::priceList();

    PosHelper::priceListItem($priceList, $this->product, [
        'type'            => PriceRuleType::FORMULA,
        'price_discount'  => 10.0,
        'price_surcharge' => 5.0,
    ]);

    expect(PointOfSale::resolvePrice($this->product, $priceList))->toBe(95.0);
});

it('bases a formula on the product cost when asked', function () {
    $priceList = PosHelper::priceList();

    PosHelper::priceListItem($priceList, $this->product, [
        'base'           => PriceRuleBase::STANDARD_PRICE,
        'type'           => PriceRuleType::FORMULA,
        'price_discount' => 0.0,
        'price_markup'   => 0.0,
    ]);

    expect(PointOfSale::resolvePrice($this->product, $priceList))->toBe(60.0);
});

it('honours the minimum quantity on a price list item', function () {
    $priceList = PosHelper::priceList();

    PosHelper::priceListItem($priceList, $this->product, [
        'type'         => PriceRuleType::FIXED,
        'fixed_price'  => 70.0,
        'min_quantity' => 5.0,
    ]);

    expect(PointOfSale::resolvePrice($this->product, $priceList, 2.0))->toBe(100.0)
        ->and(PointOfSale::resolvePrice($this->product, $priceList, 5.0))->toBe(70.0);
});

it('ignores a price list item outside its date window', function () {
    $priceList = PosHelper::priceList();

    PosHelper::priceListItem($priceList, $this->product, [
        'type'        => PriceRuleType::FIXED,
        'fixed_price' => 10.0,
        'starts_at'   => now()->addDays(3),
        'ends_at'     => now()->addDays(6),
    ]);

    expect(PointOfSale::resolvePrice($this->product, $priceList))->toBe(100.0);
});

it('prices a synced order line from the terminal price list', function () {
    $priceList = PosHelper::priceList();

    PosHelper::priceListItem($priceList, $this->product, [
        'type'        => PriceRuleType::FIXED,
        'fixed_price' => 40.0,
    ]);

    $this->config->update(['price_list_id' => $priceList->id, 'enable_price_list' => true]);

    $order = PointOfSale::syncOrder(PosHelper::orderPayload(
        $this->config->refresh(),
        $this->session,
        [['uuid' => (string) Str::uuid(), 'product_id' => $this->product->id, 'qty' => 2]],
        [PosHelper::payment($this->cash, 80.0)],
    ));

    expect((float) $order->lines()->first()->price_unit)->toBe(40.0)
        ->and((float) $order->amount_total)->toBe(80.0);
});

it('keeps a manually typed price over the price list', function () {
    $priceList = PosHelper::priceList();

    PosHelper::priceListItem($priceList, $this->product, [
        'type'        => PriceRuleType::FIXED,
        'fixed_price' => 40.0,
    ]);

    $this->config->update(['price_list_id' => $priceList->id, 'enable_price_list' => true]);

    $order = PointOfSale::syncOrder(PosHelper::orderPayload(
        $this->config->refresh(),
        $this->session,
        [PosHelper::line($this->product->id, 1, 95.0)],
        [PosHelper::payment($this->cash, 95.0)],
    ));

    expect((float) $order->lines()->first()->price_unit)->toBe(95.0);
});

it('applies product taxes to the synced line totals', function () {
    $tax = Tax::factory()->create([
        'name'       => 'GST 10',
        'amount'     => 10,
        'company_id' => PosHelper::company()->id,
    ]);

    $order = PointOfSale::syncOrder(PosHelper::orderPayload(
        $this->config,
        $this->session,
        [PosHelper::line($this->product->id, 1, 100.0, ['tax_ids' => [$tax->id]])],
        [PosHelper::payment($this->cash, 110.0)],
    ));

    expect((float) $order->amount_untaxed)->toBe(100.0)
        ->and((float) $order->amount_tax)->toBe(10.0)
        ->and((float) $order->amount_total)->toBe(110.0);
});
