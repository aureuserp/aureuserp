<?php

namespace Webkul\Account\Filament\Resources\PaymentResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Webkul\Account\Filament\Resources\PaymentResource;
use Webkul\Account\Filament\Resources\PaymentResource\Actions as BaseActions;
use Webkul\Chatter\Filament\Actions\ChatterAction;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class EditPayment extends EditRecord
{
    use HasRecordNavigationTabs;

    protected static string $resource = PaymentResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title(__('accounts::filament/resources/payment/pages/edit-payment.notification.title'))
            ->body(__('accounts::filament/resources/payment/pages/edit-payment.notification.body'));
    }

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
            DeleteAction::make(),
        ];
    }
}
