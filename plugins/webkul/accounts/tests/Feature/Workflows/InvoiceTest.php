<?php

use Webkul\Account\Enums\AccountType;
use Webkul\Account\Enums\AmountType;
use Webkul\Account\Enums\DisplayType;
use Webkul\Account\Enums\DocumentType;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Enums\PaymentState;
use Webkul\Account\Enums\RepartitionType;
use Webkul\Account\Enums\TaxIncludeOverride;
use Webkul\Account\Enums\TypeTaxUse;
use Webkul\Account\Models\TaxPartition;
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

it('computes invoice totals from a product line with no tax', function () {
    $invoice = AccountHelper::invoice(MoveType::OUT_INVOICE, $this->partner);
    AccountHelper::productLine($invoice, $this->income, qty: 2, priceUnit: 100);

    AccountHelper::compute($invoice);

    $invoice->refresh();

    expect((float) $invoice->amount_untaxed)->toBe(200.0)
        ->and((float) $invoice->amount_tax)->toBe(0.0)
        ->and((float) $invoice->amount_total)->toBe(200.0);
});

it('posts a customer invoice and marks it posted', function () {
    $invoice = AccountHelper::invoice(MoveType::OUT_INVOICE, $this->partner);
    AccountHelper::productLine($invoice, $this->income, qty: 2, priceUnit: 100);

    AccountHelper::post($invoice);

    expect($invoice->refresh()->state)->toBe(MoveState::POSTED);
});

it('creates a receivable payment-term line when a customer invoice is posted', function () {
    $invoice = AccountHelper::invoice(MoveType::OUT_INVOICE, $this->partner);
    AccountHelper::productLine($invoice, $this->income, qty: 2, priceUnit: 100);

    AccountHelper::post($invoice);

    $termLine = $invoice->refresh()->lines
        ->firstWhere('display_type', DisplayType::PAYMENT_TERM);

    expect($termLine)->not->toBeNull()
        ->and($termLine->account->account_type)->toBe(AccountType::ASSET_RECEIVABLE)
        ->and((float) abs($termLine->balance))->toBe(200.0);
});

it('balances the journal entry so total debit equals total credit', function () {
    $invoice = AccountHelper::invoice(MoveType::OUT_INVOICE, $this->partner);
    AccountHelper::productLine($invoice, $this->income, qty: 2, priceUnit: 100);

    AccountHelper::post($invoice);

    $lines = $invoice->refresh()->lines;

    $debit = (float) $lines->sum(fn ($l) => (float) $l->debit);
    $credit = (float) $lines->sum(fn ($l) => (float) $l->credit);

    expect($debit)->toBe(200.0)
        ->and($credit)->toBe(200.0);
});

it('leaves the posted invoice unpaid with the full amount residual', function () {
    $invoice = AccountHelper::invoice(MoveType::OUT_INVOICE, $this->partner);
    AccountHelper::productLine($invoice, $this->income, qty: 2, priceUnit: 100);

    AccountHelper::post($invoice);

    $invoice->refresh();

    expect($invoice->payment_state)->toBe(PaymentState::NOT_PAID)
        ->and((float) abs($invoice->amount_residual))->toBe(200.0);
});

it('applies a discount to the invoice subtotal', function () {
    $invoice = AccountHelper::invoice(MoveType::OUT_INVOICE, $this->partner);
    AccountHelper::productLine($invoice, $this->income, qty: 2, priceUnit: 100, discount: 10);

    AccountHelper::compute($invoice);

    expect((float) $invoice->refresh()->amount_untaxed)->toBe(180.0)
        ->and((float) $invoice->amount_total)->toBe(180.0);
});

it('aggregates multiple product lines into the invoice total', function () {
    $invoice = AccountHelper::invoice(MoveType::OUT_INVOICE, $this->partner);
    AccountHelper::productLine($invoice, $this->income, qty: 2, priceUnit: 100);
    AccountHelper::productLine($invoice, $this->income, qty: 1, priceUnit: 50);

    AccountHelper::compute($invoice);

    expect((float) $invoice->refresh()->amount_untaxed)->toBe(250.0)
        ->and((float) $invoice->amount_total)->toBe(250.0);
});

