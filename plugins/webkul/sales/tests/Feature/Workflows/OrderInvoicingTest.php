<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Webkul\Account\Enums\InvoicePolicy;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\MoveType;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Package;
use Webkul\Sale\Enums\InvoiceStatus;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../Helpers/SaleHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('inventories');
    TestBootstrapHelper::ensurePluginInstalled('sales');
    TestBootstrapHelper::ensurePluginInstalled('accounts');

    foreach (['inventories', 'sales', 'accounts'] as $plugin) {
        DB::table('plugins')->updateOrInsert(
            ['name' => $plugin],
            ['is_installed' => true, 'is_active' => true, 'updated_at' => now()],
        );
    }

    Package::$plugins = Plugin::all()->keyBy('name');

    URL::resolveMissingNamedRoutesUsing(fn () => '#');

    SaleHelper::actingAsAdmin();

    $this->product = SaleHelper::product(['invoice_policy' => InvoicePolicy::ORDER->value]);
});

it('marks a confirmed sale order as to invoice', function () {
    $order = SaleHelper::order();
    SaleHelper::line($order, $this->product, qty: 5, priceUnit: 100);

    SaleHelper::confirm($order);

    expect($order->refresh()->invoice_status)->toBe(InvoiceStatus::TO_INVOICE);
});

it('marks the order invoiced once an invoice is created for the full ordered quantity', function () {
    $order = SaleHelper::order();
    $line = SaleHelper::line($order, $this->product, qty: 5, priceUnit: 100);
    SaleHelper::confirm($order);

    SaleHelper::createInvoice($order);

    expect($order->refresh()->invoice_status)->toBe(InvoiceStatus::INVOICED)
        ->and((float) $line->refresh()->qty_invoiced)->toBe(5.0)
        ->and((float) $line->qty_to_invoice)->toBe(0.0);
});

it('returns the order to invoice when its invoice is cancelled', function () {
    $order = SaleHelper::order();
    $line = SaleHelper::line($order, $this->product, qty: 5, priceUnit: 100);
    SaleHelper::confirm($order);
    $invoice = SaleHelper::createInvoice($order);
    SaleHelper::postInvoice($invoice);

    SaleHelper::cancelInvoice($invoice);

    expect($invoice->refresh()->state)->toBe(MoveState::CANCEL)
        ->and((float) $line->refresh()->qty_invoiced)->toBe(0.0)
        ->and($order->refresh()->invoice_status)->toBe(InvoiceStatus::TO_INVOICE);
});

it('re-invoices the order when a cancelled invoice is reset to draft', function () {
    $order = SaleHelper::order();
    $line = SaleHelper::line($order, $this->product, qty: 5, priceUnit: 100);
    SaleHelper::confirm($order);
    $invoice = SaleHelper::createInvoice($order);
    SaleHelper::postInvoice($invoice);
    SaleHelper::cancelInvoice($invoice);

    SaleHelper::resetInvoiceToDraft($invoice);

    expect((float) $line->refresh()->qty_invoiced)->toBe(5.0)
        ->and($order->refresh()->invoice_status)->toBe(InvoiceStatus::INVOICED);
});

it('nets the invoiced quantity down when the invoice is reversed into a credit note', function () {
    $order = SaleHelper::order();
    $line = SaleHelper::line($order, $this->product, qty: 5, priceUnit: 100);
    SaleHelper::confirm($order);
    $invoice = SaleHelper::createInvoice($order);
    SaleHelper::postInvoice($invoice);

    $creditNote = SaleHelper::reverseInvoice($invoice);
    SaleHelper::postInvoice($creditNote);

    expect($creditNote->refresh()->move_type)->toBe(MoveType::OUT_REFUND)
        ->and((float) $line->refresh()->qty_invoiced)->toBe(0.0)
        ->and($order->refresh()->invoice_status)->toBe(InvoiceStatus::TO_INVOICE);
});

it('assigns an invoice number when creating an invoice from a sale order', function () {
    $order = SaleHelper::order();
    SaleHelper::line($order, $this->product, qty: 5, priceUnit: 100);
    SaleHelper::confirm($order);

    $invoice = SaleHelper::createInvoice($order);

    expect($invoice->refresh()->name)->not->toBeNull()
        ->and($invoice->name)->not->toBe('');
});
