<?php

namespace Webkul\Account\Filament\Resources\PaymentsResource\Pages;

use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Webkul\Account\Enums\PaymentStatus;
use Webkul\Account\Filament\Resources\PaymentsResource;

class CreatePayments extends CreateRecord
{
    public function getSubNavigation(): array
    {
        if (filled($cluster = static::getCluster())) {
            return $this->generateNavigationItems($cluster::getClusteredComponents());
        }

        return [];
    }

    protected static string $resource = PaymentsResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title(__('accounts::filament/resources/payment/pages/create-payment.notification.title'))
            ->body(__('accounts::filament/resources/payment/pages/create-payment.notification.body'));
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['state'] = PaymentStatus::DRAFT->value;
        $data['creator_id'] = Auth::user()->id;

        return $data;
    }
}
