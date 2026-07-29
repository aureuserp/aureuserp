<?php

use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\MoveType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Package;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../Helpers/AccountHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('accounts');

    DB::table('plugins')->updateOrInsert(
        ['name' => 'accounts'],
        ['is_installed' => true, 'is_active' => true, 'updated_at' => now()],
    );

    Package::$plugins = Plugin::all()->keyBy('name');

    URL::resolveMissingNamedRoutesUsing(fn () => '#');

    AccountHelper::actingAsAdmin();

    $this->income = AccountHelper::account('income');
    $this->expense = AccountHelper::account('expense');
    $this->partner = AccountHelper::partner();
});

it('cancels a draft invoice', function () {
    $invoice = AccountHelper::invoice(MoveType::OUT_INVOICE, $this->partner);
    AccountHelper::productLine($invoice, $this->income, qty: 2, priceUnit: 100);

    AccountHelper::cancel($invoice);

    expect($invoice->refresh()->state)->toBe(MoveState::CANCEL);
});

it('resets a posted invoice back to draft', function () {
    $invoice = AccountHelper::invoice(MoveType::OUT_INVOICE, $this->partner);
    AccountHelper::productLine($invoice, $this->income, qty: 2, priceUnit: 100);

    AccountHelper::post($invoice);
    AccountHelper::resetToDraft($invoice);

    expect($invoice->refresh()->state)->toBe(MoveState::DRAFT);
});

it('refuses to post a customer invoice without a partner', function () {
    $invoice = AccountHelper::invoice(MoveType::OUT_INVOICE, $this->partner, null, ['partner_id' => null]);
    AccountHelper::productLine($invoice, $this->income, qty: 2, priceUnit: 100);

    expect(fn () => AccountHelper::post($invoice))
        ->toThrow(Exception::class, __('accounts::account-manager.post-action-validate.customer-required'));
});

it('refuses to post a vendor bill without an invoice date', function () {
    $bill = AccountHelper::invoice(MoveType::IN_INVOICE, $this->partner, null, ['invoice_date' => null]);
    AccountHelper::productLine($bill, $this->expense, qty: 2, priceUnit: 100);

    expect(fn () => AccountHelper::post($bill))
        ->toThrow(Exception::class, __('accounts::account-manager.post-action-validate.date-required'));
});

it('refuses to post an invoice with no lines', function () {
    $invoice = AccountHelper::invoice(MoveType::OUT_INVOICE, $this->partner);

    expect(fn () => AccountHelper::post($invoice))
        ->toThrow(Exception::class, __('accounts::account-manager.post-action-validate.lines-required'));
});

it('refuses to post an already posted invoice', function () {
    $invoice = AccountHelper::invoice(MoveType::OUT_INVOICE, $this->partner);
    AccountHelper::productLine($invoice, $this->income, qty: 2, priceUnit: 100);

    AccountHelper::post($invoice);

    expect(fn () => AccountHelper::post($invoice))
        ->toThrow(Exception::class, __('accounts::account-manager.post-action-validate.draft-state-required'));
});

it('marks a posted invoice as checked', function () {
    $invoice = AccountHelper::invoice(MoveType::OUT_INVOICE, $this->partner);
    AccountHelper::productLine($invoice, $this->income, qty: 2, priceUnit: 100);
    AccountHelper::post($invoice);

    AccountHelper::setAsChecked($invoice);

    expect($invoice->refresh()->checked)->toBeTrue()
        ->and($invoice->state)->toBe(MoveState::POSTED);
});
