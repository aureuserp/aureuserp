<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Package;
use Webkul\PointOfSale\Filament\Admin\Clusters\Configurations\Resources\ConfigResource\Pages\ListConfigs;
use Webkul\PointOfSale\Filament\Admin\Clusters\Configurations\Resources\PaymentMethodResource\Pages\ListPaymentMethods;

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
        'view_any_point_of_sale_config',
        'view_point_of_sale_config',
        'create_point_of_sale_config',
        'update_point_of_sale_config',
        'view_any_point_of_sale_payment::method',
    ]);

    $this->warehouse = InventoryHelper::warehouse();
});

it('renders the terminal list page', function () {
    Livewire::test(ListConfigs::class)->assertOk();
});

it('lists an existing terminal', function () {
    $config = PosHelper::config($this->warehouse);

    Livewire::test(ListConfigs::class)
        ->assertCanSeeTableRecords([$config]);
});

it('searches a terminal by name', function () {
    $config = PosHelper::config($this->warehouse, ['name' => 'Bandra Counter']);

    Livewire::test(ListConfigs::class)
        ->searchTable('Bandra')
        ->assertCanSeeTableRecords([$config]);
});

it('renders the payment method list page', function () {
    Livewire::test(ListPaymentMethods::class)->assertOk();
});

it('lists a cash payment method', function () {
    $cash = PosHelper::cashMethod();

    Livewire::test(ListPaymentMethods::class)
        ->assertCanSeeTableRecords([$cash]);
});
