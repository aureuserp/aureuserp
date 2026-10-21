<?php

namespace Webkul\Crm\Filament\Clusters\Sales\Resources;

use Filament\Actions\Action;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Crm\Filament\Clusters\Sales;
use Webkul\Crm\Filament\Clusters\Sales\Resources\ActivityResource\Actions\ScheduleActivityAction;
use Webkul\Crm\Filament\Clusters\Sales\Resources\ActivityResource\Pages\GraphActivities;
use Webkul\Crm\Filament\Clusters\Sales\Resources\ActivityResource\Pages\KanbanActivities;
use Webkul\Crm\Filament\Clusters\Sales\Resources\ActivityResource\Pages\ListActivities;
use Webkul\Crm\Filament\Clusters\Sales\Resources\ActivityResource\Tables\ActivitiesTable;
use Webkul\Crm\Filament\Clusters\Sales\Resources\ActivityResource\Widgets\ActivitiesByStatusChart;
use Webkul\Crm\Filament\Clusters\Sales\Resources\ActivityResource\Widgets\ActivitiesByTypeChart;
use Webkul\Crm\Filament\Clusters\Sales\Resources\ActivityResource\Widgets\ActivitiesByUserChart;
use Webkul\Crm\Filament\Clusters\Sales\Resources\ActivityResource\Widgets\ActivityStatsOverview;
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

    public static function getScopedQuery(?string $scope = null): Builder
    {
        return static::getEloquentQuery()
            ->when($scope !== 'all', fn (Builder $query): Builder => $query->where('assigned_to', auth()->id()));
    }

    public static function table(Table $table): Table
    {
        return ActivitiesTable::configure($table);
    }

    public static function getWidgets(): array
    {
        return [
            ActivityStatsOverview::class,
            ActivitiesByStatusChart::class,
            ActivitiesByTypeChart::class,
            ActivitiesByUserChart::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => KanbanActivities::route('/'),
            'list'   => ListActivities::route('/list'),
            'graph'  => GraphActivities::route('/graph'),
        ];
    }

    public static function getViewSwitcherActions(string $currentPage): array
    {
        return [
            ...static::getViewSwitcherButtons($currentPage),
            ScheduleActivityAction::make(),
        ];
    }

    protected static function getViewSwitcherButtons(string $currentPage): array
    {
        return collect([
            'index'  => ['heroicon-o-view-columns', 'kanban'],
            'list'   => ['heroicon-o-list-bullet', 'list'],
            'graph'  => ['heroicon-o-chart-bar', 'graph'],
        ])
            ->except($currentPage)
            ->map(fn (array $view, string $page): Action => Action::make($view[1].'View')
                ->label(__('crm::filament/clusters/sales/resources/activity.views.'.$view[1]))
                ->icon($view[0])
                ->color('gray')
                ->url(fn (): string => static::getUrl($page)))
            ->values()
            ->all();
    }
}
