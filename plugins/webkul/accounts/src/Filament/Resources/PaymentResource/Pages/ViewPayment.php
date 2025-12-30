<?php

namespace Webkul\Account\Filament\Resources\PaymentResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Webkul\Account\Filament\Resources\PaymentResource;
use Webkul\Account\Filament\Resources\PaymentResource\Actions as BaseActions;
use Webkul\Chatter\Filament\Actions\ChatterAction;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class ViewPayment extends ViewRecord
{
    use HasRecordNavigationTabs;

    protected static string $resource = PaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ChatterAction::make()
                ->resource(static::$resource),
            BaseActions\ConfirmAction::make(),
            BaseActions\ResetToDraftAction::make(),
            BaseActions\MarkAsSendAdnUnsentAction::make(),
            BaseActions\CancelAction::make(),
            BaseActions\RejectAction::make(),
            DeleteAction::make()
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('accounts::filament/resources/payment/pages/view-payment.header-actions.delete.notification.title'))
                        ->body(__('accounts::filament/resources/payment/pages/view-payment.header-actions.delete.notification.body'))
                ),
        ];
    }
}
