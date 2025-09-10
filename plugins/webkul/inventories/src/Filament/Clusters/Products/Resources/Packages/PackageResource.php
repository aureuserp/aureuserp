<?php

namespace Webkul\Inventory\Filament\Clusters\Products\Resources\Packages;

use BackedEnum;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\Inventory\Filament\Clusters\Products;
use Webkul\Inventory\Filament\Clusters\Products\Resources\Packages\Pages\CreatePackage;
use Webkul\Inventory\Filament\Clusters\Products\Resources\Packages\Pages\EditPackage;
use Webkul\Inventory\Filament\Clusters\Products\Resources\Packages\Pages\ListPackages;
use Webkul\Inventory\Filament\Clusters\Products\Resources\Packages\Pages\ManageOperations;
use Webkul\Inventory\Filament\Clusters\Products\Resources\Packages\Pages\ManageProducts;
use Webkul\Inventory\Filament\Clusters\Products\Resources\Packages\Pages\ViewPackage;
use Webkul\Inventory\Filament\Clusters\Products\Resources\Packages\RelationManagers\ProductsRelationManager;
use Webkul\Inventory\Filament\Clusters\Products\Resources\Packages\Schemas\PackageForm;
use Webkul\Inventory\Filament\Clusters\Products\Resources\Packages\Schemas\PackageInfolist;
use Webkul\Inventory\Filament\Clusters\Products\Resources\Packages\Tables\PackagesTable;
use Webkul\Inventory\Models\Package;
use Webkul\Inventory\Settings\OperationSettings;

class PackageResource extends Resource
{
    protected static ?string $model = Package::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-cube';

    protected static ?string $cluster = Products::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 2;

    protected static ?SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    public static function isDiscovered(): bool
    {
        if (app()->runningInConsole()) {
            return true;
        }

        return app(OperationSettings::class)->enable_packages;
    }

    public static function getNavigationLabel(): string
    {
        return __('inventories::filament/clusters/products/resources/package.navigation.title');
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
