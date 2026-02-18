<?php

namespace Webkul\Account\Filament\Resources\PaymentResource\Pages;

use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Webkul\Account\Filament\Resources\PaymentResource;

class CreatePayment extends CreateRecord
{
    public function getSubNavigation(): array
    {
        if (filled($cluster = static::getCluster())) {
            return $this->generateNavigationItems($cluster::getClusteredComponents());
        }

        return [];
    }

    protected static string $resource = PaymentResource::class;

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
}
