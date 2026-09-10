<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Package;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../Helpers/SaleHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('inventories');

    TestBootstrapHelper::ensurePluginInstalled('sales');

    DB::table('plugins')->updateOrInsert(
        ['name' => 'sales'],
        ['is_installed' => true, 'is_active' => true, 'updated_at' => now()],
    );

    Package::$plugins = Plugin::all()->keyBy('name');

    SaleHelper::actingAsAdmin();

    $this->product = SaleHelper::product(['name' => 'Document Product']);
});

function renderQuotationDocument($order): string
{
    return view('sales::sales.quotation', [
        'record' => $order->refresh()->load('lines.product', 'optionalLines', 'currency', 'company', 'partner'),
        'isRtl'  => false,
    ])->render();
}

it('renders the amount of every order line', function () {
    $order = SaleHelper::order();

    SaleHelper::line($order, $this->product, qty: 3, priceUnit: 250);

    $order = SaleHelper::compute($order);

    $html = renderQuotationDocument($order);

    expect((float) $order->lines->first()->price_subtotal)->toBe(750.0)
        ->and($html)->toContain('750.00');
});

it('renders the discount percentage of a discounted line', function () {
    $order = SaleHelper::order();

    SaleHelper::line($order, $this->product, qty: 2, priceUnit: 100, discount: 25);

    $order = SaleHelper::compute($order);

    $html = renderQuotationDocument($order);

    expect($html)->toContain('25%');
});

it('keeps the discount on the line and out of the totals block', function () {
    $order = SaleHelper::order();

    SaleHelper::line($order, $this->product, qty: 2, priceUnit: 100, discount: 25);

    $order = SaleHelper::compute($order);

    $html = renderQuotationDocument($order);

    $summary = Str::between($html, '<div class="summary">', '</div>');

    expect($html)->toContain('25%')
        ->and($summary)->toContain(__('sales::app.documents.subtotal'))
        ->and($summary)->not->toContain('-$');
});

it('renders the terms and conditions of the order', function () {
    $order = SaleHelper::order(['note' => '<p>Payment due within 15 days.</p>']);

    SaleHelper::line($order, $this->product, qty: 1, priceUnit: 100);

    $order = SaleHelper::compute($order);

    expect(renderQuotationDocument($order))->toContain('Payment due within 15 days.');
});

it('omits the terms and conditions block when the note is empty markup', function () {
    $order = SaleHelper::order(['note' => '<p></p>']);

    SaleHelper::line($order, $this->product, qty: 1, priceUnit: 100);

    $order = SaleHelper::compute($order);

    expect(renderQuotationDocument($order))->not->toContain(__('sales::app.documents.terms-and-conditions'));
});

it('does not leak bare element styles into the surrounding panel', function () {
    $order = SaleHelper::order();

    SaleHelper::line($order, $this->product, qty: 1, priceUnit: 100);

    $html = renderQuotationDocument(SaleHelper::compute($order));

    expect($html)->not->toMatch('/^\s*body\s*\{/m');
});

it('provides a dark mode override for every light colour block', function () {
    $order = SaleHelper::order();

    SaleHelper::line($order, $this->product, qty: 1, priceUnit: 100);

    $html = renderQuotationDocument(SaleHelper::compute($order));

    foreach (['.quotation-document', '.agreement-title', '.summary', '.payment-info'] as $selector) {
        expect($html)->toContain(':is(.dark '.$selector);
    }
});
