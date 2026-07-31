<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Webkul\Accounting\Filament\Clusters\Reporting\Pages\BalanceSheet;
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

    FilamentHelper::actingAs(['page_accounting_balance_sheet']);

    $this->income = AccountHelper::account('income');
    $this->expense = AccountHelper::account('expense');
    $this->partner = AccountHelper::partner();
});

it('renders the balance sheet page', function () {
    Livewire::test(BalanceSheet::class)->assertOk();
});

it('keeps assets equal to liabilities plus equity', function () {
    ReportHelper::postedSale('2026-03-10', 2, 100, $this->income, $this->partner);

    $data = ReportHelper::data(BalanceSheet::class, 'balanceSheetData', ['date_range' => '2026-03-01 - 2026-03-31']);

    expect((float) $data['sections'][0]['total'])->toBe((float) $data['grand_total']);
});

it('reports the receivable as an asset', function () {
    ReportHelper::postedSale('2026-03-10', 2, 100, $this->income, $this->partner);

    $data = ReportHelper::data(BalanceSheet::class, 'balanceSheetData', ['date_range' => '2026-03-01 - 2026-03-31']);

    expect((float) $data['sections'][0]['total'])->toBe(200.0);
});

it('carries current year revenue into equity', function () {
    ReportHelper::postedSale('2026-03-10', 2, 100, $this->income, $this->partner);

    $data = ReportHelper::data(BalanceSheet::class, 'balanceSheetData', ['date_range' => '2026-03-01 - 2026-03-31']);

    expect((float) $data['sections'][2]['total'])->toBe(200.0);
});

it('stays balanced once expenses are posted too', function () {
    ReportHelper::postedSale('2026-03-10', 2, 100, $this->income, $this->partner);
    ReportHelper::postedBill('2026-03-12', 1, 60, $this->expense, $this->partner);

    $data = ReportHelper::data(BalanceSheet::class, 'balanceSheetData', ['date_range' => '2026-03-01 - 2026-03-31']);

    expect((float) $data['sections'][0]['total'])->toBe((float) $data['grand_total']);
});

it('excludes draft entries from the balance sheet', function () {
    ReportHelper::draftSale('2026-03-10', 7, 100, $this->income, $this->partner);
    ReportHelper::postedSale('2026-03-10', 2, 100, $this->income, $this->partner);

    $data = ReportHelper::data(BalanceSheet::class, 'balanceSheetData', ['date_range' => '2026-03-01 - 2026-03-31']);

    expect((float) $data['sections'][0]['total'])->toBe(200.0);
});
