<?php

namespace Webkul\Account\Filament\Resources\CashRoundingResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Webkul\Account\Filament\Resources\CashRoundingResource;

class EditCashRounding extends EditRecord
{
    protected static string $resource = CashRoundingResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title(__('accounts::filament/resources/cash-rounding/pages/edit-cash-rounding.notification.title'))
            ->body(__('accounts::filament/resources/cash-rounding/pages/edit-cash-rounding.notification.body'));
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make()
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('accounts::filament/resources/cash-rounding/pages/edit-cash-rounding.header-actions.delete.notification.title'))
                        ->body(__('accounts::filament/resources/cash-rounding/pages/edit-cash-rounding.header-actions.delete.notification.body'))
                ),
        ];
    }
}
