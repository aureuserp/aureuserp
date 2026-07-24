<?php

use Webkul\Account\Enums\DisplayType;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Enums\RoundingStrategy;
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

it('adds a rounding line so the invoice total lands on the rounding step', function () {
    $rounding = AccountHelper::cashRounding(0.05);

    $invoice = AccountHelper::invoice(MoveType::OUT_INVOICE, $this->partner, null, ['invoice_cash_rounding_id' => $rounding->id]);
    AccountHelper::productLine($invoice, $this->income, qty: 3, priceUnit: 33.33);

    AccountHelper::compute($invoice);
    AccountHelper::post($invoice);

    $roundingLine = $invoice->refresh()->lines->firstWhere('display_type', DisplayType::ROUNDING);

    expect($roundingLine)->not->toBeNull()
        ->and((float) $invoice->amount_total)->toBe(100.0);
});

it('keeps a cash-rounded invoice balanced', function () {
    $rounding = AccountHelper::cashRounding(0.05);

    $invoice = AccountHelper::invoice(MoveType::OUT_INVOICE, $this->partner, null, ['invoice_cash_rounding_id' => $rounding->id]);
    AccountHelper::productLine($invoice, $this->income, qty: 3, priceUnit: 33.33);

    AccountHelper::compute($invoice);
    AccountHelper::post($invoice);

    $lines = $invoice->refresh()->lines;

    expect((float) $lines->sum(fn ($l) => (float) $l->debit))
        ->toBe((float) $lines->sum(fn ($l) => (float) $l->credit));
});

it('does not add a rounding line when the total already lands on the step', function () {
    $rounding = AccountHelper::cashRounding(0.05);

    $invoice = AccountHelper::invoice(MoveType::OUT_INVOICE, $this->partner, null, ['invoice_cash_rounding_id' => $rounding->id]);
    AccountHelper::productLine($invoice, $this->income, qty: 2, priceUnit: 100);

    AccountHelper::compute($invoice);
    AccountHelper::post($invoice);

    $roundingLine = $invoice->refresh()->lines->firstWhere('display_type', DisplayType::ROUNDING);

    expect($roundingLine)->toBeNull()
        ->and((float) $invoice->amount_total)->toBe(200.0);
});

it('folds the rounding into the biggest tax line under the biggest-tax strategy', function () {
    $tax = AccountHelper::taxWithAccounts(10);
    $rounding = AccountHelper::cashRounding(0.05, RoundingStrategy::BIGGEST_TAX);

    $invoice = AccountHelper::invoice(MoveType::OUT_INVOICE, $this->partner, null, ['invoice_cash_rounding_id' => $rounding->id]);
    AccountHelper::productLine($invoice, $this->income, qty: 3, priceUnit: 33.33, taxes: [$tax]);

    AccountHelper::compute($invoice);
    AccountHelper::post($invoice);

    $lines = $invoice->refresh()->lines;
    $roundingLine = $lines->firstWhere('display_type', DisplayType::ROUNDING);

    expect($roundingLine)->not->toBeNull()
        ->and($roundingLine->tax_repartition_line_id)->not->toBeNull()
        ->and((float) $invoice->amount_total)->toBe(110.0)
        ->and((float) $lines->sum(fn ($l) => (float) $l->debit))
            ->toBe((float) $lines->sum(fn ($l) => (float) $l->credit));
});
