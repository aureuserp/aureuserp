<?php

use Webkul\Account\Enums\DisplayType;
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
    $this->partner = AccountHelper::partner();
});

it('splits the receivable into multiple installments by percentage', function () {
    $term = AccountHelper::paymentTerm([[30, 15], [70, 45]]);

    $invoice = AccountHelper::invoice(MoveType::OUT_INVOICE, $this->partner, null, ['invoice_payment_term_id' => $term->id]);
    AccountHelper::productLine($invoice, $this->income, qty: 2, priceUnit: 100);

    AccountHelper::post($invoice);

    $termLines = $invoice->refresh()->lines->where('display_type', DisplayType::PAYMENT_TERM);

    expect($termLines)->toHaveCount(2)
        ->and((float) $termLines->sum(fn ($l) => abs((float) $l->balance)))->toBe(200.0)
        ->and($termLines->pluck('date_maturity')->map->toDateString()->unique())->toHaveCount(2);
});

it('assigns the installment balances in the configured proportions', function () {
    $term = AccountHelper::paymentTerm([[30, 15], [70, 45]]);

    $invoice = AccountHelper::invoice(MoveType::OUT_INVOICE, $this->partner, null, ['invoice_payment_term_id' => $term->id]);
    AccountHelper::productLine($invoice, $this->income, qty: 2, priceUnit: 100);

    AccountHelper::post($invoice);

    $balances = $invoice->refresh()->lines
        ->where('display_type', DisplayType::PAYMENT_TERM)
        ->map(fn ($l) => abs((float) $l->balance))
        ->sort()
        ->values()
        ->all();

    expect($balances)->toBe([60.0, 140.0]);
});

it('keeps a multi-installment invoice balanced', function () {
    $term = AccountHelper::paymentTerm([[30, 15], [70, 45]]);

    $invoice = AccountHelper::invoice(MoveType::OUT_INVOICE, $this->partner, null, ['invoice_payment_term_id' => $term->id]);
    AccountHelper::productLine($invoice, $this->income, qty: 2, priceUnit: 100);

    AccountHelper::post($invoice);

    $lines = $invoice->refresh()->lines;

    expect((float) $lines->sum(fn ($l) => (float) $l->debit))->toBe(200.0)
        ->and((float) $lines->sum(fn ($l) => (float) $l->credit))->toBe(200.0);
});

it('sets an early payment discount date on the receivable line', function () {
    $term = AccountHelper::earlyPaymentTerm(discountPercent: 2, discountDays: 7, dueDays: 30);

    $invoice = AccountHelper::invoice(MoveType::OUT_INVOICE, $this->partner, null, ['invoice_payment_term_id' => $term->id]);
    AccountHelper::productLine($invoice, $this->income, qty: 2, priceUnit: 100);

    AccountHelper::post($invoice);

    $termLine = $invoice->refresh()->lines->firstWhere('display_type', DisplayType::PAYMENT_TERM);

    expect($termLine)->not->toBeNull()
        ->and((float) abs($termLine->balance))->toBe(200.0);
});
