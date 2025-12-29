<?php

namespace Webkul\Account\Filament\Resources\PaymentTermResource\Pages;

use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Webkul\Account\Enums\DueTermValue;
use Webkul\Account\Filament\Resources\PaymentTermResource;

class CreatePaymentTerm extends CreateRecord
{
    protected static string $resource = PaymentTermResource::class;

    public function getSubNavigation(): array
    {
        if (filled($cluster = static::getCluster())) {
            return $this->generateNavigationItems($cluster::getClusteredComponents());
        }

        return [];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title(__('accounts::filament/resources/payment-term/pages/create-payment-term.notification.success.title'))
            ->body(__('accounts::filament/resources/payment-term/pages/create-payment-term.notification.success.body'));
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = Auth::user();

        $data['creator_id'] = $user->id;
        $data['company_id'] = $user?->default_company_id;

        return $data;
    }

    protected function beforeCreate(): void
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
                ->title(__('accounts::filament/resources/payment-term/pages/create-payment-term.notification.validation-error.title'))
                ->body(__('accounts::filament/resources/payment-term/pages/create-payment-term.notification.validation-error.body'))
                ->send();

            $this->halt();
        }
    }
}
