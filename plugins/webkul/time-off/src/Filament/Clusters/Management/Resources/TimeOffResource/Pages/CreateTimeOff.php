<?php

namespace Webkul\TimeOff\Filament\Clusters\Management\Resources\TimeOffResource\Pages;

use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Webkul\TimeOff\Filament\Clusters\Management\Resources\TimeOffResource;
use Webkul\TimeOff\Traits\TimeOffHelper;

class CreateTimeOff extends CreateRecord
{
    use TimeOffHelper;

    protected static string $resource = TimeOffResource::class;

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
            ->title(__('time-off::filament/clusters/management/resources/time-off/pages/create-time-off.notification.title'))
            ->body(__('time-off::filament/clusters/management/resources/time-off/pages/create-time-off.notification.body'));
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return $this->mutateTimeOffData($data, $this->record?->id);

    }
}
