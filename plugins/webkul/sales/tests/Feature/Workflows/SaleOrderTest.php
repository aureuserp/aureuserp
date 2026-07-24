<?php

use Webkul\Account\Enums\AmountType;
use Webkul\Account\Enums\TaxIncludeOverride;
use Webkul\Sale\Enums\InvoiceStatus;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../Helpers/SaleHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('sales');

    Illuminate\Support\Facades\DB::table('plugins')->updateOrInsert(
        ['name' => 'sales'],
        ['is_installed' => true, 'is_active' => true, 'updated_at' => now()],
    );

    Webkul\PluginManager\Package::$plugins = Webkul\PluginManager\Models\Plugin::all()->keyBy('name');

    SaleHelper::actingAsAdmin();

    $this->product = SaleHelper::product();
});

it('computes a line subtotal from unit price and quantity with no tax or discount', function () {
    $order = SaleHelper::order();
    $line = SaleHelper::line($order, $this->product, qty: 2, priceUnit: 100);

    SaleHelper::compute($order);

    $line->refresh();

    expect((float) $line->price_subtotal)->toBe(200.0)
        ->and((float) $line->price_tax)->toBe(0.0)
        ->and((float) $line->price_total)->toBe(200.0);
});

it('adds an exclusive percent tax on top of the subtotal', function () {
    $order = SaleHelper::order();
    $tax = SaleHelper::tax(10, AmountType::PERCENT, TaxIncludeOverride::TAX_EXCLUDED);
    $line = SaleHelper::line($order, $this->product, qty: 2, priceUnit: 100, taxes: [$tax]);

    SaleHelper::compute($order);

    $line->refresh();

    expect((float) $line->price_subtotal)->toBe(200.0)
        ->and((float) $line->price_tax)->toBe(20.0)
        ->and((float) $line->price_total)->toBe(220.0);
});

it('strips an inclusive percent tax out of the unit price', function () {
    $order = SaleHelper::order();
    $tax = SaleHelper::tax(10, AmountType::PERCENT, TaxIncludeOverride::TAX_INCLUDED);
    $line = SaleHelper::line($order, $this->product, qty: 1, priceUnit: 110, taxes: [$tax]);

    SaleHelper::compute($order);

    $line->refresh();

    expect((float) $line->price_subtotal)->toBe(100.0)
        ->and((float) $line->price_tax)->toBe(10.0)
        ->and((float) $line->price_total)->toBe(110.0);
});

it('applies a percent discount to the subtotal before tax', function () {
    $order = SaleHelper::order();
    $line = SaleHelper::line($order, $this->product, qty: 2, priceUnit: 100, discount: 10);

    SaleHelper::compute($order);

    $line->refresh();

    expect((float) $line->price_subtotal)->toBe(180.0)
        ->and((float) $line->price_total)->toBe(180.0);
});

it('applies the discount before an exclusive tax', function () {
    $order = SaleHelper::order();
    $tax = SaleHelper::tax(15, AmountType::PERCENT, TaxIncludeOverride::TAX_EXCLUDED);
    $line = SaleHelper::line($order, $this->product, qty: 2, priceUnit: 100, discount: 10, taxes: [$tax]);

    SaleHelper::compute($order);

    $line->refresh();

    expect((float) $line->price_subtotal)->toBe(180.0)
        ->and((float) $line->price_tax)->toBe(27.0)
        ->and((float) $line->price_total)->toBe(207.0);
});

it('sums two independent exclusive percent taxes on the same base', function () {
    $order = SaleHelper::order();
    $ten = SaleHelper::tax(10, AmountType::PERCENT, TaxIncludeOverride::TAX_EXCLUDED);
    $five = SaleHelper::tax(5, AmountType::PERCENT, TaxIncludeOverride::TAX_EXCLUDED);
    $line = SaleHelper::line($order, $this->product, qty: 2, priceUnit: 100, taxes: [$ten, $five]);

    SaleHelper::compute($order);

    $line->refresh();

    expect((float) $line->price_subtotal)->toBe(200.0)
        ->and((float) $line->price_tax)->toBe(30.0)
        ->and((float) $line->price_total)->toBe(230.0);
});

it('multiplies a fixed tax by the quantity', function () {
    $order = SaleHelper::order();
    $tax = SaleHelper::tax(5, AmountType::FIXED, TaxIncludeOverride::TAX_EXCLUDED);
    $line = SaleHelper::line($order, $this->product, qty: 2, priceUnit: 100, taxes: [$tax]);

    SaleHelper::compute($order);

    $line->refresh();

    expect((float) $line->price_subtotal)->toBe(200.0)
        ->and((float) $line->price_tax)->toBe(10.0)
        ->and((float) $line->price_total)->toBe(210.0);
});

it('aggregates line totals into the order header amounts', function () {
    $order = SaleHelper::order();
    $tax = SaleHelper::tax(10, AmountType::PERCENT, TaxIncludeOverride::TAX_EXCLUDED);

    SaleHelper::line($order, $this->product, qty: 2, priceUnit: 100);
    SaleHelper::line($order, SaleHelper::product(), qty: 1, priceUnit: 50, taxes: [$tax]);

    SaleHelper::compute($order);

    $order->refresh();

    expect((float) $order->amount_untaxed)->toBe(250.0)
        ->and((float) $order->amount_tax)->toBe(5.0)
        ->and((float) $order->amount_total)->toBe(255.0);
});

it('keeps the line subtotal rounded to the stored precision', function () {
    $order = SaleHelper::order();
    $line = SaleHelper::line($order, $this->product, qty: 3, priceUnit: 33.33);

    SaleHelper::compute($order);

    $line->refresh();

    expect((float) $line->price_subtotal)->toBe(99.99);
});

it('cascades a base-affected tax on top of a base-including tax', function () {
    $order = SaleHelper::order();

    $base = SaleHelper::tax(10, AmountType::PERCENT, TaxIncludeOverride::TAX_EXCLUDED, includeBaseAmount: true, sort: 1);
    $onTop = SaleHelper::tax(5, AmountType::PERCENT, TaxIncludeOverride::TAX_EXCLUDED, isBaseAffected: true, sort: 2);

    $line = SaleHelper::line($order, $this->product, qty: 2, priceUnit: 100, taxes: [$base, $onTop]);

    SaleHelper::compute($order);

    $line->refresh();

    expect((float) $line->price_subtotal)->toBe(200.0)
        ->and((float) $line->price_tax)->toBe(31.0)
        ->and((float) $line->price_total)->toBe(231.0);
});

it('treats a default-include tax as exclusive because the tax setting defaults to excluded', function () {
    $order = SaleHelper::order();

    $tax = SaleHelper::tax(10, AmountType::PERCENT, TaxIncludeOverride::DEFAULT);

    $line = SaleHelper::line($order, $this->product, qty: 2, priceUnit: 100, taxes: [$tax]);

    SaleHelper::compute($order);

    $line->refresh();

    expect((float) $line->price_subtotal)->toBe(200.0)
        ->and((float) $line->price_tax)->toBe(20.0)
        ->and((float) $line->price_total)->toBe(220.0);
});

it('reports no invoice status while the order is still a draft', function () {
    $order = SaleHelper::order();
    SaleHelper::line($order, $this->product, qty: 10, priceUnit: 100);

    SaleHelper::compute($order);

    expect($order->refresh()->invoice_status)->toBe(InvoiceStatus::NO);
});
