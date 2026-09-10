<?php

namespace Webkul\Inventory\Filament\Clusters\Products\Resources;

use BackedEnum;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Webkul\Inventory\Filament\Clusters\Products;
use Webkul\Inventory\Filament\Clusters\Products\Resources\PackageResource\Pages\CreatePackage;
use Webkul\Inventory\Filament\Clusters\Products\Resources\PackageResource\Pages\EditPackage;
use Webkul\Inventory\Filament\Clusters\Products\Resources\PackageResource\Pages\ListPackages;
use Webkul\Inventory\Filament\Clusters\Products\Resources\PackageResource\Pages\ManageOperations;
use Webkul\Inventory\Filament\Clusters\Products\Resources\PackageResource\Pages\ManageProducts;
use Webkul\Inventory\Filament\Clusters\Products\Resources\PackageResource\Pages\ViewPackage;
use Webkul\Inventory\Filament\Clusters\Products\Resources\PackageResource\RelationManagers\ProductsRelationManager;
use Webkul\Inventory\Filament\Clusters\Products\Resources\PackageResource\Schemas\PackageForm;
use Webkul\Inventory\Filament\Clusters\Products\Resources\PackageResource\Schemas\PackageInfolist;
use Webkul\Inventory\Filament\Clusters\Products\Resources\PackageResource\Tables\PackagesTable;
use Webkul\Inventory\Models\Package;
use Webkul\Inventory\Settings\OperationSettings;

class PackageResource extends Resource
{
    protected static ?string $model = Package::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-cube';

    protected static ?string $cluster = Products::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 2;

    public static function isDiscovered(): bool
    {
        if (app()->runningInConsole()) {
            return true;
        }

        return settings(OperationSettings::class)->enable_packages;
    }

    public static function getNavigationLabel(): string
    {
        return __('inventories::filament/clusters/products/resources/package.navigation.title');
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            __('inventories::filament/clusters/products/resources/package.global-search.package-type') => $record->packageType?->name ?? '—',
            __('inventories::filament/clusters/products/resources/package.global-search.location')     => $record->location?->full_name ?? '—',
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return PackageForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PackagesTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PackageInfolist::configure($schema);
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewPackage::class,
            EditPackage::class,
            ManageProducts::class,
            ManageOperations::class,
        ]);
    }

    public static function getRelations(): array
    {
        return [
            ProductsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'      => ListPackages::route('/'),
            'create'     => CreatePackage::route('/create'),
            'edit'       => EditPackage::route('/{record}/edit'),
            'view'       => ViewPackage::route('/{record}/view'),
            'products'   => ManageProducts::route('/{record}/products'),
            'operations' => ManageOperations::route('/{record}/operations'),
        ];
    }
}
