<?php

namespace Webkul\Maintenance\Filament\Clusters\Maintenance\Resources\MaintenanceRequestResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Webkul\Maintenance\Filament\Clusters\Maintenance\Resources\MaintenanceRequestResource;
use Webkul\TableViews\Filament\Components\PresetView;
use Webkul\TableViews\Filament\Concerns\HasTableViews;

class ListMaintenanceRequests extends ListRecords
{
    use HasTableViews;

    protected static string $resource = MaintenanceRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('maintenance::filament/clusters/maintenance/resources/maintenance-request/pages/list-maintenance-requests.header-actions.create.label'))
                ->icon('heroicon-o-plus-circle')
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('maintenance::filament/clusters/maintenance/resources/maintenance-request/pages/list-maintenance-requests.header-actions.create.notification.title'))
                        ->body(__('maintenance::filament/clusters/maintenance/resources/maintenance-request/pages/list-maintenance-requests.header-actions.create.notification.body')),
                ),
        ];
    }

    public function getPresetTableViews(): array
    {
        return [
            'my_maintenances' => PresetView::make(__('maintenance::filament/clusters/maintenance/resources/maintenance-request/pages/list-maintenance-requests.tabs.my-maintenances'))
                ->icon('heroicon-s-user')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('user_id', Auth::id())),

            'todo' => PresetView::make(__('maintenance::filament/clusters/maintenance/resources/maintenance-request/pages/list-maintenance-requests.tabs.todo'))
                ->icon('heroicon-s-list-bullet')
                ->favorite()
                ->setAsDefault()
                ->modifyQueryUsing(fn (Builder $query) => $query->whereHas('stage', fn (Builder $query) => $query->where('done', false))),

            'done' => PresetView::make(__('maintenance::filament/clusters/maintenance/resources/maintenance-request/pages/list-maintenance-requests.tabs.done'))
                ->icon('heroicon-s-check-circle')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query) => $query->whereHas('stage', fn (Builder $query) => $query->where('done', true))),

            'high_priority' => PresetView::make(__('maintenance::filament/clusters/maintenance/resources/maintenance-request/pages/list-maintenance-requests.tabs.high-priority'))
                ->icon('heroicon-s-star')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('priority', '>', 0)),

            'unscheduled' => PresetView::make(__('maintenance::filament/clusters/maintenance/resources/maintenance-request/pages/list-maintenance-requests.tabs.unscheduled'))
                ->icon('heroicon-s-calendar')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereNull('scheduled_at')),

            'cancelled' => PresetView::make(__('maintenance::filament/clusters/maintenance/resources/maintenance-request/pages/list-maintenance-requests.tabs.cancelled'))
                ->icon('heroicon-s-archive-box')
                ->modifyQueryUsing(fn (Builder $query) => $query->onlyTrashed()),
        ];
    }
}
