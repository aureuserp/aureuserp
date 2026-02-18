<?php

namespace Webkul\Account\Filament\Resources\PaymentTermResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Webkul\Account\Filament\Resources\PaymentTermResource;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class ViewPaymentTerm extends ViewRecord
{
    use HasRecordNavigationTabs;

    protected static string $resource = PaymentTermResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('accounts::filament/resources/payment-term/pages/view-payment-term.header-actions.delete.notification.title'))
                        ->body(__('accounts::filament/resources/payment-term/pages/view-payment-term.header-actions.delete.notification.body'))
                ),
        ];
    }
}
