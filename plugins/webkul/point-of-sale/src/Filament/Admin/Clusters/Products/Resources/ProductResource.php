<?php

namespace Webkul\PointOfSale\Filament\Admin\Clusters\Products\Resources;

use BackedEnum;
use Filament\Resources\Pages\Page;
use Webkul\Invoice\Filament\Clusters\Customers\Resources\ProductResource as BaseProductResource;
use Webkul\PluginManager\Package;
use Webkul\PointOfSale\Filament\Admin\Clusters\Products;
use Webkul\PointOfSale\Filament\Admin\Clusters\Products\Resources\ProductResource\Pages\CreateProduct;
use Webkul\PointOfSale\Filament\Admin\Clusters\Products\Resources\ProductResource\Pages\EditProduct;
use Webkul\PointOfSale\Filament\Admin\Clusters\Products\Resources\ProductResource\Pages\ListProducts;
use Webkul\PointOfSale\Filament\Admin\Clusters\Products\Resources\ProductResource\Pages\ManageAttributes;
use Webkul\PointOfSale\Filament\Admin\Clusters\Products\Resources\ProductResource\Pages\ManageBillsOfMaterials;
use Webkul\PointOfSale\Filament\Admin\Clusters\Products\Resources\ProductResource\Pages\ManageMoves;
use Webkul\PointOfSale\Filament\Admin\Clusters\Products\Resources\ProductResource\Pages\ManageQuantities;
use Webkul\PointOfSale\Filament\Admin\Clusters\Products\Resources\ProductResource\Pages\ManageVariants;
use Webkul\PointOfSale\Filament\Admin\Clusters\Products\Resources\ProductResource\Pages\ManageVendors;
use Webkul\PointOfSale\Filament\Admin\Clusters\Products\Resources\ProductResource\Pages\ViewProduct;
use Webkul\PointOfSale\Models\Product;

class ProductResource extends BaseProductResource
{
    protected static ?string $model = Product::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-shopping-bag';

    protected static bool $shouldRegisterNavigation = true;

    protected static ?int $navigationSort = 1;

    protected static ?string $cluster = Products::class;

    public static function getNavigationLabel(): string
    {
        return __('point-of-sale::filament/admin/clusters/products/resources/product.navigation.title');
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        $items = [
            ViewProduct::class,
            EditProduct::class,
            ManageAttributes::class,
            ManageVariants::class,
        ];

        if (Package::isPluginInstalled('manufacturing')) {
            $items[] = ManageBillsOfMaterials::class;
        }

        if (Package::isPluginInstalled('purchases')) {
            $items[] = ManageVendors::class;
        }

        if (Package::isPluginInstalled('inventories')) {
            $items[] = ManageQuantities::class;
            $items[] = ManageMoves::class;
        }

        return $page->generateNavigationItems($items);
    }

    public static function getPages(): array
    {
        $pages = [
            'index'      => ListProducts::route('/'),
            'create'     => CreateProduct::route('/create'),
            'view'       => ViewProduct::route('/{record}'),
            'edit'       => EditProduct::route('/{record}/edit'),
            'attributes' => ManageAttributes::route('/{record}/attributes'),
            'variants'   => ManageVariants::route('/{record}/variants'),
        ];

        if (Package::isPluginInstalled('manufacturing')) {
            $pages['boms'] = ManageBillsOfMaterials::route('/{record}/boms');
        }

        if (Package::isPluginInstalled('purchases')) {
            $pages['vendors'] = ManageVendors::route('/{record}/vendors');
        }

        if (Package::isPluginInstalled('inventories')) {
            $pages['quantities'] = ManageQuantities::route('/{record}/quantities');
            $pages['moves'] = ManageMoves::route('/{record}/moves');
        }

        return $pages;
    }
}
