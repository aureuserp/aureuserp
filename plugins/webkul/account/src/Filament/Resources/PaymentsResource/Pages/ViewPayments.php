<?php

namespace Webkul\Account\Filament\Resources\PaymentsResource\Pages;

use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Webkul\Account\Filament\Resources\PaymentsResource;
use Webkul\Account\Filament\Resources\PaymentsResource\Actions as BaseActions;
use Webkul\Chatter\Filament\Actions as ChatterActions;

class ViewPayments extends ViewRecord
{
    protected static string $resource = PaymentsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ChatterActions\ChatterAction::make()
                ->setResource(static::$resource),
            Actions\EditAction::make(),
            Actions\DeleteAction::make()
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('accounts::filament/resources/payment/pages/view-payment.header-actions.delete.notification.title'))
                        ->body(__('accounts::filament/resources/payment/pages/view-payment.header-actions.delete.notification.body'))
                ),
            BaseActions\ConfirmAction::make(),
            BaseActions\ResetToDraftAction::make(),
            BaseActions\MarkAsSendAdnUnsentAction::make(),
            BaseActions\CancelAction::make(),
            BaseActions\RejectAction::make(),
        ];
    }
}
