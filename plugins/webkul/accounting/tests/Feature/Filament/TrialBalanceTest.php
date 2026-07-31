<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Webkul\Accounting\Filament\Clusters\Reporting\Pages\TrialBalance;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Package;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/FilamentHelper.php';
require_once __DIR__.'/../../../../accounts/tests/Helpers/AccountHelper.php';
require_once __DIR__.'/../../Helpers/ReportHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('accounting');

    DB::table('plugins')->whereIn('name', ['accounts', 'accounting'])->update([
        'is_installed' => true,
        'is_active'    => true,
        'updated_at'   => now(),
    ]);

    Package::$plugins = Plugin::all()->keyBy('name');

    URL::resolveMissingNamedRoutesUsing(fn () => '#');

    FilamentHelper::actingAs(['page_accounting_trial_balance']);

    $this->income = AccountHelper::account('income');
    $this->partner = AccountHelper::partner();
});

it('renders the trial balance page', function () {
    Livewire::test(TrialBalance::class)->assertOk();
});

it('reports the posted revenue for the period', function () {
    $invoice = ReportHelper::postedSale('2026-03-10', 2, 100, $this->income, $this->partner);

    $data = ReportHelper::data(TrialBalance::class, 'trialBalanceData', ['date_range' => '2026-03-01 - 2026-03-31']);

    $row = ReportHelper::rowBy($data['accounts'], 'id', ReportHelper::productAccountId($invoice));

    expect($row)->not->toBeNull()
        ->and((float) $row->period_credit)->toBe(200.0)
        ->and((float) $row->end_credit)->toBe(200.0);
});

it('keeps total debits equal to total credits', function () {
    ReportHelper::postedSale('2026-03-10', 2, 100, $this->income, $this->partner);

    $data = ReportHelper::data(TrialBalance::class, 'trialBalanceData', ['date_range' => '2026-03-01 - 2026-03-31']);

    $totals = $data['totals'];

    expect((float) $totals['end_debit'])->toBe((float) $totals['end_credit'])
        ->and((float) $totals['end_debit'])->toBe(200.0);
});

it('sums several invoices posted inside the period', function () {
    $invoice = ReportHelper::postedSale('2026-03-05', 1, 100, $this->income, $this->partner);
    ReportHelper::postedSale('2026-03-20', 3, 50, $this->income, $this->partner);

    $data = ReportHelper::data(TrialBalance::class, 'trialBalanceData', ['date_range' => '2026-03-01 - 2026-03-31']);

    $row = ReportHelper::rowBy($data['accounts'], 'id', ReportHelper::productAccountId($invoice));

    expect((float) $row->period_credit)->toBe(250.0);
});

it('excludes invoices posted after the period', function () {
    $invoice = ReportHelper::postedSale('2026-03-10', 2, 100, $this->income, $this->partner);
    ReportHelper::postedSale('2026-05-10', 5, 100, $this->income, $this->partner);

    $data = ReportHelper::data(TrialBalance::class, 'trialBalanceData', ['date_range' => '2026-03-01 - 2026-03-31']);

    $row = ReportHelper::rowBy($data['accounts'], 'id', ReportHelper::productAccountId($invoice));

    expect((float) $row->period_credit)->toBe(200.0)
        ->and((float) $row->end_credit)->toBe(200.0);
});

it('carries an earlier invoice into the initial balance', function () {
    ReportHelper::postedSale('2026-01-15', 1, 400, $this->income, $this->partner);
    $invoice = ReportHelper::postedSale('2026-03-10', 2, 100, $this->income, $this->partner);

    $data = ReportHelper::data(TrialBalance::class, 'trialBalanceData', ['date_range' => '2026-03-01 - 2026-03-31']);

    $row = ReportHelper::rowBy($data['accounts'], 'id', ReportHelper::productAccountId($invoice));

    expect((float) $row->initial_credit)->toBe(400.0)
        ->and((float) $row->period_credit)->toBe(200.0)
        ->and((float) $row->end_credit)->toBe(600.0);
});

it('excludes draft invoices that were never posted', function () {
    ReportHelper::draftSale('2026-03-10', 9, 100, $this->income, $this->partner);

    $invoice = ReportHelper::postedSale('2026-03-10', 2, 100, $this->income, $this->partner);

    $data = ReportHelper::data(TrialBalance::class, 'trialBalanceData', ['date_range' => '2026-03-01 - 2026-03-31']);

    $row = ReportHelper::rowBy($data['accounts'], 'id', ReportHelper::productAccountId($invoice));

    expect((float) $row->period_credit)->toBe(200.0);
});

it('keeps the revenue when filtering on the journal that posted it', function () {
    $journal = AccountHelper::saleJournal();

    $invoice = ReportHelper::postedSale('2026-03-10', 2, 100, $this->income, $this->partner, $journal);

    $data = ReportHelper::data(TrialBalance::class, 'trialBalanceData', [
        'date_range' => '2026-03-01 - 2026-03-31',
        'journals'   => [$journal->id],
    ]);

    $row = ReportHelper::rowBy($data['accounts'], 'id', ReportHelper::productAccountId($invoice));

    expect((float) $row->period_credit)->toBe(200.0);
});

it('drops the revenue when filtering on an unrelated journal', function () {
    $invoice = ReportHelper::postedSale('2026-03-10', 2, 100, $this->income, $this->partner, AccountHelper::saleJournal());

    $other = AccountHelper::generalJournal();

    $data = ReportHelper::data(TrialBalance::class, 'trialBalanceData', [
        'date_range' => '2026-03-01 - 2026-03-31',
        'journals'   => [$other->id],
    ]);

    $row = ReportHelper::rowBy($data['accounts'], 'id', ReportHelper::productAccountId($invoice));

    expect($row)->toBeNull();
});
