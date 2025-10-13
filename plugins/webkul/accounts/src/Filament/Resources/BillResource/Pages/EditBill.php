<?php

namespace Webkul\Account\Filament\Resources\BillResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;
use Webkul\Account\Facades\Account;
use Webkul\Account\Filament\Resources\BillResource;
use Webkul\Account\Filament\Resources\BillResource\Actions\CreditNoteAction;
use Webkul\Account\Filament\Resources\InvoiceResource\Actions as BaseActions;
use Webkul\Chatter\Filament\Actions as ChatterActions;
use Webkul\Support\Concerns\HasRepeaterColumnManager;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class EditBill extends EditRecord
{
    use HasRecordNavigationTabs, HasRepeaterColumnManager;

    protected static string $resource = BillResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title(__('accounts::filament/resources/bill/pages/edit-bill.notification.title'))
            ->body(__('accounts::filament/resources/bill/pages/edit-bill.notification.body'));
    }

    protected function getHeaderActions(): array
    {
        return [
            ChatterActions\ChatterAction::make()
                ->setResource($this->getResource()),
            BaseActions\PayAction::make(),
            BaseActions\ConfirmAction::make(),
            BaseActions\CancelAction::make(),
            BaseActions\ResetToDraftAction::make(),
            BaseActions\SetAsCheckedAction::make(),
            BaseActions\PrintAndSendAction::make(),
            CreditNoteAction::make(),
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $user = Auth::user();

        $record = $this->getRecord();

        $data['partner_id'] ??= $record->partner_id;
        $data['invoice_date'] ??= $record->invoice_date;
        $data['name'] ??= $record->name;
        $data['auto_post'] ??= $record->auto_post;
        $data['invoice_currency_rate'] ??= 1.0;

        return $data;
    }

    protected function afterSave(): void
    {
        Account::computeAccountMove($this->getRecord());
    }
}
