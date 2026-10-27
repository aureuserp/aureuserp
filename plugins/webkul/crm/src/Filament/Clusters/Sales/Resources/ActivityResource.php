<?php

namespace Webkul\Crm\Filament\Clusters\Sales\Resources;

use Filament\Navigation\NavigationItem;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Crm\Filament\Clusters\Sales;
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
            'index'  => ListActivities::route('/'),
            'kanban' => KanbanActivities::route('/kanban'),
            'graph'  => GraphActivities::route('/graph'),
        ];
    }

    public static function getViewTabs(string $current): array
    {
        $tab = fn (string $page, string $label, string $icon): NavigationItem => NavigationItem::make($page)
            ->label($label)
            ->icon($icon)
            ->url(static::getUrl($page))
            ->isActiveWhen(fn (): bool => $page === $current);

        return [
            $tab('index', __('crm::filament/clusters/sales/resources/activity.views.list'), 'heroicon-o-list-bullet'),
            $tab('kanban', __('crm::filament/clusters/sales/resources/activity.views.kanban'), 'heroicon-o-view-columns'),
            $tab('graph', __('crm::filament/clusters/sales/resources/activity.views.graph'), 'heroicon-o-chart-bar'),
        ];
    }
}
