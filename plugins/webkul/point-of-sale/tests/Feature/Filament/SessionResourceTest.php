<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Package;
use Webkul\PointOfSale\Enums\SessionState;
use Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources\OrderResource\Pages\ListOrders;
use Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources\SessionResource\Pages\ListSessions;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/FilamentHelper.php';
require_once __DIR__.'/../../../../inventories/tests/Helpers/InventoryHelper.php';
require_once __DIR__.'/../../Helpers/PosHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('inventories');
    TestBootstrapHelper::ensurePluginInstalled('accounts');
    TestBootstrapHelper::ensurePluginInstalled('point-of-sale');

    DB::table('plugins')->whereIn('name', ['inventories', 'accounts', 'point-of-sale'])->update([
        'is_installed' => true,
        'is_active'    => true,
        'updated_at'   => now(),
    ]);

    Package::$plugins = Plugin::all()->keyBy('name');

    URL::resolveMissingNamedRoutesUsing(fn () => '#');

    FilamentHelper::actingAs([
        'view_any_point_of_sale_session',
        'view_point_of_sale_session',
        'view_any_point_of_sale_order',
        'view_point_of_sale_order',
    ]);

    $this->warehouse = InventoryHelper::warehouse();
});

it('renders the session list page', function () {
    Livewire::test(ListSessions::class)->assertOk();
});

it('lists an open session', function () {
    $session = PosHelper::openSession($this->warehouse);

    Livewire::test(ListSessions::class)
        ->assertCanSeeTableRecords([$session]);
});

it('filters sessions by state', function () {
    $session = PosHelper::openSession($this->warehouse);

    Livewire::test(ListSessions::class)
        ->filterTable('state', SessionState::OPENED->value)
        ->assertCanSeeTableRecords([$session]);
});

it('renders the order list page', function () {
    Livewire::test(ListOrders::class)->assertOk();
});
