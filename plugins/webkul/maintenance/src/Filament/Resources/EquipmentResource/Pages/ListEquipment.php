<?php

namespace Webkul\Maintenance\Filament\Resources\EquipmentResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Webkul\Maintenance\Filament\Resources\EquipmentResource;
use Webkul\TableViews\Filament\Components\PresetView;
use Webkul\TableViews\Filament\Concerns\HasTableViews;

class ListEquipment extends ListRecords
{
    use HasTableViews;

    protected static string $resource = EquipmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('maintenance::filament/resources/equipment/pages/list-equipment.header-actions.create.label'))
                ->icon('heroicon-o-plus-circle')
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('maintenance::filament/resources/equipment/pages/list-equipment.header-actions.create.notification.title'))
                        ->body(__('maintenance::filament/resources/equipment/pages/list-equipment.header-actions.create.notification.body')),
                ),
        ];
    }

    public function getPresetTableViews(): array
    {
        return [
            'my_equipment' => PresetView::make(__('maintenance::filament/resources/equipment/pages/list-equipment.tabs.my-equipment'))
                ->icon('heroicon-s-user')
                ->favorite()
                ->setAsDefault()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('owner_user_id', Auth::id())),

            'assigned' => PresetView::make(__('maintenance::filament/resources/equipment/pages/list-equipment.tabs.assigned'))
                ->icon('heroicon-s-check-circle')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query) => $query->whereNotNull('owner_user_id')),

            'unassigned' => PresetView::make(__('maintenance::filament/resources/equipment/pages/list-equipment.tabs.unassigned'))
                ->icon('heroicon-s-minus-circle')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query) => $query->whereNull('owner_user_id')),

            'under_maintenance' => PresetView::make(__('maintenance::filament/resources/equipment/pages/list-equipment.tabs.under-maintenance'))
                ->icon('heroicon-s-wrench-screwdriver')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('maintenance_open_count', '>', 0)),

            'archived' => PresetView::make(__('maintenance::filament/resources/equipment/pages/list-equipment.tabs.archived'))
                ->icon('heroicon-s-archive-box')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query) => $query->onlyTrashed()),
        ];
    }
}
