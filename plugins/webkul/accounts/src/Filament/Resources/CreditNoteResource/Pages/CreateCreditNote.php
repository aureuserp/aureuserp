<?php

namespace Webkul\Account\Filament\Resources\CreditNoteResource\Pages;

use Filament\Notifications\Notification;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Facades\Account as AccountFacade;
use Webkul\Account\Filament\Resources\CreditNoteResource;
use Webkul\Account\Filament\Resources\InvoiceResource\Pages\CreateInvoice as CreateRecord;

class CreateCreditNote extends CreateRecord
{
    protected static string $resource = CreditNoteResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title(__('accounts::filament/resources/credit-note/pages/create-credit-note.notification.title'))
            ->body(__('accounts::filament/resources/credit-note/pages/create-credit-note.notification.body'));
    }

    public function mount(): void
    {
        parent::mount();

        $this->data['move_type'] ??= MoveType::OUT_REFUND->value;

        $this->form->fill($this->data);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['move_type'] ??= MoveType::OUT_REFUND;

        return $data;
    }

    protected function afterCreate(): void
    {
        AccountFacade::computeAccountMove($this->getRecord());
    }
}
