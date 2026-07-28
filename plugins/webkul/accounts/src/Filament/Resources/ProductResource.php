<?php

namespace Webkul\Account\Filament\Resources;

use Filament\Resources\Pages\Page;
use Webkul\Account\Filament\Resources\ProductResource\Pages\CreateProduct;
use Webkul\Account\Filament\Resources\ProductResource\Pages\EditProduct;
use Webkul\Account\Filament\Resources\ProductResource\Pages\ListProducts;
use Webkul\Account\Filament\Resources\ProductResource\Pages\ManageAttributes;
use Webkul\Account\Filament\Resources\ProductResource\Pages\ManageVariants;
use Webkul\Account\Filament\Resources\ProductResource\Pages\ViewProduct;
use Webkul\Account\Models\Product;
use Webkul\Product\Filament\Resources\ProductResource as BaseProductResource;

class ProductResource extends BaseProductResource
{
    protected static ?string $model = Product::class;

    protected static bool $shouldRegisterNavigation = false;

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
