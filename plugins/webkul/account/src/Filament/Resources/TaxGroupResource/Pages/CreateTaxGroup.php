<?php

namespace Webkul\Account\Filament\Resources\TaxGroupResource\Pages;

use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Webkul\Account\Filament\Resources\TaxGroupResource;

class CreateTaxGroup extends CreateRecord
{
    protected static string $resource = TaxGroupResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title(__('accounts::filament/resources/tax-group/pages/create-tax-group.notification.title'))
            ->body(__('accounts::filament/resources/tax-group/pages/create-tax-group.notification.body'));
    }
}