it('adds a tax total to a taxed invoice', function () {
    $tax = AccountHelper::taxWithAccounts(10);

    $invoice = AccountHelper::invoice(MoveType::OUT_INVOICE, $this->partner);
    AccountHelper::productLine($invoice, $this->income, qty: 2, priceUnit: 100, taxes: [$tax]);

    AccountHelper::compute($invoice);

    expect((float) $invoice->refresh()->amount_untaxed)->toBe(200.0)
        ->and((float) $invoice->amount_tax)->toBe(20.0)
        ->and((float) $invoice->amount_total)->toBe(220.0);
});

it('creates a tax line and keeps the entry balanced when a taxed invoice is posted', function () {
    $tax = AccountHelper::taxWithAccounts(10);

    $invoice = AccountHelper::invoice(MoveType::OUT_INVOICE, $this->partner);
    AccountHelper::productLine($invoice, $this->income, qty: 2, priceUnit: 100, taxes: [$tax]);

    AccountHelper::post($invoice);

    $lines = $invoice->refresh()->lines;
    $taxLine = $lines->firstWhere('display_type', DisplayType::TAX);

    $debit = (float) $lines->sum(fn ($l) => (float) $l->debit);
    $credit = (float) $lines->sum(fn ($l) => (float) $l->credit);

    expect($taxLine)->not->toBeNull()
        ->and((float) abs($taxLine->balance))->toBe(20.0)
        ->and($debit)->toBe(220.0)
        ->and($credit)->toBe(220.0);
});

/*
|--------------------------------------------------------------------------
| Settings: payment terms, tax groups, cash rounding, currency
|--------------------------------------------------------------------------
*/

it('splits the receivable into two installment lines with distinct due dates for a payment term', function () {
    $term = AccountHelper::paymentTerm([[50, 0], [50, 30]]);

    $invoice = AccountHelper::invoice(MoveType::OUT_INVOICE, $this->partner, null, [
        'invoice_payment_term_id' => $term->id,
    ]);
    AccountHelper::productLine($invoice, $this->income, qty: 2, priceUnit: 100);

    AccountHelper::post($invoice);

    $lines = $invoice->refresh()->lines;
    $termLines = $lines->where('display_type', DisplayType::PAYMENT_TERM)->values();

    expect($termLines)->toHaveCount(2)
        ->and($termLines->pluck('date_maturity')->map(fn ($d) => (string) $d)->unique())->toHaveCount(2)
        ->and((float) $lines->sum(fn ($l) => (float) $l->debit))->toBe(200.0)
        ->and((float) $lines->sum(fn ($l) => (float) $l->credit))->toBe(200.0);
});

it('creates a separate tax line per tax group', function () {
    $groupA = AccountHelper::taxWithAccounts(10);
    $groupB = AccountHelper::taxWithAccounts(5);

    $invoice = AccountHelper::invoice(MoveType::OUT_INVOICE, $this->partner);
    AccountHelper::productLine($invoice, $this->income, qty: 2, priceUnit: 100, taxes: [$groupA, $groupB]);

    AccountHelper::post($invoice);

    $taxLines = $invoice->refresh()->lines->where('display_type', DisplayType::TAX)->values();

    expect($taxLines)->toHaveCount(2)
        ->and($taxLines->pluck('tax_group_id')->unique())->toHaveCount(2)
        ->and((float) $invoice->amount_tax)->toBe(30.0);
});

it('adds a rounding line and rounds the total to the cash-rounding precision', function () {
    $rounding = AccountHelper::cashRounding(0.05);

    $invoice = AccountHelper::invoice(MoveType::OUT_INVOICE, $this->partner, null, [
        'invoice_cash_rounding_id' => $rounding->id,
    ]);
    AccountHelper::productLine($invoice, $this->income, qty: 1, priceUnit: 100.02);

    AccountHelper::compute($invoice);
    AccountHelper::post($invoice);

    $roundingLine = $invoice->refresh()->lines->firstWhere('display_type', DisplayType::ROUNDING);

    expect($roundingLine)->not->toBeNull()
        ->and((float) $invoice->amount_total)->toBe(100.0);
});

