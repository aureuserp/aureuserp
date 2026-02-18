<?php

namespace Webkul\Account\Filament\Resources\JournalResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Webkul\Account\Filament\Resources\JournalResource;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class ViewJournal extends ViewRecord
{
    use HasRecordNavigationTabs;

    protected static string $resource = JournalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('accounts::filament/resources/journal/pages/view-journal.header-actions.delete.notification.title'))
                        ->body(__('accounts::filament/resources/journal/pages/view-journal.header-actions.delete.notification.body'))
                ),
        ];
    }
}
