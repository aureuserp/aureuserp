<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Webkul\Accounting\Filament\Clusters\Reporting\Pages\AgedReceivable;
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

    FilamentHelper::actingAs(['page_accounting_aged_receivable']);

    $this->income = AccountHelper::account('income');
    $this->expense = AccountHelper::account('expense');
    $this->partner = AccountHelper::partner();
});

it('renders the aged receivable page', function () {
    Livewire::test(AgedReceivable::class)->assertOk();
});

it('places an invoice that is not yet due in the at-date bucket', function () {
    ReportHelper::postedSaleDueOn('2026-03-01', '2026-04-15', 2, 100, $this->income, $this->partner);

    $data = ReportHelper::data(AgedReceivable::class, 'agedReceivableData', ['as_of_date' => '2026-03-31']);

    $row = $data['partners'][$this->partner->id] ?? null;

    expect($row)->not->toBeNull()
        ->and((float) $row['at_date'])->toBe(200.0)
        ->and((float) $row['total'])->toBe(200.0);
});

it('places an invoice overdue by less than one period in the first bucket', function () {
    ReportHelper::postedSaleDueOn('2026-03-01', '2026-03-20', 2, 100, $this->income, $this->partner);

    $data = ReportHelper::data(AgedReceivable::class, 'agedReceivableData', [
        'as_of_date' => '2026-03-31',
        'period'     => 30,
    ]);

    $row = $data['partners'][$this->partner->id];

    expect((float) $row['period_1'])->toBe(200.0)
        ->and((float) $row['at_date'])->toBe(0.0);
});

it('places an invoice overdue by more than one period in the second bucket', function () {
    ReportHelper::postedSaleDueOn('2026-01-01', '2026-02-10', 2, 100, $this->income, $this->partner);

    $data = ReportHelper::data(AgedReceivable::class, 'agedReceivableData', [
        'as_of_date' => '2026-03-31',
        'period'     => 30,
    ]);

    $row = $data['partners'][$this->partner->id];

    expect((float) $row['period_2'])->toBe(200.0)
        ->and((float) $row['period_1'])->toBe(0.0);
});

it('totals every bucket for a partner', function () {
    ReportHelper::postedSaleDueOn('2026-03-01', '2026-04-15', 1, 100, $this->income, $this->partner);
    ReportHelper::postedSaleDueOn('2026-03-01', '2026-03-20', 1, 50, $this->income, $this->partner);

    $data = ReportHelper::data(AgedReceivable::class, 'agedReceivableData', [
        'as_of_date' => '2026-03-31',
        'period'     => 30,
    ]);

    $row = $data['partners'][$this->partner->id];

    $buckets = (float) $row['at_date'] + (float) $row['period_1'] + (float) $row['period_2']
        + (float) $row['period_3'] + (float) $row['period_4'] + (float) $row['older'];

    expect((float) $row['total'])->toBe(150.0)
        ->and($buckets)->toBe((float) $row['total']);
});

it('excludes draft invoices from the ageing', function () {
    ReportHelper::draftSale('2026-03-01', 9, 100, $this->income, $this->partner);
    ReportHelper::postedSaleDueOn('2026-03-01', '2026-04-15', 2, 100, $this->income, $this->partner);

    $data = ReportHelper::data(AgedReceivable::class, 'agedReceivableData', ['as_of_date' => '2026-03-31']);

    expect((float) $data['partners'][$this->partner->id]['total'])->toBe(200.0);
});

it('keeps only the selected partner when filtering', function () {
    $other = AccountHelper::partner();

    ReportHelper::postedSaleDueOn('2026-03-01', '2026-04-15', 2, 100, $this->income, $this->partner);
    ReportHelper::postedSaleDueOn('2026-03-01', '2026-04-15', 5, 100, $this->income, $other);

    $data = ReportHelper::data(AgedReceivable::class, 'agedReceivableData', [
        'as_of_date' => '2026-03-31',
        'partners'   => [$this->partner->id],
    ]);

    expect($data['partners'])->toHaveKey($this->partner->id)
        ->and($data['partners'])->not->toHaveKey($other->id);
});
