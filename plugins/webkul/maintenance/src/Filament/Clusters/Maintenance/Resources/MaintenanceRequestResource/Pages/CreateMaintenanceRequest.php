<?php

namespace Webkul\Maintenance\Filament\Clusters\Maintenance\Resources\MaintenanceRequestResource\Pages;

use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Webkul\Maintenance\Filament\Clusters\Maintenance\Resources\MaintenanceRequestResource;

class CreateMaintenanceRequest extends CreateRecord
{
    protected static string $resource = MaintenanceRequestResource::class;

    public function getSubNavigation(): array
    {
        if (filled($cluster = static::getCluster())) {
            return $this->generateNavigationItems($cluster::getClusteredComponents());
        }

        return [];
    }

    protected function getCreatedNotification(): Notification
    {
        return Notification::make()
            ->success()
            ->title(__('maintenance::filament/clusters/maintenance/resources/maintenance-request/pages/create-maintenance-request.notification.title'))
            ->body(__('maintenance::filament/clusters/maintenance/resources/maintenance-request/pages/create-maintenance-request.notification.body'));
    }
}
