<?php

namespace Webkul\PointOfSale\Filament\Admin\Clusters\Configurations\Resources;

use BackedEnum;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Webkul\PointOfSale\Filament\Admin\Clusters\Configurations;
use Webkul\PointOfSale\Filament\Admin\Clusters\Configurations\Resources\CategoryResource\Pages\CreateCategory;
use Webkul\PointOfSale\Filament\Admin\Clusters\Configurations\Resources\CategoryResource\Pages\EditCategory;
use Webkul\PointOfSale\Filament\Admin\Clusters\Configurations\Resources\CategoryResource\Pages\ListCategories;
use Webkul\PointOfSale\Filament\Admin\Clusters\Configurations\Resources\CategoryResource\Pages\ViewCategory;
use Webkul\PointOfSale\Filament\Admin\Clusters\Configurations\Resources\CategoryResource\Schemas\CategoryForm;
use Webkul\PointOfSale\Filament\Admin\Clusters\Configurations\Resources\CategoryResource\Schemas\CategoryInfolist;
use Webkul\PointOfSale\Filament\Admin\Clusters\Configurations\Resources\CategoryResource\Tables\CategoriesTable;
use Webkul\PointOfSale\Models\Category;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-squares-2x2';

    protected static ?int $navigationSort = 6;

    protected static ?string $cluster = Configurations::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getModelLabel(): string
    {
        return __('point-of-sale::models/category.title');
    }

    public static function getPluralModelLabel(): string
    {
        return __('point-of-sale::models/category.plural-title');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('point-of-sale::filament/admin/clusters/configurations.groups.products');
    }

    public static function getNavigationLabel(): string
    {
        return __('point-of-sale::filament/admin/clusters/configurations/resources/category.navigation.title');
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name'];
    }

    public static function form(Schema $schema): Schema
    {
        return CategoryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CategoriesTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes([
            SoftDeletingScope::class,
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CategoryInfolist::configure($schema);
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewCategory::class,
            EditCategory::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListCategories::route('/'),
            'create' => CreateCategory::route('/create'),
            'view'   => ViewCategory::route('/{record}'),
            'edit'   => EditCategory::route('/{record}/edit'),
        ];
    }
}
