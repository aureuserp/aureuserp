<?php

namespace Webkul\Account\Filament\Resources\InvoiceResource\Pages;

use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Webkul\Account\Filament\Resources\InvoiceResource;
use Webkul\Account\Filament\Resources\InvoiceResource\Actions as BaseActions;

class ViewInvoice extends ViewRecord
{
    protected static string $resource = InvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            BaseActions\PayAction::make(),
            BaseActions\ConfirmAction::make(),
            BaseActions\CancelAction::make(),
            BaseActions\ResetToDraftAction::make(),
            BaseActions\SetAsCheckedAction::make(),
            Actions\DeleteAction::make()
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('accounts::filament/resources/invoice/pages/view-invoice.header-actions.delete.notification.title'))
                        ->body(__('accounts::filament/resources/invoice/pages/view-invoice.header-actions.delete.notification.body'))
                ),
        ];
    }
}
