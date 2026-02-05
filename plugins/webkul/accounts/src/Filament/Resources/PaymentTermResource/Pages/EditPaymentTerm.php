<?php

namespace Webkul\Account\Filament\Resources\PaymentTermResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Webkul\Account\Enums\DueTermValue;
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
            ->title(__('accounts::filament/resources/payment-term/pages/edit-payment-term.notification.success.title'))
            ->body(__('accounts::filament/resources/payment-term/pages/edit-payment-term.notification.success.body'));
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return $data;
    }

    protected function beforeSave(): void
    {
        $this->validateDueTerms($this->data['dueTerms'] ?? []);
    }

    protected function validateDueTerms(array $dueTerms): void
    {
        $totalPercent = collect($dueTerms)
            ->where('value', DueTermValue::PERCENT)
            ->sum('value_amount');

        if ($totalPercent != 100) {
            Notification::make()
                ->danger()
                ->title(__('accounts::filament/resources/payment-term/pages/edit-payment-term.notification.validation-error.title'))
                ->body(__('accounts::filament/resources/payment-term/pages/edit-payment-term.notification.validation-error.body'))
                ->send();

            $this->halt();
        }
    }
}