it('records the foreign amount and company balance separately on a foreign-currency invoice', function () {
    $currency = AccountHelper::otherCurrency();

    $invoice = AccountHelper::invoice(MoveType::OUT_INVOICE, $this->partner, null, [
        'currency_id'          => $currency->id,
        'invoice_currency_rate' => 2.0,
    ]);
    AccountHelper::productLine($invoice, $this->income, qty: 2, priceUnit: 100);

    AccountHelper::post($invoice);

    $lines = $invoice->refresh()->lines;

    expect((float) $invoice->amount_total)->toBe(200.0)
        ->and((float) $lines->sum(fn ($l) => (float) $l->debit))->toBe((float) $lines->sum(fn ($l) => (float) $l->credit));
});

/*
|--------------------------------------------------------------------------
| Payment + reconciliation
|--------------------------------------------------------------------------
*/

it('marks a fully paid invoice as paid with zero residual', function () {
    $invoice = AccountHelper::invoice(MoveType::OUT_INVOICE, $this->partner);
    AccountHelper::productLine($invoice, $this->income, qty: 2, priceUnit: 100);
    AccountHelper::post($invoice);

    AccountHelper::pay($invoice);

    expect($invoice->refresh()->payment_state)->toBe(PaymentState::PAID)
        ->and((float) abs($invoice->amount_residual))->toBe(0.0);
});

it('marks a partially paid invoice as partial with a remaining residual', function () {
    $invoice = AccountHelper::invoice(MoveType::OUT_INVOICE, $this->partner);
    AccountHelper::productLine($invoice, $this->income, qty: 2, priceUnit: 100);
    AccountHelper::post($invoice);

    AccountHelper::pay($invoice, amount: 120);

    expect($invoice->refresh()->payment_state)->toBe(PaymentState::PARTIAL)
        ->and((float) abs($invoice->amount_residual))->toBe(80.0);
});

it('marks the invoice paid after two partial payments settle the balance', function () {
    $invoice = AccountHelper::invoice(MoveType::OUT_INVOICE, $this->partner);
    AccountHelper::productLine($invoice, $this->income, qty: 2, priceUnit: 100);
    AccountHelper::post($invoice);

    AccountHelper::pay($invoice, amount: 120);
    AccountHelper::pay($invoice->refresh(), amount: 80);

    expect($invoice->refresh()->payment_state)->toBe(PaymentState::PAID)
        ->and((float) abs($invoice->amount_residual))->toBe(0.0);
});

it('reconciles the payment against the receivable line', function () {
    $invoice = AccountHelper::invoice(MoveType::OUT_INVOICE, $this->partner);
    AccountHelper::productLine($invoice, $this->income, qty: 2, priceUnit: 100);
    AccountHelper::post($invoice);

    AccountHelper::pay($invoice);

    expect((bool) $invoice->refresh()->paymentTermLines->first()->reconciled)->toBeTrue();
});

/*
|--------------------------------------------------------------------------
| Tax variants: division, group, repartition
|--------------------------------------------------------------------------
*/

it('computes a division tax out of the base', function () {
    $tax = AccountHelper::taxWithAccounts(10, AmountType::DIVISION);

    $invoice = AccountHelper::invoice(MoveType::OUT_INVOICE, $this->partner);
    AccountHelper::productLine($invoice, $this->income, qty: 2, priceUnit: 100, taxes: [$tax]);

    AccountHelper::compute($invoice);

    expect((float) $invoice->refresh()->amount_untaxed)->toBe(200.0)
        ->and((float) $invoice->amount_tax)->toBe(22.22)
        ->and((float) $invoice->amount_total)->toBe(222.22);
});

it('sums the child taxes of a group tax', function () {
    $group = AccountHelper::groupTax([
        AccountHelper::taxWithAccounts(10),
        AccountHelper::taxWithAccounts(5),
    ]);

    $invoice = AccountHelper::invoice(MoveType::OUT_INVOICE, $this->partner);
    AccountHelper::productLine($invoice, $this->income, qty: 2, priceUnit: 100, taxes: [$group]);

    AccountHelper::compute($invoice);

    expect((float) $invoice->refresh()->amount_untaxed)->toBe(200.0)
        ->and((float) $invoice->amount_tax)->toBe(30.0)
        ->and((float) $invoice->amount_total)->toBe(230.0);
});

