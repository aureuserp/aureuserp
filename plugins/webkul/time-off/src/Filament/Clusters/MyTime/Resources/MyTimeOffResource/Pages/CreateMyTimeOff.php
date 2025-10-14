<?php

namespace Webkul\TimeOff\Filament\Clusters\MyTime\Resources\MyTimeOffResource\Pages;

use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Webkul\TimeOff\Filament\Clusters\MyTime\Resources\MyTimeOffResource;
use Webkul\TimeOff\Traits\TimeOffHelper;

class CreateMyTimeOff extends CreateRecord
{
    use TimeOffHelper;

    public function getSubNavigation(): array
    {
        if (filled($cluster = static::getCluster())) {
            return $this->generateNavigationItems($cluster::getClusteredComponents());
        }

        return [];
    }

    protected static string $resource = MyTimeOffResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function getCreatedNotification(): Notification
    {
        return Notification::make()
            ->success()
            ->title(__('time-off::filament/clusters/my-time/resources/my-time-off/pages/create-time-off.notification.success.title'))
            ->body(__('time-off::filament/clusters/my-time/resources/my-time-off/pages/create-time-off.notification.success.body'));
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return $this->mutateTimeOffData($data, $this->record?->id);

    }
}
