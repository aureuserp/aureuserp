<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\MoveType;
use Webkul\Inventory\Facades\Inventory;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Package;
use Webkul\Purchase\Enums\OrderInvoiceStatus;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../inventories/tests/Helpers/InventoryHelper.php';
require_once __DIR__.'/../../Helpers/PurchaseHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('inventories');
    TestBootstrapHelper::ensurePluginInstalled('purchases');
    TestBootstrapHelper::ensurePluginInstalled('accounts');

    foreach (['inventories', 'purchases', 'accounts'] as $plugin) {
        DB::table('plugins')->updateOrInsert(
            ['name' => $plugin],
            ['is_installed' => true, 'is_active' => true, 'updated_at' => now()],
        );
    }

    Package::$plugins = Plugin::all()->keyBy('name');

    URL::resolveMissingNamedRoutesUsing(fn () => '#');

    PurchaseHelper::actingAsAdmin();

    $this->warehouse = InventoryHelper::warehouse();
    $this->product = PurchaseHelper::product();
});

it('marks a received purchase order as to invoice', function () {
    $order = PurchaseHelper::confirmedOrder($this->warehouse, $this->product, qty: 5);
    PurchaseHelper::receiveChain($order);

    expect($order->refresh()->invoice_status)->toBe(OrderInvoiceStatus::TO_INVOICED);
});

it('marks the order invoiced once a bill is created and posted', function () {
    $order = PurchaseHelper::confirmedOrder($this->warehouse, $this->product, qty: 5);
    PurchaseHelper::receiveChain($order);
    $line = $order->refresh()->lines->first();

    $bill = PurchaseHelper::createBill($order);
    PurchaseHelper::postBill($bill);

    expect($order->refresh()->invoice_status)->toBe(OrderInvoiceStatus::INVOICED)
        ->and((float) $line->refresh()->qty_invoiced)->toBe(5.0);
});

it('returns the order to invoice when its bill is cancelled', function () {
    $order = PurchaseHelper::confirmedOrder($this->warehouse, $this->product, qty: 5);
    PurchaseHelper::receiveChain($order);
    $line = $order->refresh()->lines->first();
    $bill = PurchaseHelper::createBill($order);
    PurchaseHelper::postBill($bill);

    PurchaseHelper::cancelBill($bill);

    expect($bill->refresh()->state)->toBe(MoveState::CANCEL)
        ->and((float) $line->refresh()->qty_invoiced)->toBe(0.0)
        ->and($order->refresh()->invoice_status)->toBe(OrderInvoiceStatus::TO_INVOICED);
});

it('re-bills the order when a cancelled bill is reset to draft', function () {
    $order = PurchaseHelper::confirmedOrder($this->warehouse, $this->product, qty: 5);
    PurchaseHelper::receiveChain($order);
    $line = $order->refresh()->lines->first();
    $bill = PurchaseHelper::createBill($order);
    PurchaseHelper::postBill($bill);
    PurchaseHelper::cancelBill($bill);

    PurchaseHelper::resetBillToDraft($bill);

    expect((float) $line->refresh()->qty_invoiced)->toBe(5.0)
        ->and($order->refresh()->invoice_status)->toBe(OrderInvoiceStatus::INVOICED);
});

it('nets the billed quantity down when the bill is reversed into a refund', function () {
    $order = PurchaseHelper::confirmedOrder($this->warehouse, $this->product, qty: 5);
    PurchaseHelper::receiveChain($order);
    $line = $order->refresh()->lines->first();
    $bill = PurchaseHelper::createBill($order);
    PurchaseHelper::postBill($bill);

    $refund = PurchaseHelper::reverseBill($bill);
    PurchaseHelper::postBill($refund);

    expect($refund->refresh()->move_type)->toBe(MoveType::IN_REFUND)
        ->and((float) $line->refresh()->qty_invoiced)->toBe(0.0)
        ->and($order->refresh()->invoice_status)->toBe(OrderInvoiceStatus::TO_INVOICED);
});

it('nets the billed quantity down when a received quantity is returned and refunded', function () {
    $order = PurchaseHelper::confirmedOrder($this->warehouse, $this->product, qty: 10);
    PurchaseHelper::receiveChain($order);
    $line = $order->refresh()->lines->first();

    $bill = PurchaseHelper::createBill($order);
    PurchaseHelper::postBill($bill);

    $receipt = $order->refresh()->operations->first();
    $receiptMove = $receipt->moves->first();
    $return = Inventory::returnTransfer($receipt, [$receiptMove->id => 4]);
    Inventory::doneTransfer($return->refresh());

    expect($order->refresh()->invoice_status)->toBe(OrderInvoiceStatus::TO_INVOICED);

    $refund = PurchaseHelper::createBill($order);
    PurchaseHelper::postBill($refund);

    expect($refund->refresh()->move_type)->toBe(MoveType::IN_REFUND)
        ->and((float) $line->refresh()->qty_invoiced)->toBe(6.0)
        ->and($order->refresh()->invoice_status)->toBe(OrderInvoiceStatus::INVOICED);
});

it('assigns a bill number when creating a bill from a purchase order', function () {
    $order = PurchaseHelper::confirmedOrder($this->warehouse, $this->product, qty: 5);
    PurchaseHelper::receiveChain($order);

    $bill = PurchaseHelper::createBill($order);

    expect($bill->refresh()->name)->not->toBeNull()
        ->and($bill->name)->not->toBe('');
});
