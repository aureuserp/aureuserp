<?php

use Webkul\Account\Enums\AccountType;
use Webkul\Account\Enums\DisplayType;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Enums\PaymentState;
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

    $this->expense = AccountHelper::account('expense');
    $this->partner = AccountHelper::partner();
});

it('computes vendor bill totals from an expense line with no tax', function () {
    $bill = AccountHelper::invoice(MoveType::IN_INVOICE, $this->partner);
    AccountHelper::productLine($bill, $this->expense, qty: 2, priceUnit: 100);

    AccountHelper::compute($bill);

    expect((float) $bill->refresh()->amount_untaxed)->toBe(200.0)
        ->and((float) $bill->amount_total)->toBe(200.0);
});

it('posts a vendor bill and marks it posted', function () {
    $bill = AccountHelper::invoice(MoveType::IN_INVOICE, $this->partner);
    AccountHelper::productLine($bill, $this->expense, qty: 2, priceUnit: 100);

    AccountHelper::post($bill);

    expect($bill->refresh()->state)->toBe(MoveState::POSTED);
});

it('creates a payable payment-term line when a vendor bill is posted', function () {
    $bill = AccountHelper::invoice(MoveType::IN_INVOICE, $this->partner);
    AccountHelper::productLine($bill, $this->expense, qty: 2, priceUnit: 100);

    AccountHelper::post($bill);

    $termLine = $bill->refresh()->lines->firstWhere('display_type', DisplayType::PAYMENT_TERM);

    expect($termLine)->not->toBeNull()
        ->and($termLine->account->account_type)->toBe(AccountType::LIABILITY_PAYABLE)
        ->and((float) abs($termLine->balance))->toBe(200.0);
});

it('balances the vendor bill entry so total debit equals total credit', function () {
    $bill = AccountHelper::invoice(MoveType::IN_INVOICE, $this->partner);
    AccountHelper::productLine($bill, $this->expense, qty: 2, priceUnit: 100);

    AccountHelper::post($bill);

    $lines = $bill->refresh()->lines;

    expect((float) $lines->sum(fn ($l) => (float) $l->debit))->toBe(200.0)
        ->and((float) $lines->sum(fn ($l) => (float) $l->credit))->toBe(200.0);
});

it('leaves the posted vendor bill unpaid with the full amount residual', function () {
    $bill = AccountHelper::invoice(MoveType::IN_INVOICE, $this->partner);
    AccountHelper::productLine($bill, $this->expense, qty: 2, priceUnit: 100);

    AccountHelper::post($bill);

    expect($bill->refresh()->payment_state)->toBe(PaymentState::NOT_PAID)
        ->and((float) abs($bill->amount_residual))->toBe(200.0);
});

it('marks a fully paid vendor bill as paid with zero residual', function () {
    $bill = AccountHelper::invoice(MoveType::IN_INVOICE, $this->partner);
    AccountHelper::productLine($bill, $this->expense, qty: 2, priceUnit: 100);
    AccountHelper::post($bill);

    AccountHelper::pay($bill);

    expect($bill->refresh()->payment_state)->toBe(PaymentState::PAID)
        ->and((float) abs($bill->amount_residual))->toBe(0.0);
});

it('marks a partially paid vendor bill as partial with a remaining residual', function () {
    $bill = AccountHelper::invoice(MoveType::IN_INVOICE, $this->partner);
    AccountHelper::productLine($bill, $this->expense, qty: 2, priceUnit: 100);
    AccountHelper::post($bill);

    AccountHelper::pay($bill, amount: 120);

    expect($bill->refresh()->payment_state)->toBe(PaymentState::PARTIAL)
        ->and((float) abs($bill->amount_residual))->toBe(80.0);
});

it('reconciles a vendor refund against the bill and clears the residual', function () {
    $bill = AccountHelper::invoice(MoveType::IN_INVOICE, $this->partner);
    AccountHelper::productLine($bill, $this->expense, qty: 2, priceUnit: 100);
    AccountHelper::post($bill);

    $refund = AccountHelper::invoice(MoveType::IN_REFUND, $this->partner);
    AccountHelper::productLine($refund, $this->expense, qty: 2, priceUnit: 100);
    AccountHelper::post($refund);

    AccountHelper::reconcile($bill, $refund);

    expect((float) abs($bill->refresh()->amount_residual))->toBe(0.0)
        ->and($bill->payment_state)->toBe(PaymentState::REVERSED);
});
