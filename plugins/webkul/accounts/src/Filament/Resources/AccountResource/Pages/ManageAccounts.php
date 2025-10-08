<?php

namespace Webkul\Account\Filament\Resources\AccountResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Webkul\Account\Filament\Resources\AccountResource;
use Filament\Notifications\Notification;

class ManageAccounts extends ManageRecords
{
    protected static string $resource = AccountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->icon('heroicon-o-plus-circle')
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('accounts::filament/resources/account/pages/manage-accounts.header-actions.create.notification.title'))
                        ->body(__('accounts::filament/resources/account/pages/manage-accounts.header-actions.create.notification.body'))
                ),
        ];
    }
}
