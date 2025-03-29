<?php

namespace Webkul\Sale\Filament\Clusters\Configuration\Resources;

use Webkul\Invoice\Filament\Clusters\Configuration\Resources\ProductCategoryResource as BaseProductCategoryResource;
use Filament\Resources\Pages\Page;
use Webkul\Sale\Filament\Clusters\Configuration;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\ProductCategoryResource\Pages;
use Webkul\Sale\Models\Category;

class ProductCategoryResource extends BaseProductCategoryResource
{
    protected static ?string $model = Category::class;

    protected static ?string $cluster = Configuration::class;

    public static function getNavigationGroup(): ?string
    {
        return __('sales::filament/clusters/configurations/resources/product-category.navigation.group');
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            Pages\ViewProductCategory::class,
            Pages\EditProductCategory::class,
            Pages\ManageProducts::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'    => Pages\ListProductCategories::route('/'),
            'create'   => Pages\CreateProductCategory::route('/create'),
            'view'     => Pages\ViewProductCategory::route('/{record}'),
            'edit'     => Pages\EditProductCategory::route('/{record}/edit'),
            'products' => Pages\ManageProducts::route('/{record}/products'),
        ];
    }
}
