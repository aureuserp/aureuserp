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

it('sums the child taxes of a group tax on the subtotal', function () {
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

it('creates a separate tax line for each child of a group tax on post', function () {
    $group = AccountHelper::groupTax([
        AccountHelper::taxWithAccounts(10),
        AccountHelper::taxWithAccounts(5),
    ]);

    $invoice = AccountHelper::invoice(MoveType::OUT_INVOICE, $this->partner);
    AccountHelper::productLine($invoice, $this->income, qty: 2, priceUnit: 100, taxes: [$group]);

    AccountHelper::post($invoice);

    $taxLines = $invoice->refresh()->lines->where('display_type', DisplayType::TAX);

    $taxAmounts = $taxLines->map(fn ($l) => abs((float) $l->balance))->sort()->values()->all();

    expect($taxLines)->toHaveCount(2)
        ->and($taxAmounts)->toBe([10.0, 20.0]);
});

it('keeps a group-taxed invoice balanced on post', function () {
    $group = AccountHelper::groupTax([
        AccountHelper::taxWithAccounts(10),
        AccountHelper::taxWithAccounts(5),
    ]);

    $invoice = AccountHelper::invoice(MoveType::OUT_INVOICE, $this->partner);
    AccountHelper::productLine($invoice, $this->income, qty: 2, priceUnit: 100, taxes: [$group]);

    AccountHelper::post($invoice);

    $lines = $invoice->refresh()->lines;

    expect((float) $lines->sum(fn ($l) => (float) $l->debit))->toBe(230.0)
        ->and((float) $lines->sum(fn ($l) => (float) $l->credit))->toBe(230.0);
});
