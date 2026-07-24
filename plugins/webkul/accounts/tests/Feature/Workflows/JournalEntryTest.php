<?php

use Webkul\Account\Enums\MoveState;
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

it('posts a balanced manual journal entry', function () {
    $entry = AccountHelper::journalEntry();
    AccountHelper::entryLine($entry, $this->debitAccount, debit: 100, credit: 0);
    AccountHelper::entryLine($entry, $this->creditAccount, debit: 0, credit: 100);

    AccountHelper::post($entry);

    expect($entry->refresh()->state)->toBe(MoveState::POSTED);
});

it('keeps a manual journal entry balanced so total debit equals total credit', function () {
    $entry = AccountHelper::journalEntry();
    AccountHelper::entryLine($entry, $this->debitAccount, debit: 100, credit: 0);
    AccountHelper::entryLine($entry, $this->creditAccount, debit: 0, credit: 100);

    AccountHelper::post($entry);

    $lines = $entry->refresh()->lines;

    expect((float) $lines->sum(fn ($l) => (float) $l->debit))->toBe(100.0)
        ->and((float) $lines->sum(fn ($l) => (float) $l->credit))->toBe(100.0);
});

it('refuses to post a manual journal entry with no lines', function () {
    $entry = AccountHelper::journalEntry();

    expect(fn () => AccountHelper::post($entry))
        ->toThrow(Exception::class, __('accounts::account-manager.post-action-validate.lines-required'));
});
