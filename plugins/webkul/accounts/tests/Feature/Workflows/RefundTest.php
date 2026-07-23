<?php

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

it('computes vendor refund totals from an expense line', function () {
    $refund = AccountHelper::invoice(MoveType::IN_REFUND, $this->partner);
    AccountHelper::productLine($refund, $this->expense, qty: 2, priceUnit: 100);

    AccountHelper::compute($refund);

    expect((float) $refund->refresh()->amount_untaxed)->toBe(200.0)
        ->and((float) $refund->amount_total)->toBe(200.0);
});

it('posts a vendor refund and keeps the entry balanced', function () {
    $refund = AccountHelper::invoice(MoveType::IN_REFUND, $this->partner);
    AccountHelper::productLine($refund, $this->expense, qty: 2, priceUnit: 100);

    AccountHelper::post($refund);

    $lines = $refund->refresh()->lines;

    expect($refund->state)->toBe(MoveState::POSTED)
        ->and((float) $lines->sum(fn ($l) => (float) $l->debit))->toBe(200.0)
        ->and((float) $lines->sum(fn ($l) => (float) $l->credit))->toBe(200.0);
});

it('reverses a posted vendor bill into a linked vendor refund', function () {
    $bill = AccountHelper::invoice(MoveType::IN_INVOICE, $this->partner);
    AccountHelper::productLine($bill, $this->expense, qty: 2, priceUnit: 100);

    AccountHelper::post($bill);

    $reversal = AccountHelper::reverse($bill);

    expect($reversal)->not->toBeNull()
        ->and($reversal->reversed_entry_id)->toBe($bill->id);
});

it('mirrors the original debit and credit on the vendor refund reversal', function () {
    $bill = AccountHelper::invoice(MoveType::IN_INVOICE, $this->partner);
    AccountHelper::productLine($bill, $this->expense, qty: 2, priceUnit: 100);

    AccountHelper::post($bill);

    $reversal = AccountHelper::reverse($bill);

    $lines = $reversal->refresh()->lines;

    expect((float) $lines->sum(fn ($l) => (float) $l->debit))->toBe(200.0)
        ->and((float) $lines->sum(fn ($l) => (float) $l->credit))->toBe(200.0);
});

it('reverses and reconciles a posted vendor bill, marking it reversed', function () {
    $bill = AccountHelper::invoice(MoveType::IN_INVOICE, $this->partner);
    AccountHelper::productLine($bill, $this->expense, qty: 2, priceUnit: 100);
    AccountHelper::post($bill);

    $refund = AccountHelper::reverse($bill);

    AccountHelper::post($refund);

    expect($bill->refresh()->payment_state)->toBe(PaymentState::REVERSED)
        ->and((float) abs($bill->amount_residual))->toBe(0.0);
});
