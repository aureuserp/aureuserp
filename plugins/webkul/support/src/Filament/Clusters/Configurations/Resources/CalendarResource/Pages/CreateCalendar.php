<?php

namespace Webkul\Support\Filament\Clusters\Configurations\Resources\CalendarResource\Pages;

use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Webkul\Support\Filament\Clusters\Configurations\Resources\CalendarResource;

class CreateCalendar extends CreateRecord
{
    protected static string $resource = CalendarResource::class;

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
            ->title(__('support::filament/clusters/configurations/resources/calendar/pages/create-calendar.notification.title'))
            ->body(__('support::filament/clusters/configurations/resources/calendar/pages/create-calendar.notification.body'));
    }
}
