<?php

namespace Webkul\Asset\Filament\Resources;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Asset\Filament\Clusters\Configurations;
use Webkul\Asset\Filament\Resources\AssetWarehouseResource\Pages\CreateAssetWarehouse;
use Webkul\Asset\Filament\Resources\AssetWarehouseResource\Pages\EditAssetWarehouse;
use Webkul\Asset\Filament\Resources\AssetWarehouseResource\Pages\ListAssetWarehouses;
use Webkul\Asset\Filament\Resources\AssetWarehouseResource\Pages\ViewAssetWarehouse;
use Webkul\Asset\Filament\Resources\AssetWarehouseResource\RelationManagers\WarehouseAssetsRelationManager;
use Webkul\Asset\Filament\Resources\AssetWarehouseResource\Schemas\AssetWarehouseForm;
use Webkul\Asset\Filament\Resources\AssetWarehouseResource\Schemas\AssetWarehouseInfolist;
use Webkul\Asset\Filament\Resources\AssetWarehouseResource\Tables\AssetWarehousesTable;
use Webkul\Asset\Models\AssetWarehouse;

class AssetWarehouseResource extends Resource
{
    protected static ?string $model = AssetWarehouse::class;

    protected static ?string $slug = 'warehouses';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 1;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-building-storefront';

    protected static ?string $cluster = Configurations::class;

    public static function getNavigationGroup(): string
    {
        return __('assets::filament/clusters/configurations.groups.warehouses');
    }

    public static function getModelLabel(): string
    {
        return __('assets::filament/resources/asset-warehouse.title');
    }

    public static function getPluralModelLabel(): string
    {
        return __('assets::filament/resources/asset-warehouse.plural-title');
    }

    public static function getNavigationLabel(): string
    {
        return __('assets::filament/resources/asset-warehouse.navigation.title');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['custodian', 'company', 'creator']);
    }

    public static function form(Schema $schema): Schema
    {
        return AssetWarehouseForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AssetWarehousesTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return AssetWarehouseInfolist::configure($schema);
    }

    public static function getRelations(): array
    {
        return [
            WarehouseAssetsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListAssetWarehouses::route('/'),
            'create' => CreateAssetWarehouse::route('/create'),
            'view'   => ViewAssetWarehouse::route('/{record}'),
            'edit'   => EditAssetWarehouse::route('/{record}/edit'),
        ];
    }
}