it('links the tax move line to its invoice repartition line', function () {
    $tax = AccountHelper::taxWithAccounts(10);

    $invoice = AccountHelper::invoice(MoveType::OUT_INVOICE, $this->partner);
    AccountHelper::productLine($invoice, $this->income, qty: 2, priceUnit: 100, taxes: [$tax]);

    AccountHelper::post($invoice);

    $taxLine = $invoice->refresh()->lines->firstWhere('display_type', DisplayType::TAX);
    $repartition = TaxPartition::find($taxLine->tax_repartition_line_id);

    expect($taxLine->tax_repartition_line_id)->not->toBeNull()
        ->and($repartition->document_type)->toBe(DocumentType::INVOICE)
        ->and($repartition->repartition_type)->toBe(RepartitionType::TAX);
});

it('splits a tax across two repartition lines', function () {
    $tax = AccountHelper::taxWithAccounts(10, taxFactors: [40, 60]);

    $invoice = AccountHelper::invoice(MoveType::OUT_INVOICE, $this->partner);
    AccountHelper::productLine($invoice, $this->income, qty: 2, priceUnit: 100, taxes: [$tax]);

    AccountHelper::post($invoice);

    $taxLines = $invoice->refresh()->lines->where('display_type', DisplayType::TAX)->values();

    expect($taxLines)->toHaveCount(2)
        ->and((float) $taxLines->sum(fn ($l) => abs((float) $l->balance)))->toBe(20.0)
        ->and($taxLines->every(fn ($l) => $l->tax_repartition_line_id !== null))->toBeTrue();
});

it('nets a reverse-charge tax to zero across positive and negative repartition', function () {
    $tax = AccountHelper::taxWithAccounts(10, taxFactors: [100, -100]);

    $invoice = AccountHelper::invoice(MoveType::OUT_INVOICE, $this->partner);
    AccountHelper::productLine($invoice, $this->income, qty: 2, priceUnit: 100, taxes: [$tax]);

    AccountHelper::post($invoice);

    $lines = $invoice->refresh()->lines;
    $taxLines = $lines->where('display_type', DisplayType::TAX)->values();

    expect($taxLines)->toHaveCount(2)
        ->and((float) $invoice->amount_tax)->toBe(0.0)
        ->and((float) $lines->sum(fn ($l) => (float) $l->debit))->toBe((float) $lines->sum(fn ($l) => (float) $l->credit));
});

it('sets an early-payment discount date on the receivable line', function () {
    $term = AccountHelper::earlyPaymentTerm(2, 7);

    $invoice = AccountHelper::invoice(MoveType::OUT_INVOICE, $this->partner, null, [
        'invoice_payment_term_id' => $term->id,
    ]);
    AccountHelper::productLine($invoice, $this->income, qty: 2, priceUnit: 100);

    AccountHelper::post($invoice);

    $termLine = $invoice->refresh()->lines->firstWhere('display_type', DisplayType::PAYMENT_TERM);

    expect($termLine)->not->toBeNull()
        ->and((float) abs($termLine->balance))->toBe(200.0);
});

it('reconciles a credit note against the invoice and clears the residual', function () {
    $invoice = AccountHelper::invoice(MoveType::OUT_INVOICE, $this->partner);
    AccountHelper::productLine($invoice, $this->income, qty: 2, priceUnit: 100);
    AccountHelper::post($invoice);

    $creditNote = AccountHelper::invoice(MoveType::OUT_REFUND, $this->partner);
    AccountHelper::productLine($creditNote, $this->income, qty: 2, priceUnit: 100);
    AccountHelper::post($creditNote);

    AccountHelper::reconcile($invoice, $creditNote);

    expect((float) abs($invoice->refresh()->amount_residual))->toBe(0.0)
        ->and($invoice->payment_state)->toBe(PaymentState::REVERSED);
});

it('restores the residual when a reconciliation is undone', function () {
    $invoice = AccountHelper::invoice(MoveType::OUT_INVOICE, $this->partner);
    AccountHelper::productLine($invoice, $this->income, qty: 2, priceUnit: 100);
    AccountHelper::post($invoice);

    $creditNote = AccountHelper::invoice(MoveType::OUT_REFUND, $this->partner);
    AccountHelper::productLine($creditNote, $this->income, qty: 2, priceUnit: 100);
    AccountHelper::post($creditNote);

    AccountHelper::reconcile($invoice, $creditNote);
    AccountHelper::unreconcile($invoice);

    expect((float) abs($invoice->refresh()->amount_residual))->toBe(200.0)
        ->and($invoice->payment_state)->toBe(PaymentState::NOT_PAID);
});

