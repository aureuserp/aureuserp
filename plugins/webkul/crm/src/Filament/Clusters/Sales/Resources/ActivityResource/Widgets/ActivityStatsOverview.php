<?php

namespace Webkul\Crm\Filament\Clusters\Sales\Resources\ActivityResource\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Webkul\Crm\Enums\ActivityDueStatus;
use Webkul\Crm\Filament\Clusters\Sales\Resources\ActivityResource;

class ActivityStatsOverview extends StatsOverviewWidget
{
    public ?string $scope = 'mine';

    protected int|string|array $columnSpan = 'full';

    protected function getStats(): array
    {
        $query = fn () => ActivityResource::getScopedQuery($this->scope);

        return [
            Stat::make(ActivityDueStatus::OVERDUE->getLabel(), $query()->overdue()->count())
                ->icon(ActivityDueStatus::OVERDUE->getIcon())
                ->color('danger'),
            Stat::make(ActivityDueStatus::TODAY->getLabel(), $query()->dueToday()->count())
                ->icon(ActivityDueStatus::TODAY->getIcon())
                ->color('warning'),
            Stat::make(ActivityDueStatus::PLANNED->getLabel(), $query()->planned()->count())
                ->icon(ActivityDueStatus::PLANNED->getIcon())
                ->color('success'),
            Stat::make(__('crm::filament/clusters/sales/resources/activity/pages/graph-activities.widgets.total'), $query()->count())
                ->icon('heroicon-o-clock'),
        ];
    }
}
