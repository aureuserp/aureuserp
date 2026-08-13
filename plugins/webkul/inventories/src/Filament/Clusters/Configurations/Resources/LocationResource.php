<?php

namespace Webkul\Inventory\Filament\Clusters\Configurations\Resources;

use BackedEnum;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\Inventory\Filament\Clusters\Configurations;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\LocationResource\Pages\CreateLocation;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\LocationResource\Pages\EditLocation;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\LocationResource\Pages\ListLocations;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\LocationResource\Pages\ViewLocation;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\LocationResource\Schemas\LocationForm;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\LocationResource\Schemas\LocationInfolist;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\LocationResource\Tables\LocationsTable;
use Webkul\Inventory\Models\Location;
use Webkul\Inventory\Settings\WarehouseSettings;

class LocationResource extends Resource
{
    protected static ?string $model = Location::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-map-pin';

    protected static ?int $navigationSort = 2;

    protected static ?string $cluster = Configurations::class;

    protected static ?string $recordTitleAttribute = 'full_name';

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
        return __('inventories::filament/clusters/configurations/resources/location.navigation.group');
    }

    public static function getNavigationLabel(): string
    {
        return __('inventories::filament/clusters/configurations/resources/location.navigation.title');
    }

    public static function form(Schema $schema): Schema
    {
        return LocationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LocationsTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return LocationInfolist::configure($schema);
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewLocation::class,
            EditLocation::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListLocations::route('/'),
            'create' => CreateLocation::route('/create'),
            'view'   => ViewLocation::route('/{record}'),
            'edit'   => EditLocation::route('/{record}/edit'),
        ];
    }
}
