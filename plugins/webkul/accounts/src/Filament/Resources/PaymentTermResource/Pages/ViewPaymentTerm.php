<?php

namespace Webkul\Account\Filament\Resources\PaymentTermResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Webkul\Account\Filament\Resources\PaymentTermResource;
use Webkul\Account\Filament\Resources\PaymentTermResource\Tables\PaymentTermsTable;
use Webkul\Account\Models\PaymentTerm;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class ViewPaymentTerm extends ViewRecord
{
    use HasRecordNavigationTabs;

    protected static string $resource = PaymentTermResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->action(fn (PaymentTerm $record, DeleteAction $action) => PaymentTermsTable::runDeletion(
                    fn () => $record->delete(),
                    $action,
                ))
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('accounts::filament/resources/payment-term/pages/view-payment-term.header-actions.delete.notification.title'))
                        ->body(__('accounts::filament/resources/payment-term/pages/view-payment-term.header-actions.delete.notification.body'))
                ),
        ];
    }
}
