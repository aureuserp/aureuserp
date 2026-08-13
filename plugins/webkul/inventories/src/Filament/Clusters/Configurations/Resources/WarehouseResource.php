<?php

namespace Webkul\Inventory\Filament\Clusters\Configurations\Resources;

use BackedEnum;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\Field\Filament\Traits\HasCustomFields;
use Webkul\Inventory\Filament\Clusters\Configurations;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\WarehouseResource\Pages\CreateWarehouse;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\WarehouseResource\Pages\EditWarehouse;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\WarehouseResource\Pages\ListWarehouses;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\WarehouseResource\Pages\ManageRoutes;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\WarehouseResource\Pages\ViewWarehouse;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\WarehouseResource\Schemas\WarehouseForm;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\WarehouseResource\Schemas\WarehouseInfolist;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\WarehouseResource\Tables\WarehousesTable;
use Webkul\Inventory\Models\Warehouse;
use Webkul\Inventory\Settings\WarehouseSettings;

class WarehouseResource extends Resource
{
    use HasCustomFields;

    protected static ?string $model = Warehouse::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-building-storefront';

    protected static ?int $navigationSort = 1;

    protected static ?string $cluster = Configurations::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static bool $isGloballySearchable = false;

    public static function getNavigationGroup(): string
    {
        return __('inventories::filament/clusters/configurations/resources/warehouse.navigation.group');
    }

    public static function getNavigationLabel(): string
    {
        return __('inventories::filament/clusters/configurations/resources/warehouse.navigation.title');
    }

    public static function form(Schema $schema): Schema
    {
        return WarehouseForm::configure($schema, static::getCustomFormFields());
    }

    public static function table(Table $table): Table
    {
        return WarehousesTable::configure($table, static::getCustomTableColumns(), static::getCustomTableFilters());
    }

    public static function infolist(Schema $schema): Schema
    {
        return WarehouseInfolist::configure($schema, static::getCustomInfolistEntries());
    }

    public static function getWarehouseSettings(): WarehouseSettings
    {
        return settings(WarehouseSettings::class);
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewWarehouse::class,
            EditWarehouse::class,
            ManageRoutes::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListWarehouses::route('/'),
            'create' => CreateWarehouse::route('/create'),
            'view'   => ViewWarehouse::route('/{record}'),
            'edit'   => EditWarehouse::route('/{record}/edit'),
            'routes' => ManageRoutes::route('/{record}/routes'),
        ];
    }
}
