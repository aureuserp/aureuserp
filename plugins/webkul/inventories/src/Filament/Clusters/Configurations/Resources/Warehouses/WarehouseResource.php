<?php

namespace Webkul\Inventory\Filament\Clusters\Configurations\Resources\Warehouses;

use BackedEnum;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\Field\Filament\Traits\HasCustomFields;
use Webkul\Inventory\Filament\Clusters\Configurations;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\Warehouses\Pages\CreateWarehouse;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\Warehouses\Pages\EditWarehouse;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\Warehouses\Pages\ListWarehouses;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\Warehouses\Pages\ManageRoutes;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\Warehouses\Pages\ViewWarehouse;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\Warehouses\Schemas\WarehouseForm;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\Warehouses\Schemas\WarehouseInfolist;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\Warehouses\Tables\WarehousesTable;
use Webkul\Inventory\Models\Warehouse;

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
        return WarehouseForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WarehousesTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return WarehouseInfolist::configure($schema);
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
