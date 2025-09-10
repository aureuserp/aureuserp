<?php

namespace Webkul\Employee\Filament\Clusters\Configurations\Resources\Calendar\Pages;

use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Webkul\Employee\Filament\Clusters\Configurations\Resources\Calendar\CalendarResource;

class CreateCalendar extends CreateRecord
{
    protected static string $resource = CalendarResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function getCreatedNotification(): Notification
    {
        return Notification::make()
            ->success()
            ->title(__('employees::filament/clusters/configurations/resources/calendar/pages/create-calendar.notification.title'))
            ->body(__('employees::filament/clusters/configurations/resources/calendar/pages/create-calendar.notification.body'));
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return $data;
    }
}
