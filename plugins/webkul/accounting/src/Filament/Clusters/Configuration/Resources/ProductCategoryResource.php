<?php

namespace Webkul\Accounting\Filament\Clusters\Configuration\Resources;

use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Resources\Pages\Page;
use Webkul\Account\Filament\Resources\ProductCategoryResource as BaseProductCategoryResource;
use Webkul\Accounting\Filament\Clusters\Configuration;
use Webkul\Accounting\Filament\Clusters\Configuration\Resources\ProductCategoryResource\Pages\CreateProductCategory;
use Webkul\Accounting\Filament\Clusters\Configuration\Resources\ProductCategoryResource\Pages\EditProductCategory;
use Webkul\Accounting\Filament\Clusters\Configuration\Resources\ProductCategoryResource\Pages\ListProductCategories;
use Webkul\Accounting\Filament\Clusters\Configuration\Resources\ProductCategoryResource\Pages\ManageProducts;
use Webkul\Accounting\Filament\Clusters\Configuration\Resources\ProductCategoryResource\Pages\ViewProductCategory;
use Webkul\Accounting\Models\Category;

class ProductCategoryResource extends BaseProductCategoryResource
{
    protected static ?string $model = Category::class;

    protected static ?string $cluster = Configuration::class;

    protected static bool $shouldRegisterNavigation = true;

    protected static ?int $navigationSort = 11;

    public static function getNavigationGroup(): ?string
    {
        return __('accounting::filament/clusters/configurations/resources/product-category.navigation.group');
    }

    public static function getNavigationLabel(): string
    {
        return __('accounting::filament/clusters/configurations/resources/product-category.navigation.title');
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
