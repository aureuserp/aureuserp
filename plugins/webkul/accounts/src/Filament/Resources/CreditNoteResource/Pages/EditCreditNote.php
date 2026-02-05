<?php

namespace Webkul\Account\Filament\Resources\CreditNoteResource\Pages;

use Filament\Notifications\Notification;
use Webkul\Account\Facades\Account as AccountFacade;
use Webkul\Account\Filament\Resources\CreditNoteResource;
use Webkul\Account\Filament\Resources\InvoiceResource\Actions as BaseActions;
use Webkul\Account\Filament\Resources\InvoiceResource\Pages\EditInvoice as EditRecord;

class EditCreditNote extends EditRecord
{
    protected static string $resource = CreditNoteResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title(__('accounts::filament/resources/credit-note/pages/edit-credit-note.notification.title'))
            ->body(__('accounts::filament/resources/credit-note/pages/edit-credit-note.notification.body'));
    }

    protected function getHeaderActions(): array
    {
        $predefinedActions = parent::getHeaderActions();

        $predefinedActions = collect($predefinedActions)->filter(function ($action) {
            return ! in_array($action->getName(), [
                'customers.invoice.set-as-checked',
                'customers.invoice.credit-note',
            ]);
        })->map(function ($action) {
            if ($action->getName() == 'customers.invoice.preview') {
                return BaseActions\PreviewAction::make()
                    ->modalHeading(__('accounts::filament/resources/credit-note/pages/edit-credit-note.header-actions.preview.modal-heading'))
                    ->setTemplate('accounts::credit-note/actions/preview.index');
            }

            return $action;
        })
            ->values()
            ->all();

        return $predefinedActions;
    }

    protected function afterSave(): void
    {
        AccountFacade::computeAccountMove($this->getRecord());
    }
}
