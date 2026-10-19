<?php

namespace Webkul\Crm\Filament\Clusters\Sales\Resources\ActivityResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Webkul\Crm\Filament\Clusters\Sales\Resources\ActivityResource;
use Webkul\TableViews\Filament\Components\PresetView;
use Webkul\TableViews\Filament\Concerns\HasTableViews;

class ListActivities extends ListRecords
{
    use HasTableViews;

    protected static string $resource = ActivityResource::class;

    public function getTitle(): string
    {
        return __('crm::filament/clusters/sales/resources/activity.navigation.title');
    }

    public function getBreadcrumb(): string
    {
        return __('crm::filament/clusters/sales/resources/activity.views.list');
    }

    protected function getHeaderActions(): array
    {
        return ActivityResource::getViewSwitcherActions('index');
    }

    public function getPresetTableViews(): array
    {
        $mine = fn (Builder $query): Builder => $query->where('assigned_to', Auth::id());

        return [
            'my_activities' => PresetView::make(__('crm::filament/clusters/sales/resources/activity/pages/list-activities.tabs.my-activities'))
                ->icon('heroicon-s-user')
                ->favorite()
                ->setAsDefault()
                ->modifyQueryUsing($mine),

            'overdue' => PresetView::make(__('crm::filament/clusters/sales/resources/activity/pages/list-activities.tabs.overdue'))
                ->icon('heroicon-s-exclamation-circle')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query): Builder => $mine($query)->overdue()),

            'today' => PresetView::make(__('crm::filament/clusters/sales/resources/activity/pages/list-activities.tabs.today'))
                ->icon('heroicon-s-clock')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query): Builder => $mine($query)->dueToday()),

            'planned' => PresetView::make(__('crm::filament/clusters/sales/resources/activity/pages/list-activities.tabs.planned'))
                ->icon('heroicon-s-calendar-days')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query): Builder => $mine($query)->planned()),

            'all' => PresetView::make(__('crm::filament/clusters/sales/resources/activity/pages/list-activities.tabs.all'))
                ->icon('heroicon-s-users')
                ->favorite(),
        ];
    }
}
