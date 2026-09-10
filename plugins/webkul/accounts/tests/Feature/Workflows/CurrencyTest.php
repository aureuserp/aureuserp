<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Webkul\Account\Enums\DisplayType;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Enums\PaymentState;
use Webkul\Account\Models\Move;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Package;
use Webkul\Support\Models\Currency;

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

    $this->foreign = AccountHelper::otherCurrency();
    $this->foreign->rates()->delete();
    AccountHelper::setCurrencyRate($this->foreign, 2.0);
});

it('converts nothing when both currencies are the same', function () {
    $company = AccountHelper::company();

    expect($this->foreign->getConversionRate($this->foreign, $this->foreign, $company))->toBe(1);
});

it('converts from company currency into a foreign currency at the foreign rate', function () {
    $company = AccountHelper::company();
    $base = $company->currency;

    expect($base->convert(100, $this->foreign, $company))->toBe(200.0);
});

it('converts from a foreign currency back into company currency', function () {
    $company = AccountHelper::company();
    $base = $company->currency;

    expect($this->foreign->convert(200, $base, $company))->toBe(100.0);
});

it('converts between two foreign currencies using both rates', function () {
    $company = AccountHelper::company();

    $other = Currency::query()
        ->whereNotIn('id', [$company->currency_id, $this->foreign->id])
        ->firstOrFail();

    $other->rates()->delete();

    AccountHelper::setCurrencyRate($other, 8.0);

    expect($this->foreign->convert(100, $other, $company))->toBe(400.0);
});

it('returns to the original amount after a round trip', function () {
    $company = AccountHelper::company();
    $base = $company->currency;

    $converted = $base->convert(250, $this->foreign, $company);

    expect($this->foreign->convert($converted, $base, $company))->toBe(250.0);
});

it('totals a foreign-currency invoice in its own currency', function () {
    $invoice = AccountHelper::invoice(MoveType::OUT_INVOICE, $this->partner, null, ['currency_id' => $this->foreign->id]);
    AccountHelper::productLine($invoice, $this->income, qty: 2, priceUnit: 100);

    AccountHelper::compute($invoice);

    expect((float) $invoice->refresh()->amount_total)->toBe(200.0);
});

it('records the foreign amount separately from the company-currency balance', function () {
    $invoice = AccountHelper::invoice(MoveType::OUT_INVOICE, $this->partner, null, ['currency_id' => $this->foreign->id]);
    AccountHelper::productLine($invoice, $this->income, qty: 2, priceUnit: 100);

    AccountHelper::post($invoice);

    $productLine = $invoice->refresh()->lines->firstWhere('display_type', DisplayType::PRODUCT);

    expect((float) abs($productLine->amount_currency))->toBe(200.0)
        ->and((float) abs($productLine->balance))->toBe(100.0);
});

it('keeps a foreign-currency invoice balanced in company currency', function () {
    $invoice = AccountHelper::invoice(MoveType::OUT_INVOICE, $this->partner, null, ['currency_id' => $this->foreign->id]);
    AccountHelper::productLine($invoice, $this->income, qty: 2, priceUnit: 100);

    AccountHelper::post($invoice);

    $lines = $invoice->refresh()->lines;

    expect((float) $lines->sum(fn ($l) => (float) $l->debit))
        ->toBe((float) $lines->sum(fn ($l) => (float) $l->credit));
});

it('creates an exchange-difference entry when reconciling foreign documents at different rates', function () {
    AccountHelper::setCurrencyRate($this->foreign, 4.0, now()->toDateString());

    $invoice = AccountHelper::invoice(MoveType::OUT_INVOICE, $this->partner, null, [
        'currency_id'  => $this->foreign->id,
        'invoice_date' => now()->subYear()->toDateString(),
        'date'         => now()->subYear()->toDateString(),
    ]);
    AccountHelper::productLine($invoice, $this->income, qty: 2, priceUnit: 100);
    AccountHelper::post($invoice);

    $creditNote = AccountHelper::invoice(MoveType::OUT_REFUND, $this->partner, null, [
        'currency_id'  => $this->foreign->id,
        'invoice_date' => now()->toDateString(),
        'date'         => now()->toDateString(),
    ]);
    AccountHelper::productLine($creditNote, $this->income, qty: 2, priceUnit: 100);
    AccountHelper::post($creditNote);

    AccountHelper::reconcile($invoice, $creditNote);

    expect((float) abs($invoice->refresh()->amount_residual))->toBe(0.0)
        ->and(Move::where('move_type', MoveType::ENTRY)->count())->toBeGreaterThan(0);
});

it('marks a foreign-currency invoice paid when settled in its own currency', function () {
    $invoice = AccountHelper::invoice(MoveType::OUT_INVOICE, $this->partner, null, ['currency_id' => $this->foreign->id]);
    AccountHelper::productLine($invoice, $this->income, qty: 2, priceUnit: 100);
    AccountHelper::post($invoice);

    AccountHelper::pay($invoice);

    $receivable = $invoice->refresh()->lines->firstWhere('display_type', DisplayType::PAYMENT_TERM);

    expect($invoice->payment_state)->toBe(PaymentState::PAID)
        ->and((float) abs($invoice->amount_residual))->toBe(0.0)
        ->and((float) abs($receivable->amount_residual_currency))->toBe(0.0);
});
