<?php

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

    $this->debitAccount = AccountHelper::account('expense');
    $this->creditAccount = AccountHelper::account('income');
});

it('reverses a posted entry so every account nets to zero', function () {
    $entry = AccountHelper::journalEntry();
    AccountHelper::entryLine($entry, $this->debitAccount, debit: 100, credit: 0);
    AccountHelper::entryLine($entry, $this->creditAccount, debit: 0, credit: 100);

    AccountHelper::post($entry);

    $reversal = AccountHelper::reverse($entry);

    $all = $entry->refresh()->lines->concat($reversal->refresh()->lines);

    foreach ($all->groupBy('account_id') as $lines) {
        expect((float) $lines->sum(fn ($line) => (float) $line->debit - (float) $line->credit))->toBe(0.0);
    }
});

it('swaps debit and credit on a reversed entry', function () {
    $entry = AccountHelper::journalEntry();
    AccountHelper::entryLine($entry, $this->debitAccount, debit: 100, credit: 0);
    AccountHelper::entryLine($entry, $this->creditAccount, debit: 0, credit: 100);

    AccountHelper::post($entry);

    $reversal = AccountHelper::reverse($entry);

    $line = $reversal->refresh()->lines->first(fn ($line) => (float) $line->balance === -100.0);

    expect((float) $line->debit)->toBe(0.0)
        ->and((float) $line->credit)->toBe(100.0);
});

it('keeps reversed debit and credit stable through a later recompute', function () {
    $entry = AccountHelper::journalEntry();
    AccountHelper::entryLine($entry, $this->debitAccount, debit: 100, credit: 0);
    AccountHelper::entryLine($entry, $this->creditAccount, debit: 0, credit: 100);

    AccountHelper::post($entry);

    $reversal = AccountHelper::reverse($entry);

    AccountHelper::compute($reversal);

    $line = $reversal->refresh()->lines->first(fn ($line) => (float) $line->balance === -100.0);

    expect((float) $line->debit)->toBe(0.0)
        ->and((float) $line->credit)->toBe(100.0);
});

it('reverses a storno entry so debit and credit follow the storno branch', function () {
    $entry = AccountHelper::journalEntry(overrides: ['is_storno' => true]);
    AccountHelper::entryLine($entry, $this->debitAccount, debit: 100, credit: 0);
    AccountHelper::entryLine($entry, $this->creditAccount, debit: 0, credit: 100);

    AccountHelper::post($entry);

    $reversal = AccountHelper::reverse($entry);

    $line = $reversal->refresh()->lines->first(fn ($line) => (float) $line->balance === -100.0);

    expect((float) $line->debit)->toBe(-100.0)
        ->and((float) $line->credit)->toBe(0.0);
});
