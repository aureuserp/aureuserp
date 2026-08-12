<?php

namespace Webkul\Manufacturing\Filament\Clusters\Products\Resources;

use Filament\Resources\Pages\Page;
use Webkul\Inventory\Filament\Clusters\Products\Resources\ProductResource as BaseProductResource;
use Webkul\Manufacturing\Filament\Clusters\Products;
use Webkul\Manufacturing\Filament\Clusters\Products\Resources\ProductResource\Pages\CreateProduct;
use Webkul\Manufacturing\Filament\Clusters\Products\Resources\ProductResource\Pages\EditProduct;
use Webkul\Manufacturing\Filament\Clusters\Products\Resources\ProductResource\Pages\ListProducts;
use Webkul\Manufacturing\Filament\Clusters\Products\Resources\ProductResource\Pages\ManageAttributes;
use Webkul\Manufacturing\Filament\Clusters\Products\Resources\ProductResource\Pages\ManageBillsOfMaterials;
use Webkul\Manufacturing\Filament\Clusters\Products\Resources\ProductResource\Pages\ManageMoves;
use Webkul\Manufacturing\Filament\Clusters\Products\Resources\ProductResource\Pages\ManageQuantities;
use Webkul\Manufacturing\Filament\Clusters\Products\Resources\ProductResource\Pages\ManageVariants;
use Webkul\Manufacturing\Filament\Clusters\Products\Resources\ProductResource\Pages\ManageVendors;
use Webkul\Manufacturing\Filament\Clusters\Products\Resources\ProductResource\Pages\ViewProduct;
use Webkul\Manufacturing\Models\Product;
use Webkul\PluginManager\Package;

class ProductResource extends BaseProductResource
{
    protected static ?string $model = Product::class;

    protected static ?string $cluster = Products::class;

    protected static ?int $navigationSort = 1;

    public static function getRecordSubNavigation(Page $page): array
    {
        $items = [
            ViewProduct::class,
            EditProduct::class,
            ManageAttributes::class,
            ManageVariants::class,
            ManageBillsOfMaterials::class,
        ];

        if (Package::isPluginInstalled('purchases')) {
            $items[] = ManageVendors::class;
        }

        $items[] = ManageQuantities::class;
        $items[] = ManageMoves::class;

        return $page->generateNavigationItems($items);
    }

    public static function getPages(): array
    {
        $pages = [
            'index'      => ListProducts::route('/'),
            'create'     => CreateProduct::route('/create'),
            'view'       => ViewProduct::route('/{record}'),
            'edit'       => EditProduct::route('/{record}/edit'),
            'boms'       => ManageBillsOfMaterials::route('/{record}/boms'),
            'attributes' => ManageAttributes::route('/{record}/attributes'),
            'variants'   => ManageVariants::route('/{record}/variants'),
            'moves'      => ManageMoves::route('/{record}/moves'),
            'quantities' => ManageQuantities::route('/{record}/quantities'),
        ];

        if (Package::isPluginInstalled('purchases')) {
            $pages['vendors'] = ManageVendors::route('/{record}/vendors');
        }

        return $pages;
    }
}
