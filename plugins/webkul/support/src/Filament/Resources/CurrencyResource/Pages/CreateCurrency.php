<?php

namespace Webkul\Support\Filament\Resources\CurrencyResource\Pages;

use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Webkul\Support\Filament\Resources\CurrencyResource;

class CreateCurrency extends CreateRecord
{
    protected static string $resource = CurrencyResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function getCreatedNotification(): Notification
    {
        return Notification::make()
            ->success()
            ->title(__('support::filament/resources/currency/pages/create-currency.notification.title'))
            ->body(__('support::filament/resources/currency/pages/create-currency.notification.body'));
    }
}