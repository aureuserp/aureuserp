<?php

namespace Webkul\Support\Filament\Resources\CalendarResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Webkul\Support\Filament\Resources\CalendarResource;
use Webkul\Support\Models\Calendar;

class ListCalendars extends ListRecords
{
    protected static string $resource = CalendarResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('support::filament/resources/calendar/pages/list-calendar.header-actions.create.label'))
                ->icon('heroicon-o-plus-circle')
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('support::filament/resources/calendar/pages/list-calendar.header-actions.create.notification.title'))
                        ->body(__('support::filament/resources/calendar/pages/list-calendar.header-actions.create.notification.body')),
                ),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make(__('support::filament/resources/calendar/pages/list-calendar.tabs.all'))
                ->badge(Calendar::count()),
            'archived' => Tab::make(__('support::filament/resources/calendar/pages/list-calendar.tabs.archived'))
                ->badge(Calendar::onlyTrashed()->count())
                ->modifyQueryUsing(function ($query) {
                    return $query->onlyTrashed();
                }),
        ];
    }
}
