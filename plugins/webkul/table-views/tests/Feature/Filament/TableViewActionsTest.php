<?php

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Webkul\Account\Filament\Resources\InvoiceResource\Pages\ListInvoices;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Package;
use Webkul\TableViews\Models\TableView;
use Webkul\TableViews\Models\TableViewFavorite;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/FilamentHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('accounts');

    DB::table('plugins')->updateOrInsert(
        ['name' => 'accounts'],
        ['is_installed' => true, 'is_active' => true, 'updated_at' => now()],
    );

    Package::$plugins = Plugin::all()->keyBy('name');

    URL::resolveMissingNamedRoutesUsing(fn () => '#');
});

afterEach(fn () => Model::preventSilentlyDiscardingAttributes(false));

it('saves a favorite table view on the invoices list with strict model writes', function () {
    $user = FilamentHelper::actingAs(['view_any_account_invoice']);

    Model::preventSilentlyDiscardingAttributes();

    Livewire::test(ListInvoices::class)
        ->callAction('createTableView', data: [
            'name'        => 'Strict saved view',
            'is_favorite' => true,
            'is_public'   => false,
        ])
        ->assertHasNoActionErrors();

    $view = TableView::query()
        ->where('filterable_type', ListInvoices::class)
        ->where('name', 'Strict saved view')
        ->firstOrFail();

    $favorite = TableViewFavorite::query()
        ->where('view_type', 'saved')
        ->where('view_key', $view->id)
        ->where('user_id', $user->id)
        ->firstOrFail();

    expect($view->user_id)->toBe($user->id)
        ->and((bool) $favorite->is_favorite)->toBeTrue();
});

it('edits a saved table view on the invoices list with strict model writes', function () {
    $user = FilamentHelper::actingAs(['view_any_account_invoice']);

    $view = TableView::create([
        'name'            => 'Strict view before edit',
        'is_public'       => false,
        'filters'         => [],
        'filterable_type' => ListInvoices::class,
        'user_id'         => $user->id,
    ]);

    Model::preventSilentlyDiscardingAttributes();

    Livewire::test(ListInvoices::class)
        ->callAction('editTableView', data: [
            'name'        => 'Strict view after edit',
            'is_favorite' => true,
            'is_public'   => true,
        ], arguments: ['view_key' => $view->id])
        ->assertHasNoActionErrors();

    $favorite = TableViewFavorite::query()
        ->where('view_type', 'saved')
        ->where('view_key', $view->id)
        ->where('user_id', $user->id)
        ->firstOrFail();

    expect($view->refresh()->name)->toBe('Strict view after edit')
        ->and((bool) $view->is_public)->toBeTrue()
        ->and((bool) $favorite->is_favorite)->toBeTrue();
});
