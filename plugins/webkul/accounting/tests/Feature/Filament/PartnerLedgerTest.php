<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Webkul\Accounting\Filament\Clusters\Reporting\Pages\PartnerLedger;
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

    FilamentHelper::actingAs(['page_accounting_partner_ledger']);

    $this->income = AccountHelper::account('income');
    $this->expense = AccountHelper::account('expense');
    $this->partner = AccountHelper::partner();
});

it('renders the partner ledger page', function () {
    Livewire::test(PartnerLedger::class)->assertOk();
});

it('reports the partner movement for the period', function () {
    ReportHelper::postedSale('2026-03-10', 2, 100, $this->income, $this->partner);

    $data = ReportHelper::data(PartnerLedger::class, 'partnerLedgerData', ['date_range' => '2026-03-01 - 2026-03-31']);

    $row = ReportHelper::rowBy($data['partners'], 'id', $this->partner->id);

    expect($row)->not->toBeNull()
        ->and((float) $row->period_debit)->toBe(200.0)
        ->and((float) $row->ending_balance)->toBe(200.0);
});

it('opens the period with the partner balance carried forward', function () {
    ReportHelper::postedSale('2026-01-15', 1, 400, $this->income, $this->partner);
    ReportHelper::postedSale('2026-03-10', 2, 100, $this->income, $this->partner);

    $data = ReportHelper::data(PartnerLedger::class, 'partnerLedgerData', ['date_range' => '2026-03-01 - 2026-03-31']);

    $row = ReportHelper::rowBy($data['partners'], 'id', $this->partner->id);

    expect((float) $row->opening_balance)->toBe(400.0)
        ->and((float) $row->period_debit)->toBe(200.0)
        ->and((float) $row->ending_balance)->toBe(600.0);
});

it('excludes entries posted after the period', function () {
    ReportHelper::postedSale('2026-03-10', 2, 100, $this->income, $this->partner);
    ReportHelper::postedSale('2026-06-10', 9, 100, $this->income, $this->partner);

    $data = ReportHelper::data(PartnerLedger::class, 'partnerLedgerData', ['date_range' => '2026-03-01 - 2026-03-31']);

    $row = ReportHelper::rowBy($data['partners'], 'id', $this->partner->id);

    expect((float) $row->period_debit)->toBe(200.0)
        ->and((float) $row->ending_balance)->toBe(200.0);
});

it('excludes draft entries from the partner ledger', function () {
    ReportHelper::draftSale('2026-03-10', 7, 100, $this->income, $this->partner);
    ReportHelper::postedSale('2026-03-10', 2, 100, $this->income, $this->partner);

    $data = ReportHelper::data(PartnerLedger::class, 'partnerLedgerData', ['date_range' => '2026-03-01 - 2026-03-31']);

    $row = ReportHelper::rowBy($data['partners'], 'id', $this->partner->id);

    expect((float) $row->period_debit)->toBe(200.0);
});

it('keeps only the selected partner when filtering', function () {
    $other = AccountHelper::partner();

    ReportHelper::postedSale('2026-03-10', 2, 100, $this->income, $this->partner);
    ReportHelper::postedSale('2026-03-12', 5, 100, $this->income, $other);

    $data = ReportHelper::data(PartnerLedger::class, 'partnerLedgerData', [
        'date_range' => '2026-03-01 - 2026-03-31',
        'partners'   => [$this->partner->id],
    ]);

    expect(ReportHelper::rowBy($data['partners'], 'id', $this->partner->id))->not->toBeNull()
        ->and(ReportHelper::rowBy($data['partners'], 'id', $other->id))->toBeNull();
});

it('drops the partner when filtering on an unrelated journal', function () {
    ReportHelper::postedSale('2026-03-10', 2, 100, $this->income, $this->partner, AccountHelper::saleJournal());

    $data = ReportHelper::data(PartnerLedger::class, 'partnerLedgerData', [
        'date_range' => '2026-03-01 - 2026-03-31',
        'journals'   => [AccountHelper::generalJournal()->id],
    ]);

    expect(ReportHelper::rowBy($data['partners'], 'id', $this->partner->id))->toBeNull();
});
