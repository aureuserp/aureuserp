<?php

namespace Webkul\Account\Filament\Resources\PaymentTermResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Resources\Pages\ViewRecord;
use Webkul\Account\Filament\Resources\PaymentTermResource;

class ViewPaymentTerm extends ViewRecord
{
    protected static string $resource = PaymentTermResource::class;

    public static function getSubNavigationPosition(): SubNavigationPosition
    {
        return SubNavigationPosition::Top;
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
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
