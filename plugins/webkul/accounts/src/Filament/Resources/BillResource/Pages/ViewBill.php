<?php

namespace Webkul\Account\Filament\Resources\BillResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Webkul\Account\Filament\Resources\BillResource;
use Webkul\Account\Filament\Resources\BillResource\Actions\CreditNoteAction;
use Webkul\Account\Filament\Resources\InvoiceResource\Actions as BaseActions;
use Webkul\Chatter\Filament\Actions\ChatterAction;
use Webkul\Support\Concerns\HasRepeatableEntryColumnManager;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class ViewBill extends ViewRecord
{
    use HasRecordNavigationTabs, HasRepeatableEntryColumnManager;

    protected static string $resource = BillResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ChatterAction::make()
                ->resource($this->getResource()),
            BaseActions\PayAction::make(),
            BaseActions\ConfirmAction::make(),
            BaseActions\CancelAction::make(),
            BaseActions\ResetToDraftAction::make(),
            BaseActions\SetAsCheckedAction::make(),
            CreditNoteAction::make(),
            DeleteAction::make()
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('accounts::filament/resources/bill/pages/view-bill.header-actions.delete.notification.title'))
                        ->body(__('accounts::filament/resources/bill/pages/view-bill.header-actions.delete.notification.body'))
                ),
        ];
    }
}
