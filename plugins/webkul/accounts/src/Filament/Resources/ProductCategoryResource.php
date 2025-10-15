<?php

namespace Webkul\Account\Filament\Resources;

use Filament\Resources\Pages\Page;
use Webkul\Account\Filament\Resources\ProductCategoryResource\Pages\CreateProductCategory;
use Webkul\Account\Filament\Resources\ProductCategoryResource\Pages\EditProductCategory;
use Webkul\Account\Filament\Resources\ProductCategoryResource\Pages\ListProductCategories;
use Webkul\Account\Filament\Resources\ProductCategoryResource\Pages\ManageProducts;
use Webkul\Account\Filament\Resources\ProductCategoryResource\Pages\ViewProductCategory;
use Webkul\Invoice\Models\Category;
use Webkul\Product\Filament\Resources\CategoryResource as BaseProductCategoryResource;

class ProductCategoryResource extends BaseProductCategoryResource
{
    protected static ?string $model = Category::class;


    protected static bool $shouldRegisterNavigation = false;

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewProductCategory::class,
            EditProductCategory::class,
            ManageProducts::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'    => ListProductCategories::route('/'),
            'create'   => CreateProductCategory::route('/create'),
            'view'     => ViewProductCategory::route('/{record}'),
            'edit'     => EditProductCategory::route('/{record}/edit'),
            'products' => ManageProducts::route('/{record}/products'),
        ];
    }
}
