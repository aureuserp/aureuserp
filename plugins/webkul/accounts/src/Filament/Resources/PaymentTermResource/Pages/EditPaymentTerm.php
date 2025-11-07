<?php

namespace Webkul\Account\Filament\Resources\PaymentTermResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Webkul\Account\Filament\Resources\PaymentTermResource;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class EditPaymentTerm extends EditRecord
{
    use HasRecordNavigationTabs;

    protected static string $resource = PaymentTermResource::class;

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
