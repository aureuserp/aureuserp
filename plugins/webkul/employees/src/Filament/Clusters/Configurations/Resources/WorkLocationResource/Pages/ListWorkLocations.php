<?php

namespace Webkul\Employee\Filament\Clusters\Configurations\Resources\WorkLocationResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Webkul\Employee\Filament\Clusters\Configurations\Resources\WorkLocationResource;

class ListWorkLocations extends ListRecords
{
    protected static string $resource = WorkLocationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('employees::filament/clusters/configurations/resources/work-location/pages/list-work-location.header-actions.create.label'))
                ->icon('heroicon-o-plus-circle')
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('employees::filament/clusters/configurations/resources/work-location/pages/list-work-location.header-actions.create.notification.title'))
                        ->body(__('employees::filament/clusters/configurations/resources/work-location/pages/list-work-location.header-actions.create.notification.body')),
                ),
        ];
    }

    public function getTabs(): array
    {
        return [
            null     => Tab::make(__('employees::filament/clusters/configurations/resources/work-location/pages/list-work-location.tabs.all')),
            'office' => Tab::make(__('employees::filament/clusters/configurations/resources/work-location/pages/list-work-location.tabs.office'))
                ->icon('heroicon-m-building-office-2')
                ->query(fn ($query) => $query->where('location_type', 'office')),
            'home'   => Tab::make(__('employees::filament/clusters/configurations/resources/work-location/pages/list-work-location.tabs.home'))
                ->icon('heroicon-m-home')->query(fn ($query) => $query->where('location_type', 'home')),
            'other'  => Tab::make(__('employees::filament/clusters/configurations/resources/work-location/pages/list-work-location.tabs.other'))
                ->icon('heroicon-m-map-pin')->query(fn ($query) => $query->where('location_type', 'other')),
        ];
    }
}
