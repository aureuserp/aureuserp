<?php

namespace Webkul\Account\Filament\Resources\AccountResource\Pages;

use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Webkul\Account\Filament\Resources\AccountResource;

class ViewAccount extends ViewRecord
{
    protected static string $resource = AccountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make()
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('accounts::filament/resources/account/pages/view-account.header-actions.delete.notification.title'))
                        ->body(__('accounts::filament/resources/account/pages/view-account.header-actions.delete.notification.body'))
                ),
        ];
    }
}
