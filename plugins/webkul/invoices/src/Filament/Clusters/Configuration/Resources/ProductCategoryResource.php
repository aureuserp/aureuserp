<?php

namespace Webkul\Invoice\Filament\Clusters\Configuration\Resources;

use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Pages\Page;
use Webkul\Invoice\Filament\Clusters\Configuration;
use Webkul\Invoice\Filament\Clusters\Configuration\Resources\ProductCategoryResource\Pages;
use Webkul\Invoice\Models\Category;
use Webkul\Product\Filament\Resources\CategoryResource as BaseProductCategoryResource;

class ProductCategoryResource extends BaseProductCategoryResource
{
    protected static ?string $model = Category::class;

    protected static ?string $cluster = Configuration::class;

    protected static bool $shouldRegisterNavigation = true;

    public static function getNavigationGroup(): ?string
    {
        return __('invoices::filament/clusters/configurations/resources/product-category.navigation.group');
    }

    public static function getNavigationLabel(): string
    {
        return __('invoices::filament/clusters/configurations/resources/product-category.navigation.title');
    }

    public static function getSubNavigationPosition(): SubNavigationPosition
    {
        $route = request()->route()?->getName() ?? session('current_route');

        if ($route && $route != 'livewire.update') {
            session(['current_route' => $route]);
        } else {
            $route = session('current_route');
        }

        if ($route === self::getRouteBaseName().'.index') {
            return SubNavigationPosition::Start;
        }

        return SubNavigationPosition::Top;
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
