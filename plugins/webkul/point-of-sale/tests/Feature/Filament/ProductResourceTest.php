<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Package;
use Webkul\PointOfSale\Filament\Admin\Clusters\Products\Resources\ProductResource\Pages\CreateProduct;
use Webkul\PointOfSale\Filament\Admin\Clusters\Products\Resources\ProductResource\Pages\ListProducts;
use Webkul\PointOfSale\Filament\Admin\Clusters\Products\Resources\ProductResource\Pages\ViewProduct;
use Webkul\Product\Enums\ProductType;

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
        'view_any_point_of_sale_product',
        'view_point_of_sale_product',
        'create_point_of_sale_product',
        'update_point_of_sale_product',
    ]);
});

it('renders the point of sale product list page', function () {
    Livewire::test(ListProducts::class)->assertOk();
});

it('defaults to the pos tab listing only products available in the point of sale', function () {
    $available = InventoryHelper::product(['name' => 'Counter Croissant', 'price' => 2.5]);
    $hidden = InventoryHelper::product(['name' => 'Warehouse Pallet', 'price' => 120.0]);

    DB::table('products_products')->where('id', $available->id)->update(['available_in_pos' => true]);

    Livewire::test(ListProducts::class)
        ->assertSet('activeTableView', 'pos_products')
        ->assertCanSeeTableRecords([$available])
        ->assertCanNotSeeTableRecords([$hidden]);
});

it('lists products not available in the point of sale outside the pos tab', function () {
    $hidden = InventoryHelper::product(['name' => 'Warehouse Pallet', 'price' => 120.0]);

    Livewire::test(ListProducts::class)
        ->set('activeTableView', 'goods_products')
        ->assertCanSeeTableRecords([$hidden]);
});

it('opens a product that is not available in the point of sale', function () {
    $hidden = InventoryHelper::product(['name' => 'Warehouse Pallet', 'price' => 120.0]);

    Livewire::test(ViewProduct::class, ['record' => $hidden->getRouteKey()])
        ->assertOk();
});

it('prefills the product defaults on the point of sale create page', function () {
    Livewire::test(CreateProduct::class)
        ->assertOk()
        ->assertSchemaStateSet([
            'available_in_pos' => true,
            'type'             => ProductType::GOODS,
        ])
        ->assertSet('data.category_id', fn ($categoryId): bool => filled($categoryId))
        ->assertSet('data.uom_id', fn ($uomId): bool => filled($uomId))
        ->assertSet('data.uom_po_id', fn ($uomId): bool => filled($uomId));
});
