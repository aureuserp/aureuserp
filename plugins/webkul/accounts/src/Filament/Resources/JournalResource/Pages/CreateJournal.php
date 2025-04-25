<?php

namespace Webkul\Account\Filament\Resources\JournalResource\Pages;

use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Webkul\Account\Enums\CommunicationStandard;
use Webkul\Account\Enums\CommunicationType;
use Webkul\Account\Filament\Resources\JournalResource;

class CreateJournal extends CreateRecord
{
    protected static string $resource = JournalResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title(__('accounts::filament/resources/journal/pages/create-journal.notification.title'))
            ->body(__('accounts::filament/resources/journal/pages/create-journal.notification.body'));
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['creator_id'] = Auth::user()->id;

        $data['invoice_reference_type'] = $data['invoice_reference_type'] ?? CommunicationType::INVOICE->value;
        $data['invoice_reference_model'] = $data['invoice_reference_model'] ?? CommunicationStandard::AUREUS->value;

        return $data;
    }
}
