<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Filament\Resources\CreditNoteResource\Pages\CreateCreditNote;
use Webkul\Account\Filament\Resources\CreditNoteResource\Pages\EditCreditNote;
use Webkul\Account\Filament\Resources\CreditNoteResource\Pages\ListCreditNotes;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Package;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/FilamentHelper.php';
require_once __DIR__.'/../../Helpers/AccountHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('accounts');

    DB::table('plugins')->updateOrInsert(
        ['name' => 'accounts'],
        ['is_installed' => true, 'is_active' => true, 'updated_at' => now()],
    );

    Package::$plugins = Plugin::all()->keyBy('name');

    URL::resolveMissingNamedRoutesUsing(fn () => '#');
});

it('forbids listing credit notes without permission', function () {
    FilamentHelper::actingAs([]);

    Livewire::test(ListCreditNotes::class)->assertForbidden();
});

it('lists credit notes with their key columns for authorized users', function () {
    FilamentHelper::actingAs(['view_any_account_credit::note']);

    Livewire::test(ListCreditNotes::class)
        ->assertOk()
        ->assertCanRenderTableColumn('name')
        ->assertCanRenderTableColumn('state');
});

it('renders the credit note create page', function () {
    FilamentHelper::actingAs(['view_any_account_credit::note', 'create_account_credit::note']);

    Livewire::test(CreateCreditNote::class)->assertOk();
});

it('renders the credit note edit page for a draft record', function () {
    FilamentHelper::actingAs(['view_any_account_credit::note', 'update_account_credit::note']);

    $creditNote = AccountHelper::invoice(MoveType::OUT_REFUND);

    Livewire::test(EditCreditNote::class, ['record' => $creditNote->id])
        ->assertOk();
});
