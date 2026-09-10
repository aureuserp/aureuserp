<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Webkul\Accounting\Filament\Clusters\Reporting\Pages\ProfitLoss;
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

    FilamentHelper::actingAs(['page_accounting_profit_loss']);

    $this->income = AccountHelper::account('income');
    $this->expense = AccountHelper::account('expense');
    $this->partner = AccountHelper::partner();
});

it('renders the profit and loss page', function () {
    Livewire::test(ProfitLoss::class)->assertOk();
});

it('reports posted revenue in the revenue section', function () {
    ReportHelper::postedSale('2026-03-10', 2, 100, $this->income, $this->partner);

    $data = ReportHelper::data(ProfitLoss::class, 'profitLossData', ['date_range' => '2026-03-01 - 2026-03-31']);

    expect((float) $data['sections'][0]['total'])->toBe(200.0);
});

it('reports a profit equal to revenue when there are no expenses', function () {
    ReportHelper::postedSale('2026-03-10', 2, 100, $this->income, $this->partner);

    $data = ReportHelper::data(ProfitLoss::class, 'profitLossData', ['date_range' => '2026-03-01 - 2026-03-31']);

    expect((float) $data['net_income'])->toBe(200.0)
        ->and($data['is_profit'])->toBeTrue();
});

it('nets expenses against revenue', function () {
    ReportHelper::postedSale('2026-03-10', 2, 100, $this->income, $this->partner);
    ReportHelper::postedBill('2026-03-12', 1, 60, $this->expense, $this->partner);

    $data = ReportHelper::data(ProfitLoss::class, 'profitLossData', ['date_range' => '2026-03-01 - 2026-03-31']);

    $revenue = (float) $data['sections'][0]['total'];
    $expenses = (float) $data['sections'][1]['total'];

    expect($revenue)->toBe(200.0)
        ->and($expenses)->toBe(60.0)
        ->and((float) $data['net_income'])->toBe($revenue - $expenses);
});

it('reports a loss when expenses exceed revenue', function () {
    ReportHelper::postedSale('2026-03-10', 1, 50, $this->income, $this->partner);
    ReportHelper::postedBill('2026-03-12', 1, 300, $this->expense, $this->partner);

    $data = ReportHelper::data(ProfitLoss::class, 'profitLossData', ['date_range' => '2026-03-01 - 2026-03-31']);

    expect((float) $data['net_income'])->toBe(-250.0)
        ->and($data['is_profit'])->toBeFalse();
});

it('excludes revenue posted outside the period', function () {
    ReportHelper::postedSale('2026-03-10', 2, 100, $this->income, $this->partner);
    ReportHelper::postedSale('2026-06-10', 9, 100, $this->income, $this->partner);

    $data = ReportHelper::data(ProfitLoss::class, 'profitLossData', ['date_range' => '2026-03-01 - 2026-03-31']);

    expect((float) $data['sections'][0]['total'])->toBe(200.0);
});

it('excludes draft revenue that was never posted', function () {
    ReportHelper::draftSale('2026-03-10', 7, 100, $this->income, $this->partner);
    ReportHelper::postedSale('2026-03-10', 2, 100, $this->income, $this->partner);

    $data = ReportHelper::data(ProfitLoss::class, 'profitLossData', ['date_range' => '2026-03-01 - 2026-03-31']);

    expect((float) $data['sections'][0]['total'])->toBe(200.0);
});

it('drops revenue when filtering on an unrelated journal', function () {
    ReportHelper::postedSale('2026-03-10', 2, 100, $this->income, $this->partner, AccountHelper::saleJournal());

    $data = ReportHelper::data(ProfitLoss::class, 'profitLossData', [
        'date_range' => '2026-03-01 - 2026-03-31',
        'journals'   => [AccountHelper::generalJournal()->id],
    ]);

    expect((float) $data['sections'][0]['total'])->toBe(0.0);
});
