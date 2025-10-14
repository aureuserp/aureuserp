<?php

namespace Webkul\TimeOff\Filament\Clusters\Management\Resources\AllocationResource\Pages;

use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Webkul\TimeOff\Filament\Clusters\Management\Resources\AllocationResource;

class CreateAllocation extends CreateRecord
{
    protected static string $resource = AllocationResource::class;

    public function getSubNavigation(): array
    {
        if (filled($cluster = static::getCluster())) {
            return $this->generateNavigationItems($cluster::getClusteredComponents());
        }

        return [];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function getCreatedNotification(): Notification
    {
        return Notification::make()
            ->success()
            ->title(__('time-off::filament/clusters/management/resources/allocation/pages/create-allocation.notification.title'))
            ->body(__('time-off::filament/clusters/management/resources/allocation/pages/create-allocation.notification.body'));
    }
}
