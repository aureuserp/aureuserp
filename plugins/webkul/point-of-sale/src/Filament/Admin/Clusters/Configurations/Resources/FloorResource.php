<?php

namespace Webkul\PointOfSale\Filament\Admin\Clusters\Configurations\Resources;

use BackedEnum;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Webkul\PointOfSale\Filament\Admin\Clusters\Configurations;
use Webkul\PointOfSale\Filament\Admin\Clusters\Configurations\Resources\FloorResource\Pages\CreateFloor;
use Webkul\PointOfSale\Filament\Admin\Clusters\Configurations\Resources\FloorResource\Pages\EditFloor;
use Webkul\PointOfSale\Filament\Admin\Clusters\Configurations\Resources\FloorResource\Pages\ListFloors;
use Webkul\PointOfSale\Filament\Admin\Clusters\Configurations\Resources\FloorResource\Pages\ViewFloor;
use Webkul\PointOfSale\Filament\Admin\Clusters\Configurations\Resources\FloorResource\RelationManagers\TablesRelationManager;
use Webkul\PointOfSale\Filament\Admin\Clusters\Configurations\Resources\FloorResource\Schemas\FloorForm;
use Webkul\PointOfSale\Filament\Admin\Clusters\Configurations\Resources\FloorResource\Schemas\FloorInfolist;
use Webkul\PointOfSale\Filament\Admin\Clusters\Configurations\Resources\FloorResource\Tables\FloorsTable;
use Webkul\PointOfSale\Models\Floor;

class FloorResource extends Resource
{
    protected static ?string $model = Floor::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-map';

    protected static ?int $navigationSort = 4;

    protected static ?string $cluster = Configurations::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getModelLabel(): string
    {
        return __('point-of-sale::models/floor.title');
    }

    public static function getPluralModelLabel(): string
    {
        return __('point-of-sale::models/floor.plural-title');
    }

    public static function getNavigationGroup(): ?string
    {
        return null;
    }

    public static function getNavigationLabel(): string
    {
        return __('point-of-sale::filament/admin/clusters/configurations/resources/floor.navigation.title');
    }

    public static function form(Schema $schema): Schema
    {
        return FloorForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FloorsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            TablesRelationManager::class,
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes([
            SoftDeletingScope::class,
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return FloorInfolist::configure($schema);
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewFloor::class,
            EditFloor::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListFloors::route('/'),
            'create' => CreateFloor::route('/create'),
            'view'   => ViewFloor::route('/{record}'),
            'edit'   => EditFloor::route('/{record}/edit'),
        ];
    }
}
