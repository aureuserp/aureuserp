<?php

namespace Webkul\Inventory\Filament\Clusters\Configurations\Resources;

use BackedEnum;
use Filament\Resources\Pages\Page;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\Inventory\Filament\Clusters\Configurations;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\StorageCategoryResource\Pages\CreateStorageCategory;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\StorageCategoryResource\Pages\EditStorageCategory;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\StorageCategoryResource\Pages\ListStorageCategories;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\StorageCategoryResource\Pages\ManageCapacityByPackages;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\StorageCategoryResource\Pages\ManageCapacityByProducts;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\StorageCategoryResource\Pages\ManageLocations;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\StorageCategoryResource\Pages\ViewStorageCategory;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\StorageCategoryResource\RelationManagers\CapacityByPackagesRelationManager;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\StorageCategoryResource\RelationManagers\CapacityByProductsRelationManager;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\StorageCategoryResource\Schemas\StorageCategoryForm;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\StorageCategoryResource\Schemas\StorageCategoryInfolist;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\StorageCategoryResource\Tables\StorageCategoriesTable;
use Webkul\Inventory\Models\StorageCategory;
use Webkul\Inventory\Settings\WarehouseSettings;

class StorageCategoryResource extends Resource
{
    protected static ?string $model = StorageCategory::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-folder';

    protected static ?int $navigationSort = 4;

    protected static ?string $cluster = Configurations::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static bool $isGloballySearchable = false;

    public static function isDiscovered(): bool
    {
        if (app()->runningInConsole()) {
            return true;
        }

        return settings(WarehouseSettings::class)->enable_locations;
    }

    public static function getNavigationGroup(): string
    {
        return __('inventories::filament/clusters/configurations/resources/storage-category.navigation.group');
    }

    public static function getNavigationLabel(): string
    {
        return __('inventories::filament/clusters/configurations/resources/storage-category.navigation.title');
    }

    public static function form(Schema $schema): Schema
    {
        return StorageCategoryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StorageCategoriesTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return StorageCategoryInfolist::configure($schema);
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewStorageCategory::class,
            EditStorageCategory::class,
            ManageCapacityByPackages::class,
            ManageCapacityByProducts::class,
            ManageLocations::class,
        ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationGroup::make('Capacity By Packages', [
                CapacityByPackagesRelationManager::class,
            ])
                ->icon('heroicon-o-cube'),

            RelationGroup::make('Capacity By Products', [
                CapacityByProductsRelationManager::class,
            ])
                ->icon('heroicon-o-clipboard-document-check'),
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'      => ListStorageCategories::route('/'),
            'create'     => CreateStorageCategory::route('/create'),
            'view'       => ViewStorageCategory::route('/{record}'),
            'edit'       => EditStorageCategory::route('/{record}/edit'),
            'packages'   => ManageCapacityByPackages::route('/{record}/packages'),
            'products'   => ManageCapacityByProducts::route('/{record}/products'),
            'locations'  => ManageLocations::route('/{record}/locations'),
        ];
    }
}
