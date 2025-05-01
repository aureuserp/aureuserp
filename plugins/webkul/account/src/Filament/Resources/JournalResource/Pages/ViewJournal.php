<?php

namespace Webkul\Account\Filament\Resources\JournalResource\Pages;

use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Webkul\Account\Filament\Resources\JournalResource;

class ViewJournal extends ViewRecord
{
    protected static string $resource = JournalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make()
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('accounts::filament/resources/journal/pages/view-journal.notification.title'))
                        ->body(__('accounts::filament/resources/journal/pages/view-journal.notification.body'))
                ),
        ];
    }
}
