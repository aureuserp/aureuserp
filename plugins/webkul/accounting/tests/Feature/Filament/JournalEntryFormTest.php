<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Webkul\Account\Enums\DisplayType;
use Webkul\Accounting\Filament\Clusters\Accounting\Resources\JournalEntryResource\Pages\CreateJournalEntry;
use Webkul\Accounting\Filament\Clusters\Accounting\Resources\JournalEntryResource\Pages\EditJournalEntry;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Package;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/FilamentHelper.php';
require_once __DIR__.'/../../../../accounts/tests/Helpers/AccountHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('accounting');

    DB::table('plugins')->updateOrInsert(
        ['name' => 'accounting'],
        ['is_installed' => true, 'is_active' => true, 'updated_at' => now()],
    );

    Package::$plugins = Plugin::all()->keyBy('name');

    URL::resolveMissingNamedRoutesUsing(fn () => '#');

    $this->debitAccount = AccountHelper::account('expense');
    $this->creditAccount = AccountHelper::account('income');
});

it('renders the journal entry create page', function () {
    FilamentHelper::actingAs(['view_any_accounting_journal::entry', 'create_accounting_journal::entry']);

    Livewire::test(CreateJournalEntry::class)->assertOk();
});

it('renders the edit form for an entry that includes auto-generated tax and balancing lines', function () {
    FilamentHelper::actingAs(['view_any_accounting_journal::entry', 'update_accounting_journal::entry']);

    $entry = AccountHelper::journalEntry();

    $normalLine = AccountHelper::entryLine($entry, $this->debitAccount, debit: 100, credit: 0);

    $taxLine = AccountHelper::entryLine($entry, $this->debitAccount, debit: 15, credit: 0);
    $taxLine->update(['name' => 'Tax: VAT', 'display_type' => DisplayType::TAX]);

    $balancingLine = AccountHelper::entryLine($entry, $this->creditAccount, debit: 0, credit: 115);
    $balancingLine->update(['name' => 'Automatic Balancing']);

    Livewire::test(EditJournalEntry::class, ['record' => $entry->id])
        ->assertOk()
        ->assertFormComponentActionVisible('lines', 'delete', ['item' => 'record-'.$normalLine->id])
        ->assertFormComponentActionHidden('lines', 'delete', ['item' => 'record-'.$taxLine->id])
        ->assertFormComponentActionHidden('lines', 'delete', ['item' => 'record-'.$balancingLine->id]);
});

it('recalculates the balancing line when a line amount changes', function () {
    FilamentHelper::actingAs(['view_any_accounting_journal::entry', 'update_accounting_journal::entry']);

    $journal = AccountHelper::generalJournal();
    $journal->update(['suspense_account_id' => $this->creditAccount->id]);

    $entry = AccountHelper::journalEntry($journal);
    $line = AccountHelper::entryLine($entry, $this->debitAccount, debit: 40, credit: 0);

    $component = Livewire::test(EditJournalEntry::class, ['record' => $entry->id])
        ->assertOk()
        ->set('data.lines.record-'.$line->id.'.debit', 100)
        ->assertHasNoErrors();

    $names = collect($component->get('data.lines'))->pluck('name');

    expect($names)->toContain('Automatic Balancing');
});
