<?php

namespace Webkul\Purchase\Filament\Admin\Clusters\Configurations\Resources;

use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Resources\Pages\Page;
use Webkul\Product\Filament\Resources\CategoryResource;
use Webkul\Purchase\Filament\Admin\Clusters\Configurations;
use Webkul\Purchase\Filament\Admin\Clusters\Configurations\Resources\ProductCategoryResource\Pages\CreateProductCategory;
use Webkul\Purchase\Filament\Admin\Clusters\Configurations\Resources\ProductCategoryResource\Pages\EditProductCategory;
use Webkul\Purchase\Filament\Admin\Clusters\Configurations\Resources\ProductCategoryResource\Pages\ListProductCategories;
use Webkul\Purchase\Filament\Admin\Clusters\Configurations\Resources\ProductCategoryResource\Pages\ManageProducts;
use Webkul\Purchase\Filament\Admin\Clusters\Configurations\Resources\ProductCategoryResource\Pages\ViewProductCategory;
use Webkul\Purchase\Models\Category;

class ProductCategoryResource extends CategoryResource
{
    protected static ?string $model = Category::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-folder';

    protected static bool $shouldRegisterNavigation = true;

    protected static ?int $navigationSort = 8;

    protected static ?string $cluster = Configurations::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationGroup(): string
    {
        return __('purchases::filament/admin/clusters/configurations/resources/product-category.navigation.group');
    }

    public static function getNavigationLabel(): string
    {
        return __('purchases::filament/admin/clusters/configurations/resources/product-category.navigation.title');
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
