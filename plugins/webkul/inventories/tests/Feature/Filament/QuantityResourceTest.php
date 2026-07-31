<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\QuantityResource\Pages\ManageQuantities;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Package;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/FilamentHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/CompanyHelper.php';
require_once __DIR__.'/../../Helpers/InventoryHelper.php';

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

    InventoryHelper::enableLocations();

    $this->warehouse = InventoryHelper::warehouse();
    $this->product = InventoryHelper::product();
    $this->stock = $this->warehouse->lotStockLocation;
});

it('forbids listing inventory quantities without permission', function () {
    FilamentHelper::actingAs([]);

    Livewire::test(ManageQuantities::class)->assertForbidden();
});

it('lists inventory quantities for authorized users', function () {
    FilamentHelper::actingAs(['view_any_inventory_quantity']);

    Livewire::test(ManageQuantities::class)->assertOk();
});

it('lists a quant with its adjustment columns for authorized users', function () {
    FilamentHelper::actingAs(['view_any_inventory_quantity']);

    $quant = InventoryHelper::stockUp($this->product, $this->stock, 10);

    Livewire::test(ManageQuantities::class)
        ->assertOk()
        ->assertCanSeeTableRecords([$quant])
        ->assertCanRenderTableColumn('product.name')
        ->assertCanRenderTableColumn('location.full_name')
        ->assertCanRenderTableColumn('quantity')
        ->assertCanRenderTableColumn('counted_quantity');
});

it('finds a quant by searching its product name', function () {
    FilamentHelper::actingAs(['view_any_inventory_quantity']);

    $quant = InventoryHelper::stockUp($this->product, $this->stock, 10);

    Livewire::test(ManageQuantities::class)
        ->searchTable($this->product->name)
        ->assertCanSeeTableRecords([$quant]);
});

it('shows the on hand quantity of a stocked up quant', function () {
    FilamentHelper::actingAs(['view_any_inventory_quantity']);

    $quant = InventoryHelper::stockUp($this->product, $this->stock, 10);

    Livewire::test(ManageQuantities::class)
        ->assertOk()
        ->assertTableColumnStateSet('quantity', 10, record: $quant);
});

it('exposes the inventory adjustment preset views', function () {
    FilamentHelper::actingAs(['view_any_inventory_quantity']);

    $views = Livewire::test(ManageQuantities::class)
        ->assertOk()
        ->instance()
        ->getPresetTableViews();

    expect(array_keys($views))->toBe([
        'internal_locations',
        'transit_locations',
        'on_hand',
        'to_count',
        'to_apply',
    ]);
});

it('lists a quant to a user of the owning company', function () {
    $quant = InventoryHelper::stockUp($this->product, $this->stock, 10);

    FilamentHelper::actingAsCompanyUser(InventoryHelper::company(), ['view_any_inventory_quantity']);

    Livewire::test(ManageQuantities::class)
        ->assertOk()
        ->assertCanSeeTableRecords([$quant]);
});

it('hides a quant owned by another company from the list', function () {
    $quant = InventoryHelper::stockUp($this->product, $this->stock, 10);

    FilamentHelper::actingAsCompanyUser(CompanyHelper::company(), ['view_any_inventory_quantity']);

    Livewire::test(ManageQuantities::class)
        ->assertOk()
        ->assertCanNotSeeTableRecords([$quant]);
});
