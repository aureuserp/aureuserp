<?php

namespace Webkul\Account\Filament\Resources\InvoiceResource\Pages;

use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Webkul\Account\Enums;
use Webkul\Account\Facades\Account;
use Webkul\Account\Filament\Resources\InvoiceResource;

class CreateInvoice extends CreateRecord
{
    protected static string $resource = InvoiceResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title(__('accounts::filament/resources/invoice/pages/create-invoice.notification.title'))
            ->body(__('accounts::filament/resources/invoice/pages/create-invoice.notification.body'));
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['move_type'] ??= Enums\MoveType::OUT_INVOICE;

        $data['date'] = now();

        return $data;
    }

    protected function afterCreate(): void
    {
        Account::computeAccountMove($this->getRecord());
    }
}