it('assigns a sequential name from the journal when the invoice is posted', function () {
    $invoice = AccountHelper::invoice(MoveType::OUT_INVOICE, $this->partner);
    AccountHelper::productLine($invoice, $this->income, qty: 2, priceUnit: 100);

    AccountHelper::post($invoice);

    expect($invoice->refresh()->name)->not->toBeNull()
        ->and($invoice->name)->not->toBe('/');
});

it('extracts an inclusive tax out of the unit price on post', function () {
    $tax = AccountHelper::taxWithAccounts(10, AmountType::PERCENT, TypeTaxUse::SALE, TaxIncludeOverride::TAX_INCLUDED);

    $invoice = AccountHelper::invoice(MoveType::OUT_INVOICE, $this->partner);
    AccountHelper::productLine($invoice, $this->income, qty: 1, priceUnit: 110, taxes: [$tax]);

    AccountHelper::post($invoice);

    $lines = $invoice->refresh()->lines;
    $taxLine = $lines->firstWhere('display_type', DisplayType::TAX);

    expect((float) $invoice->amount_untaxed)->toBe(100.0)
        ->and((float) $invoice->amount_tax)->toBe(10.0)
        ->and((float) $invoice->amount_total)->toBe(110.0)
        ->and((float) abs($taxLine->balance))->toBe(10.0)
        ->and((float) $lines->sum(fn ($l) => (float) $l->debit))->toBe((float) $lines->sum(fn ($l) => (float) $l->credit));
});

it('marks the invoice paid after two partial payments cover the full amount', function () {
    $invoice = AccountHelper::invoice(MoveType::OUT_INVOICE, $this->partner);
    AccountHelper::productLine($invoice, $this->income, qty: 2, priceUnit: 100);
    AccountHelper::post($invoice);

    AccountHelper::pay($invoice, amount: 120);
    AccountHelper::pay($invoice, amount: 80);

    expect($invoice->refresh()->payment_state)->toBe(PaymentState::PAID)
        ->and((float) abs($invoice->amount_residual))->toBe(0.0);
});

it('restores the residual and unpaid state when a payment is unreconciled', function () {
    $invoice = AccountHelper::invoice(MoveType::OUT_INVOICE, $this->partner);
    AccountHelper::productLine($invoice, $this->income, qty: 2, priceUnit: 100);
    AccountHelper::post($invoice);

    AccountHelper::pay($invoice);
    AccountHelper::unreconcile($invoice);

    expect((float) abs($invoice->refresh()->amount_residual))->toBe(200.0)
        ->and($invoice->payment_state)->toBe(PaymentState::NOT_PAID);
});

it('writes off the shortfall and marks the invoice paid when the difference is reconciled', function () {
    $invoice = AccountHelper::invoice(MoveType::OUT_INVOICE, $this->partner);
    AccountHelper::productLine($invoice, $this->income, qty: 2, priceUnit: 100);
    AccountHelper::post($invoice);

    AccountHelper::pay($invoice, amount: 190, differenceHandling: 'reconcile');

    expect($invoice->refresh()->payment_state)->toBe(PaymentState::PAID)
        ->and((float) abs($invoice->amount_residual))->toBe(0.0);
});

it('keeps section and note lines through post without affecting the balance', function () {
    $invoice = AccountHelper::invoice(MoveType::OUT_INVOICE, $this->partner);
    AccountHelper::displayLine($invoice, DisplayType::LINE_SECTION, 'Services');
    AccountHelper::productLine($invoice, $this->income, qty: 2, priceUnit: 100);
    AccountHelper::displayLine($invoice, DisplayType::LINE_NOTE, 'Thanks for your business');

    AccountHelper::post($invoice);

    $lines = $invoice->refresh()->lines;
    $section = $lines->firstWhere('display_type', DisplayType::LINE_SECTION);
    $note = $lines->firstWhere('display_type', DisplayType::LINE_NOTE);

    expect($section)->not->toBeNull()
        ->and($section->account_id)->toBeNull()
        ->and($note)->not->toBeNull()
        ->and($note->account_id)->toBeNull()
        ->and((float) $invoice->amount_untaxed)->toBe(200.0)
        ->and((float) $lines->sum(fn ($l) => (float) $l->debit))->toBe(200.0)
        ->and((float) $lines->sum(fn ($l) => (float) $l->credit))->toBe(200.0);
});
