<?php

namespace Webkul\Support\Filament\Resources\CalendarResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Webkul\Support\Filament\Resources\CalendarResource;

class ViewCalendar extends ViewRecord
{
    protected static string $resource = CalendarResource::class;

    public function getSubNavigation(): array
    {
        if (filled($cluster = static::getCluster())) {
            return $this->generateNavigationItems($cluster::getClusteredComponents());
        }

        return [];
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            DeleteAction::make()
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('support::filament/resources/calendar/pages/view-calendar.header-actions.delete.notification.title'))
                        ->body(__('support::filament/resources/calendar/pages/view-calendar.header-actions.delete.notification.body')),
                ),
        ];
    }
}
