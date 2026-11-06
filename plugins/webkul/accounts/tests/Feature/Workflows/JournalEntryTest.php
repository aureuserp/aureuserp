<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Webkul\Account\Enums\DisplayType;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Models\MoveLine;
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

it('refuses to post an unbalanced manual journal entry', function () {
    $entry = AccountHelper::journalEntry();
    AccountHelper::entryLine($entry, $this->debitAccount, debit: 100, credit: 0);
    AccountHelper::entryLine($entry, $this->creditAccount, debit: 0, credit: 60);

    expect(fn () => AccountHelper::post($entry))
        ->toThrow(Exception::class, __('accounts::account-manager.post-action-validate.unbalanced'));

    expect($entry->refresh()->state)->toBe(MoveState::DRAFT);
});

it('keeps the account given on each line when the journal has a default account', function () {
    $journal = AccountHelper::generalJournal();
    $entry = AccountHelper::journalEntry($journal);

    $debit = MoveLine::create(['move_id' => $entry->id, 'account_id' => $this->debitAccount->id, 'debit' => 100, 'credit' => 0]);
    $credit = MoveLine::create(['move_id' => $entry->id, 'account_id' => $this->creditAccount->id, 'debit' => 0, 'credit' => 100]);

    expect($debit->refresh()->account_id)->toBe($this->debitAccount->id)
        ->and($credit->refresh()->account_id)->toBe($this->creditAccount->id)
        ->and($journal->default_account_id)->not->toBe($this->debitAccount->id);
});

it('keeps the account given on each cost of goods sold line', function () {
    $entry = AccountHelper::journalEntry();
    $stockAccount = AccountHelper::account('expense');

    foreach ([
        [$this->debitAccount, 60, 0],
        [$stockAccount, 0, 60],
        [$this->debitAccount, 40, 0],
        [$stockAccount, 0, 40],
    ] as [$account, $debit, $credit]) {
        MoveLine::create([
            'move_id'      => $entry->id,
            'display_type' => DisplayType::COGS,
            'account_id'   => $account->id,
            'currency_id'  => $entry->currency_id,
            'debit'        => $debit,
            'credit'       => $credit,
        ]);
    }

    $accounts = $entry->refresh()->lines->sortBy('id')->pluck('account_id')->all();

    expect($accounts)->toBe([$this->debitAccount->id, $stockAccount->id, $this->debitAccount->id, $stockAccount->id]);
});
