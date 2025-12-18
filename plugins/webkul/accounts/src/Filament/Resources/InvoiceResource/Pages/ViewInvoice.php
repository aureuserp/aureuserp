<?php

namespace Webkul\Account\Filament\Resources\InvoiceResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Webkul\Account\Filament\Resources\InvoiceResource;
use Webkul\Account\Filament\Resources\InvoiceResource\Actions as BaseActions;
use Webkul\Chatter\Filament\Actions as ChatterActions;
use Webkul\Support\Filament\Forms\Concerns\HasRepeatableEntryColumnManager;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class ViewInvoice extends ViewRecord
{
    use HasRecordNavigationTabs, HasRepeatableEntryColumnManager;

    protected static string $resource = InvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ChatterActions\ChatterAction::make()
                ->setResource($this->getResource()),
            BaseActions\PrintAndSendAction::make(),
            BaseActions\PreviewAction::make()
                ->setTemplate('accounts::invoice/actions/preview.index'),
            BaseActions\PayAction::make(),
            BaseActions\ConfirmAction::make(),
            BaseActions\CancelAction::make(),
            BaseActions\SetAsCheckedAction::make(),
            BaseActions\CreditNoteAction::make(),
            BaseActions\ResetToDraftAction::make(),
            DeleteAction::make()
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('accounts::filament/resources/invoice/pages/view-invoice.header-actions.delete.notification.title'))
                        ->body(__('accounts::filament/resources/invoice/pages/view-invoice.header-actions.delete.notification.body'))
                ),
        ];
    }
}
