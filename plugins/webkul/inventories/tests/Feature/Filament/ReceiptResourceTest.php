<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\ReceiptResource\Pages\CreateReceipt;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\ReceiptResource\Pages\ListReceipts;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Package;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/FilamentHelper.php';

beforeEach(function () {
    foreach (['products', 'inventories'] as $plugin) {
        TestBootstrapHelper::ensurePluginInstalled($plugin);

        DB::table('plugins')->updateOrInsert(
            ['name' => $plugin],
            ['is_installed' => true, 'is_active' => true, 'updated_at' => now()],
        );
    }

    Package::$plugins = Plugin::all()->keyBy('name');

    URL::resolveMissingNamedRoutesUsing(fn () => '#');
});

it('forbids listing receipts without permission', function () {
    FilamentHelper::actingAs([]);

    Livewire::test(ListReceipts::class)->assertForbidden();
});

it('lists receipts for authorized users', function () {
    FilamentHelper::actingAs(['view_any_inventory_receipt']);

    Livewire::test(ListReceipts::class)->assertOk();
});

it('renders the receipt create page', function () {
    FilamentHelper::actingAs(['view_any_inventory_receipt', 'create_inventory_receipt']);

    Livewire::test(CreateReceipt::class)->assertOk();
});
