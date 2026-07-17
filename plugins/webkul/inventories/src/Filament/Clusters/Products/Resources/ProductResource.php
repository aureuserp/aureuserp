<?php

namespace Webkul\Inventory\Filament\Clusters\Products\Resources;

use Filament\Resources\Pages\Page;
use Webkul\Inventory\Filament\Clusters\Products;
use Webkul\Inventory\Filament\Clusters\Products\Resources\ProductResource\Pages\CreateProduct;
use Webkul\Inventory\Filament\Clusters\Products\Resources\ProductResource\Pages\EditProduct;
use Webkul\Inventory\Filament\Clusters\Products\Resources\ProductResource\Pages\ListProducts;
use Webkul\Inventory\Filament\Clusters\Products\Resources\ProductResource\Pages\ManageAttributes;
use Webkul\Inventory\Filament\Clusters\Products\Resources\ProductResource\Pages\ManageMoves;
use Webkul\Inventory\Filament\Clusters\Products\Resources\ProductResource\Pages\ManageQuantities;
use Webkul\Inventory\Filament\Clusters\Products\Resources\ProductResource\Pages\ManageVariants;
use Webkul\Inventory\Filament\Clusters\Products\Resources\ProductResource\Pages\ManageVendors;
use Webkul\Inventory\Filament\Clusters\Products\Resources\ProductResource\Pages\ViewProduct;
use Webkul\Inventory\Models\Product;
use Webkul\PluginManager\Package;
use Webkul\Product\Filament\Resources\ProductResource as BaseProductResource;

class ProductResource extends BaseProductResource
{
    protected static ?string $model = Product::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shopping-bag';

    protected static bool $shouldRegisterNavigation = true;

    protected static ?string $cluster = Products::class;

    protected static ?int $navigationSort = 1;

    protected static bool $isGloballySearchable = true;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationLabel(): string
    {
        return __('inventories::filament/clusters/products/resources/product.navigation.title');
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        $items = [
            ViewProduct::class,
            EditProduct::class,
            ManageAttributes::class,
            ManageVariants::class,
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
