<?php

namespace Webkul\Support\Filament\Resources\CalendarResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Webkul\Support\Filament\Resources\CalendarResource;

class EditCalendar extends EditRecord
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

    protected function getSavedNotification(): Notification
    {
        return Notification::make()
            ->success()
            ->title(__('support::filament/resources/calendar/pages/edit-calendar.notification.title'))
            ->body(__('support::filament/resources/calendar/pages/edit-calendar.notification.body'));
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make()
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('support::filament/resources/calendar/pages/edit-calendar.header-actions.delete.notification.title'))
                        ->body(__('support::filament/resources/calendar/pages/edit-calendar.header-actions.delete.notification.body')),
                ),
        ];
    }
}
