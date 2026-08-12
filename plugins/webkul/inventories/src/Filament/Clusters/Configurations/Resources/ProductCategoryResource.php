<?php

namespace Webkul\Inventory\Filament\Clusters\Configurations\Resources;

use Filament\Resources\Pages\Page;
use Filament\Schemas\Schema;
use Webkul\Inventory\Filament\Clusters\Configurations;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\ProductCategoryResource\Pages\CreateProductCategory;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\ProductCategoryResource\Pages\EditProductCategory;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\ProductCategoryResource\Pages\ListProductCategories;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\ProductCategoryResource\Pages\ManageProducts;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\ProductCategoryResource\Pages\ViewProductCategory;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\ProductCategoryResource\Schemas\ProductCategoryForm;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\ProductCategoryResource\Schemas\ProductCategoryInfolist;
use Webkul\Inventory\Models\Category;
use Webkul\Product\Filament\Resources\CategoryResource;

class ProductCategoryResource extends CategoryResource
{
    protected static ?string $model = Category::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-folder';

    protected static bool $shouldRegisterNavigation = true;

    protected static ?int $navigationSort = 8;

    protected static ?string $cluster = Configurations::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static bool $isGloballySearchable = false;

    public static function getNavigationGroup(): string
    {
        return __('inventories::filament/clusters/configurations/resources/product-category.navigation.group');
    }

    public static function getNavigationLabel(): string
    {
        return __('inventories::filament/clusters/configurations/resources/product-category.navigation.title');
    }

    public static function form(Schema $schema): Schema
    {
        return ProductCategoryForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ProductCategoryInfolist::configure($schema);
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
