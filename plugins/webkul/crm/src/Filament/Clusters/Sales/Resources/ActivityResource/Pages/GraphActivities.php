<?php

namespace Webkul\Crm\Filament\Clusters\Sales\Resources\ActivityResource\Pages;

use Filament\Actions\Action;
use Filament\Resources\Pages\Page;
use Livewire\Attributes\Url;
use Webkul\Crm\Filament\Clusters\Sales\Resources\ActivityResource;
use Webkul\Crm\Filament\Clusters\Sales\Resources\ActivityResource\Widgets\ActivitiesByStatusChart;
use Webkul\Crm\Filament\Clusters\Sales\Resources\ActivityResource\Widgets\ActivitiesByTypeChart;
use Webkul\Crm\Filament\Clusters\Sales\Resources\ActivityResource\Widgets\ActivitiesByUserChart;
use Webkul\Crm\Filament\Clusters\Sales\Resources\ActivityResource\Widgets\ActivityStatsOverview;
use Webkul\Crm\Filament\Concerns\HasCrmClusterNavigation;

class GraphActivities extends Page
{
    use HasCrmClusterNavigation;

    protected static string $resource = ActivityResource::class;

    #[Url]
    public string $scope = 'mine';

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

    protected function getHeaderActions(): array
    {
        return [
            Action::make('toggleScope')
                ->label(fn (): string => $this->scope === 'all'
                    ? __('crm::filament/clusters/sales/resources/activity/pages/graph-activities.scope.show-mine')
                    : __('crm::filament/clusters/sales/resources/activity/pages/graph-activities.scope.show-all'))
                ->icon(fn (): string => $this->scope === 'all' ? 'heroicon-o-user' : 'heroicon-o-users')
                ->color('gray')
                ->link()
                ->url(fn (): string => ActivityResource::getUrl('graph', ['scope' => $this->scope === 'all' ? 'mine' : 'all'])),
            ...ActivityResource::getViewSwitcherActions('graph'),
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

    public function getWidgetData(): array
    {
        return [
            'scope' => $this->scope,
        ];
    }
}
