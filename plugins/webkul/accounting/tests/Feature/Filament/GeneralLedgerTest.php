<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Webkul\Accounting\Filament\Clusters\Reporting\Pages\GeneralLedger;
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

    FilamentHelper::actingAs(['page_accounting_general_ledger']);

    $this->income = AccountHelper::account('income');
    $this->expense = AccountHelper::account('expense');
    $this->partner = AccountHelper::partner();
});

it('renders the general ledger page', function () {
    Livewire::test(GeneralLedger::class)->assertOk();
});

it('reports the period movement on the revenue account', function () {
    $invoice = ReportHelper::postedSale('2026-03-10', 2, 100, $this->income, $this->partner);

    $data = ReportHelper::data(GeneralLedger::class, 'generalLedgerData', ['date_range' => '2026-03-01 - 2026-03-31']);

    $row = ReportHelper::rowBy($data['accounts'], 'id', ReportHelper::productAccountId($invoice));

    expect($row)->not->toBeNull()
        ->and((float) $row->period_credit)->toBe(200.0)
        ->and((float) $row->period_debit)->toBe(0.0);
});

it('reports the period movement on the receivable account', function () {
    $invoice = ReportHelper::postedSale('2026-03-10', 2, 100, $this->income, $this->partner);

    $data = ReportHelper::data(GeneralLedger::class, 'generalLedgerData', ['date_range' => '2026-03-01 - 2026-03-31']);

    $row = ReportHelper::rowBy($data['accounts'], 'id', ReportHelper::receivableAccountId($invoice));

    expect($row)->not->toBeNull()
        ->and((float) $row->period_debit)->toBe(200.0);
});

it('opens the period with the balance carried from earlier entries', function () {
    ReportHelper::postedSale('2026-01-15', 1, 400, $this->income, $this->partner);
    $invoice = ReportHelper::postedSale('2026-03-10', 2, 100, $this->income, $this->partner);

    $data = ReportHelper::data(GeneralLedger::class, 'generalLedgerData', ['date_range' => '2026-03-01 - 2026-03-31']);

    $row = ReportHelper::rowBy($data['accounts'], 'id', ReportHelper::productAccountId($invoice));

    expect((float) $row->opening_balance)->toBe(-400.0)
        ->and((float) $row->period_credit)->toBe(200.0)
        ->and((float) $row->ending_balance)->toBe(-600.0);
});

it('excludes entries posted after the period from the movement', function () {
    $invoice = ReportHelper::postedSale('2026-03-10', 2, 100, $this->income, $this->partner);
    ReportHelper::postedSale('2026-06-10', 9, 100, $this->income, $this->partner);

    $data = ReportHelper::data(GeneralLedger::class, 'generalLedgerData', ['date_range' => '2026-03-01 - 2026-03-31']);

    $row = ReportHelper::rowBy($data['accounts'], 'id', ReportHelper::productAccountId($invoice));

    expect((float) $row->period_credit)->toBe(200.0)
        ->and((float) $row->ending_balance)->toBe(-200.0);
});

it('excludes draft entries from the ledger', function () {
    ReportHelper::draftSale('2026-03-10', 7, 100, $this->income, $this->partner);
    $invoice = ReportHelper::postedSale('2026-03-10', 2, 100, $this->income, $this->partner);

    $data = ReportHelper::data(GeneralLedger::class, 'generalLedgerData', ['date_range' => '2026-03-01 - 2026-03-31']);

    $row = ReportHelper::rowBy($data['accounts'], 'id', ReportHelper::productAccountId($invoice));

    expect((float) $row->period_credit)->toBe(200.0);
});

it('drops the account when filtering on an unrelated journal', function () {
    $invoice = ReportHelper::postedSale('2026-03-10', 2, 100, $this->income, $this->partner, AccountHelper::saleJournal());

    $data = ReportHelper::data(GeneralLedger::class, 'generalLedgerData', [
        'date_range' => '2026-03-01 - 2026-03-31',
        'journals'   => [AccountHelper::generalJournal()->id],
    ]);

    expect(ReportHelper::rowBy($data['accounts'], 'id', ReportHelper::productAccountId($invoice)))->toBeNull();
});
