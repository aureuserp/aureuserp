<?php

namespace Webkul\Manufacturing\Filament\Clusters\Configurations\Resources;

use BackedEnum;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Webkul\Manufacturing\Filament\Clusters\Configurations;
use Webkul\Manufacturing\Filament\Clusters\Configurations\Resources\WorkCenterResource\Pages\CreateWorkCenter;
use Webkul\Manufacturing\Filament\Clusters\Configurations\Resources\WorkCenterResource\Pages\EditWorkCenter;
use Webkul\Manufacturing\Filament\Clusters\Configurations\Resources\WorkCenterResource\Pages\ListWorkCenters;
use Webkul\Manufacturing\Filament\Clusters\Configurations\Resources\WorkCenterResource\Pages\ManageOperations;
use Webkul\Manufacturing\Filament\Clusters\Configurations\Resources\WorkCenterResource\Pages\ViewWorkCenter;
use Webkul\Manufacturing\Filament\Clusters\Configurations\Resources\WorkCenterResource\Schemas\WorkCenterForm;
use Webkul\Manufacturing\Filament\Clusters\Configurations\Resources\WorkCenterResource\Schemas\WorkCenterInfolist;
use Webkul\Manufacturing\Filament\Clusters\Configurations\Resources\WorkCenterResource\Tables\WorkCentersTable;
use Webkul\Manufacturing\Models\WorkCenter;
use Webkul\Manufacturing\Settings\OperationSettings;

class WorkCenterResource extends Resource
{
    protected static ?string $model = WorkCenter::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-wrench-screwdriver';

    protected static ?int $navigationSort = 1;

    protected static ?string $cluster = Configurations::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function isDiscovered(): bool
    {
        if (app()->runningInConsole()) {
            return true;
        }

        return settings(OperationSettings::class)->enable_work_orders;
    }

    public static function getModelLabel(): string
    {
        return __('manufacturing::models/work-center.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('manufacturing::filament/clusters/configurations/resources/work-center.navigation.group');
    }

    public static function getNavigationLabel(): string
    {
        return __('manufacturing::filament/clusters/configurations/resources/work-center.navigation.title');
    }

    public static function getSubNavigationPosition(): SubNavigationPosition
    {
        return SubNavigationPosition::Start;
    }

    public static function form(Schema $schema): Schema
    {
        return WorkCenterForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WorkCentersTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return WorkCenterInfolist::configure($schema);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'      => ListWorkCenters::route('/'),
            'create'     => CreateWorkCenter::route('/create'),
            'view'       => ViewWorkCenter::route('/{record}'),
            'edit'       => EditWorkCenter::route('/{record}/edit'),
            'operations' => ManageOperations::route('/{record}/operations'),
        ];
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewWorkCenter::class,
            EditWorkCenter::class,
            ManageOperations::class,
        ]);
    }
}
