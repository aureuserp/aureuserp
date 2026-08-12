<?php

namespace Webkul\Inventory\Filament\Clusters\Configurations\Resources;

use BackedEnum;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\Inventory\Filament\Clusters\Configurations;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\RouteResource\Pages\CreateRoute;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\RouteResource\Pages\EditRoute;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\RouteResource\Pages\ListRoutes;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\RouteResource\Pages\ManageRules;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\RouteResource\Pages\ViewRoute;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\RouteResource\RelationManagers\RulesRelationManager;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\RouteResource\Schemas\RouteForm;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\RouteResource\Schemas\RouteInfolist;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\RouteResource\Tables\RoutesTable;
use Webkul\Inventory\Models\Route;
use Webkul\Inventory\Settings\WarehouseSettings;

class RouteResource extends Resource
{
    protected static ?string $model = Route::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-arrow-path';

    protected static ?int $navigationSort = 3;

    protected static ?string $cluster = Configurations::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static bool $isGloballySearchable = false;

    public static function isDiscovered(): bool
    {
        if (app()->runningInConsole()) {
            return true;
        }

        return settings(WarehouseSettings::class)->enable_multi_steps_routes;
    }

    public static function getNavigationGroup(): string
    {
        return __('inventories::filament/clusters/configurations/resources/route.navigation.group');
    }

    public static function getNavigationLabel(): string
    {
        return __('inventories::filament/clusters/configurations/resources/route.navigation.title');
    }

    public static function form(Schema $schema): Schema
    {
        return RouteForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RoutesTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return RouteInfolist::configure($schema);
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewRoute::class,
            EditRoute::class,
            ManageRules::class,
        ]);
    }

    public static function getRelations(): array
    {
        return [
            RulesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'   => ListRoutes::route('/'),
            'create'  => CreateRoute::route('/create'),
            'view'    => ViewRoute::route('/{record}'),
            'edit'    => EditRoute::route('/{record}/edit'),
            'rules'   => ManageRules::route('/{record}/rules'),
        ];
    }
}
