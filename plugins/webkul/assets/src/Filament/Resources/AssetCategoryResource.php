<?php

namespace Webkul\Asset\Filament\Resources;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Asset\Filament\Clusters\Configurations;
use Webkul\Asset\Filament\Resources\AssetCategoryResource\Pages\CreateAssetCategory;
use Webkul\Asset\Filament\Resources\AssetCategoryResource\Pages\EditAssetCategory;
use Webkul\Asset\Filament\Resources\AssetCategoryResource\Pages\ListAssetCategories;
use Webkul\Asset\Filament\Resources\AssetCategoryResource\Pages\ViewAssetCategory;
use Webkul\Asset\Filament\Resources\AssetCategoryResource\RelationManagers\CategoryProductsRelationManager;
use Webkul\Asset\Filament\Resources\AssetCategoryResource\Schemas\AssetCategoryForm;
use Webkul\Asset\Filament\Resources\AssetCategoryResource\Schemas\AssetCategoryInfolist;
use Webkul\Asset\Filament\Resources\AssetCategoryResource\Tables\AssetCategoriesTable;
use Webkul\Asset\Models\AssetCategory;

class AssetCategoryResource extends Resource
{
    protected static ?string $model = AssetCategory::class;

    protected static ?string $slug = 'categories';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 3;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-tag';

    protected static ?string $cluster = Configurations::class;

    public static function getNavigationGroup(): string
    {
        return __('assets::filament/clusters/configurations.groups.products');
    }

    public static function getModelLabel(): string
    {
        return __('assets::filament/resources/category.title');
    }

    public static function getPluralModelLabel(): string
    {
        return __('assets::filament/resources/category.plural-title');
    }

    public static function getNavigationLabel(): string
    {
        return __('assets::filament/resources/category.navigation.title');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['parent', 'company', 'creator']);
    }

    public static function form(Schema $schema): Schema
    {
        return AssetCategoryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AssetCategoriesTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return AssetCategoryInfolist::configure($schema);
    }

    public static function getRelations(): array
    {
        return [
            CategoryProductsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListAssetCategories::route('/'),
            'create' => CreateAssetCategory::route('/create'),
            'view'   => ViewAssetCategory::route('/{record}'),
            'edit'   => EditAssetCategory::route('/{record}/edit'),
        ];
    }
}
