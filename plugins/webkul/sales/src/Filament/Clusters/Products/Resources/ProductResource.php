<?php

namespace Webkul\Sale\Filament\Clusters\Products\Resources;

use Filament\Resources\Pages\Page;
use Webkul\Invoice\Filament\Clusters\Customer\Resources\ProductResource as BaseProductResource;
use Webkul\Sale\Filament\Clusters\Products;
use Webkul\Sale\Filament\Clusters\Products\Resources\ProductResource\Pages\CreateProduct;
use Webkul\Sale\Filament\Clusters\Products\Resources\ProductResource\Pages\EditProduct;
use Webkul\Sale\Filament\Clusters\Products\Resources\ProductResource\Pages\ListProducts;
use Webkul\Sale\Filament\Clusters\Products\Resources\ProductResource\Pages\ManageAttributes;
use Webkul\Sale\Filament\Clusters\Products\Resources\ProductResource\Pages\ManageVariants;
use Webkul\Sale\Filament\Clusters\Products\Resources\ProductResource\Pages\ViewProduct;
use Webkul\Sale\Models\Product;

class ProductResource extends BaseProductResource
{
    protected static ?string $model = Product::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shopping-bag';

    protected static bool $shouldRegisterNavigation = true;

    protected static ?string $cluster = Products::class;

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewProduct::class,
            EditProduct::class,
            ManageAttributes::class,
            ManageVariants::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'      => ListProducts::route('/'),
            'create'     => CreateProduct::route('/create'),
            'view'       => ViewProduct::route('/{record}'),
            'edit'       => EditProduct::route('/{record}/edit'),
            'attributes' => ManageAttributes::route('/{record}/attributes'),
            'variants'   => ManageVariants::route('/{record}/variants'),
        ];
    }
}
