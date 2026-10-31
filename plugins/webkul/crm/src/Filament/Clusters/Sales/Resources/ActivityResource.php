<?php

namespace Webkul\Crm\Filament\Clusters\Sales\Resources;

use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Crm\Filament\Clusters\Sales;
use Webkul\Crm\Filament\Clusters\Sales\Resources\ActivityResource\Pages\ListActivities;
use Webkul\Crm\Filament\Clusters\Sales\Resources\ActivityResource\Tables\ActivitiesTable;
use Webkul\Crm\Models\Activity;

class ActivityResource extends Resource
{
    protected static ?string $model = Activity::class;

    protected static ?string $slug = 'activities';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clock';

    protected static ?string $cluster = Sales::class;

    protected static ?int $navigationSort = 2;

    protected static bool $isGloballySearchable = false;

    public static function getModelLabel(): string
    {
        return __('crm::filament/clusters/sales/resources/activity.model-label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('crm::filament/clusters/sales/resources/activity.plural-model-label');
    }

    public static function getNavigationLabel(): string
    {
        return __('crm::filament/clusters/sales/resources/activity.navigation.title');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('lead')
            ->with(['lead.partner', 'lead.stage', 'activityType', 'assignedTo']);
    }

    public static function table(Table $table): Table
    {
        return ActivitiesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListActivities::route('/'),
            'kanban' => ListActivities::route('/kanban'),
            'graph'  => ListActivities::route('/graph'),
        ];
    }
}
