<?php

use Webkul\Partner\Models\Partner as BasePartner;
use Webkul\Product\Enums\PriceRuleType;
use Webkul\Product\Models\PriceList;
use Webkul\Product\Models\PriceRuleItem;
use Webkul\Product\Models\Product;
use Webkul\Product\Services\PriceListResolver;
use Webkul\Product\Settings\ProductSettings;
use Webkul\Sale\Filament\Clusters\Products\Resources\PriceListResource;
use Webkul\Sale\Models\Partner;
use Webkul\Support\Models\Currency;

require_once __DIR__.'/../../../../support/tests/Helpers/CompanyHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../Helpers/SaleHelper.php';

beforeEach(function () {
    foreach (['products', 'sales'] as $plugin) {
        TestBootstrapHelper::ensurePluginInstalled($plugin);
    }

    SecurityHelper::disableUserEvents();
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

it('keeps the price list an order was placed against', function () {
    $priceList = PriceList::factory()->create();

    $order = SaleHelper::order(['price_list_id' => $priceList->id]);

    expect($order->refresh()->priceList?->id)->toBe($priceList->id);
});

it('leaves the price list empty on orders that do not use one', function () {
    $order = SaleHelper::order();

    expect($order->refresh()->price_list_id)->toBeNull()
        ->and($order->priceList)->toBeNull();
});

it('remembers the price list a customer buys on', function () {
    $priceList = PriceList::factory()->create();

    $partner = Partner::findOrFail(SaleHelper::partner()->id);

    $partner->update(['price_list_id' => $priceList->id]);

    expect($partner->refresh()->priceList?->id)->toBe($priceList->id);
});

it('keeps the price list off the base partner model', function () {
    expect((new BasePartner)->getFillable())->not->toContain('price_list_id')
        ->and(method_exists(BasePartner::class, 'priceList'))->toBeFalse();
});

it('prices an order line from the price list on the order', function () {
    $priceList = PriceList::factory()->create();

    $product = Product::factory()->create(['price' => 200]);

    PriceRuleItem::factory()->create([
        'price_list_id' => $priceList->id,
        'type'          => PriceRuleType::PERCENTAGE,
        'percent_price' => 25,
    ]);

    $price = app(PriceListResolver::class)->getProductPrice(
        $priceList,
        $product,
        1,
        $product->uom,
        $priceList->currency,
    );

    expect($price)->toBe(150.0);
});

it('hides the price list screen until the feature is switched on', function () {
    $settings = app(ProductSettings::class);

    $settings->enable_price_lists = false;
    $settings->save();

    expect(PriceListResource::shouldRegisterNavigation())->toBeFalse();
});

it('shows the price list screen once the feature is switched on', function () {
    $settings = app(ProductSettings::class);

    $settings->enable_price_lists = true;
    $settings->save();

    expect(PriceListResource::shouldRegisterNavigation())->toBeTrue();
});

it('takes the order currency from its price list when saved', function () {
    $otherCurrency = Currency::query()->whereKeyNot(SaleHelper::currency()->id)->first();

    $priceList = PriceList::factory()->create(['currency_id' => $otherCurrency->id]);

    $order = SaleHelper::order(['price_list_id' => $priceList->id]);

    expect($order->refresh()->currency_id)->toBe($otherCurrency->id);
});

it('follows the price list currency when the price list changes', function () {
    $otherCurrency = Currency::query()->whereKeyNot(SaleHelper::currency()->id)->first();

    $order = SaleHelper::order();

    $priceList = PriceList::factory()->create(['currency_id' => $otherCurrency->id]);

    $order->update(['price_list_id' => $priceList->id]);

    expect($order->refresh()->currency_id)->toBe($otherCurrency->id);
});

it('keeps a currency on an order that has no price list', function () {
    $order = SaleHelper::order(['price_list_id' => null]);

    expect($order->refresh()->currency_id)->not->toBeNull();
});

it('prices a sale from the product sales price, not its vendor price', function () {
    $product = Product::factory()->create(['price' => 200, 'cost' => 60]);

    $price = app(PriceListResolver::class)->getProductPrice(null, $product);

    expect($price)->toBe(200.0);
});

it('prices a sale from the price list rather than the product sales price when one applies', function () {
    $priceList = PriceList::factory()->create();

    $product = Product::factory()->create(['price' => 200]);

    PriceRuleItem::factory()->create([
        'price_list_id' => $priceList->id,
        'type'          => PriceRuleType::FIXED,
        'fixed_price'   => 75,
    ]);

    $price = app(PriceListResolver::class)->getProductPrice($priceList, $product);

    expect($price)->toBe(75.0);
});
