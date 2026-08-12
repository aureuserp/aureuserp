<?php

namespace Webkul\Employee\Filament\Clusters\Configurations\Resources;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\Employee\Filament\Clusters\Configurations;
use Webkul\Employee\Filament\Clusters\Configurations\Resources\WorkLocationResource\Pages\ListWorkLocations;
use Webkul\Employee\Filament\Clusters\Configurations\Resources\WorkLocationResource\Schemas\WorkLocationForm;
use Webkul\Employee\Filament\Clusters\Configurations\Resources\WorkLocationResource\Schemas\WorkLocationInfolist;
use Webkul\Employee\Filament\Clusters\Configurations\Resources\WorkLocationResource\Tables\WorkLocationsTable;
use Webkul\Employee\Models\WorkLocation;

class WorkLocationResource extends Resource
{
    protected static ?string $model = WorkLocation::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-map-pin';

    protected static ?string $cluster = Configurations::class;

    public static function getModelLabel(): string
    {
        return __('employees::filament/clusters/configurations/resources/work-location.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('employees::filament/clusters/configurations/resources/work-location.navigation.group');
    }

    public static function getNavigationLabel(): string
    {
        return __('employees::filament/clusters/configurations/resources/work-location.navigation.title');
    }

    public static function form(Schema $schema): Schema
    {
        return WorkLocationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WorkLocationsTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return WorkLocationInfolist::configure($schema);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListWorkLocations::route('/'),
        ];
    }
}
