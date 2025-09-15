<?php

namespace Webkul\Account\Filament\Resources\PaymentTermResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Resources\Pages\EditRecord;
use Webkul\Account\Filament\Resources\PaymentTermResource;

class EditPaymentTerm extends EditRecord
{
    protected static string $resource = PaymentTermResource::class;

    public static function getSubNavigationPosition(): SubNavigationPosition
    {
        return SubNavigationPosition::Top;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make()
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('accounts::filament/resources/payment-term/pages/edit-payment-term.header-actions.delete.notification.title'))
                        ->body(__('accounts::filament/resources/payment-term/pages/edit-payment-term.header-actions.delete.notification.body'))
                ),
        ];
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title(__('accounts::filament/resources/payment-term/pages/edit-payment-term.notification.title'))
            ->body(__('accounts::filament/resources/payment-term/pages/edit-payment-term.notification.body'));
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return $data;
    }
}
