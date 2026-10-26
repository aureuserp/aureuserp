<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Webkul\Account\Enums\DisplayType;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Models\Move;
use Webkul\Accounting\Filament\Clusters\Accounting\Resources\JournalEntryResource\Pages\CreateJournalEntry;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Package;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/FilamentHelper.php';
require_once __DIR__.'/../../../../accounts/tests/Helpers/AccountHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('accounting');

    DB::table('plugins')->whereIn('name', ['accounts', 'accounting'])->update([
        'is_installed' => true,
        'is_active'    => true,
        'updated_at'   => now(),
    ]);

    Package::$plugins = Plugin::all()->keyBy('name');

    URL::resolveMissingNamedRoutesUsing(fn () => '#');

    FilamentHelper::actingAs(['create_accounting_journal::entry', 'view_any_accounting_journal::entry']);
});

function createTaxedJournalEntry(float $amount, int $taxId, bool $onCredit = false): Move
{
    $journal = AccountHelper::generalJournal();
    $receivable = AccountHelper::account('receivable');
    $currencyId = AccountHelper::currency()->id;

    $component = Livewire::test(CreateJournalEntry::class)
        ->fillForm(['journal_id' => $journal->id, 'date' => now()]);

    $component->set('data.lines', [
        'a' => [
            'account_id'               => $receivable->id,
            'partner_id'               => null,
            'name'                     => 'Receivable',
            'amount_currency'          => $onCredit ? -$amount : $amount,
            'currency_id'              => $currencyId,
            'taxes'                    => [],
            'debit'                    => $onCredit ? 0 : $amount,
            'credit'                   => $onCredit ? $amount : 0,
            'discount_amount_currency' => 0,
            'display_type'             => null,
        ],
    ]);

    $component->set('data.lines.a.taxes', [$taxId]);

    $component->call('create')->assertHasNoFormErrors();

    return Move::query()->where('move_type', MoveType::ENTRY)->latest('id')->firstOrFail();
}

function createTaxFreeJournalEntry(float $amount): Move
{
    $journal = AccountHelper::generalJournal();
    $receivable = AccountHelper::account('receivable');
    $income = AccountHelper::account('income');
    $currencyId = AccountHelper::currency()->id;

    $component = Livewire::test(CreateJournalEntry::class)
        ->fillForm(['journal_id' => $journal->id, 'date' => now()]);

    $component->set('data.lines', [
        'a' => [
            'account_id'               => $receivable->id,
            'partner_id'               => null,
            'name'                     => 'Receivable',
            'amount_currency'          => $amount,
            'currency_id'              => $currencyId,
            'taxes'                    => [],
            'debit'                    => $amount,
            'credit'                   => 0,
            'discount_amount_currency' => 0,
            'display_type'             => null,
        ],
        'b' => [
            'account_id'               => $income->id,
            'partner_id'               => null,
            'name'                     => 'Income',
            'amount_currency'          => -$amount,
            'currency_id'              => $currencyId,
            'taxes'                    => [],
            'debit'                    => 0,
            'credit'                   => $amount,
            'discount_amount_currency' => 0,
            'display_type'             => null,
        ],
    ]);

    $component->call('create')->assertHasNoFormErrors();

    return Move::query()->where('move_type', MoveType::ENTRY)->latest('id')->firstOrFail();
}

it('stores a balance equal to debit minus credit on every journal item including tax lines', function () {
    $tax = AccountHelper::taxWithAccounts(15);

    $entry = createTaxedJournalEntry(200, $tax->id)->refresh();

    foreach ($entry->lines as $line) {
        expect((float) $line->balance)->toBe((float) $line->debit - (float) $line->credit);
    }

    expect($entry->lines->firstWhere('display_type', DisplayType::TAX))->not->toBeNull()
        ->and((float) $entry->lines->sum('debit'))->toBe((float) $entry->lines->sum('credit'))
        ->and((float) $entry->lines->sum('balance'))->toBe(0.0);
});

it('stores a balance equal to debit minus credit on a tax-free manual entry', function () {
    $entry = createTaxFreeJournalEntry(100)->refresh();

    expect($entry->lines->firstWhere('display_type', DisplayType::TAX))->toBeNull();

    foreach ($entry->lines as $line) {
        expect((float) $line->balance)->toBe((float) $line->debit - (float) $line->credit);
    }

    expect((float) $entry->lines->sum('debit'))->toBe((float) $entry->lines->sum('credit'))
        ->and((float) $entry->lines->sum('balance'))->toBe(0.0);
});

it('stores a negative balance on a credit-side taxed line and keeps the entry balanced', function () {
    $tax = AccountHelper::taxWithAccounts(15);

    $entry = createTaxedJournalEntry(200, $tax->id, onCredit: true)->refresh();

    $taxLine = $entry->lines->firstWhere('display_type', DisplayType::TAX);

    expect($taxLine)->not->toBeNull()
        ->and((float) $taxLine->credit)->toBeGreaterThan(0.0)
        ->and((float) $taxLine->balance)->toBe((float) $taxLine->debit - (float) $taxLine->credit);

    foreach ($entry->lines as $line) {
        expect((float) $line->balance)->toBe((float) $line->debit - (float) $line->credit);
    }

    expect((float) $entry->lines->sum('debit'))->toBe((float) $entry->lines->sum('credit'))
        ->and((float) $entry->lines->sum('balance'))->toBe(0.0);
});

it('persists the correct balance on every journal item after posting a taxed entry', function () {
    $tax = AccountHelper::taxWithAccounts(15);

    $entry = AccountHelper::post(createTaxedJournalEntry(100, $tax->id))->refresh();

    expect($entry->state)->toBe(MoveState::POSTED);

    foreach ($entry->lines as $line) {
        expect((float) $line->balance)->toBe((float) $line->debit - (float) $line->credit);
    }

    expect((float) $entry->lines->sum('debit'))->toBe((float) $entry->lines->sum('credit'))
        ->and((float) $entry->lines->sum('balance'))->toBe(0.0);
});

it('keeps balance consistent and the totals zero after reversing a taxed entry', function () {
    $tax = AccountHelper::taxWithAccounts(15);

    $entry = AccountHelper::post(createTaxedJournalEntry(200, $tax->id));

    $reversal = AccountHelper::reverse($entry)->refresh();

    $originals = $entry->refresh()->lines->values();
    $reversed = $reversal->lines->values();

    expect($reversed)->toHaveCount($originals->count());

    foreach ($originals as $index => $original) {
        $line = $reversed[$index];

        expect((float) $line->debit)->toBe((float) $original->credit)
            ->and((float) $line->credit)->toBe((float) $original->debit)
            ->and((float) $line->balance)->toBe(-(float) $original->balance)
            ->and((float) $line->balance)->toBe((float) $line->debit - (float) $line->credit);
    }

    expect((float) $reversal->lines->sum('debit'))->toBe((float) $reversal->lines->sum('credit'))
        ->and((float) $reversal->lines->sum('balance'))->toBe(0.0);
});
