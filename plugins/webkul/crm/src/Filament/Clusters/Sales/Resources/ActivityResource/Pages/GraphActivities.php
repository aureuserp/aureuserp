<?php

namespace Webkul\Crm\Filament\Clusters\Sales\Resources\ActivityResource\Pages;

use Filament\Resources\Pages\Page;
use Webkul\Crm\Filament\Clusters\Sales\Resources\ActivityResource;
use Webkul\Crm\Filament\Clusters\Sales\Resources\ActivityResource\Actions\ScheduleActivityAction;
use Webkul\Crm\Filament\Clusters\Sales\Resources\ActivityResource\Widgets\ActivitiesByStatusChart;
use Webkul\Crm\Filament\Clusters\Sales\Resources\ActivityResource\Widgets\ActivitiesByTypeChart;
use Webkul\Crm\Filament\Clusters\Sales\Resources\ActivityResource\Widgets\ActivitiesByUserChart;
use Webkul\Crm\Filament\Clusters\Sales\Resources\ActivityResource\Widgets\ActivityStatsOverview;
use Webkul\Crm\Filament\Concerns\HasCRMClusterNavigation;

class GraphActivities extends Page
{
    use HasCRMClusterNavigation;

    protected static string $resource = ActivityResource::class;

    public static function canAccess(array $parameters = []): bool
    {
        return ActivityResource::canViewAny();
    }

    public function getTitle(): string
    {
        return __('crm::filament/clusters/sales/resources/activity.navigation.title');
    }

    public function getBreadcrumb(): string
    {
        return __('crm::filament/clusters/sales/resources/activity.views.graph');
    }

    public function getRecordNavigationTabs(): array
    {
        return ActivityResource::getViewTabs('graph');
    }

    protected function getHeaderActions(): array
    {
        return [
            ScheduleActivityAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            ActivityStatsOverview::class,
            ActivitiesByStatusChart::class,
            ActivitiesByTypeChart::class,
            ActivitiesByUserChart::class,
        ];
    }

    public function getHeaderWidgetsColumns(): int|array
    {
        return 2;
    }
}
