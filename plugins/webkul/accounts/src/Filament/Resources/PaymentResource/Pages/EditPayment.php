<?php

namespace Webkul\Account\Filament\Resources\PaymentResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Webkul\Account\Filament\Resources\PaymentResource;
use Webkul\Account\Filament\Resources\PaymentResource\Actions as BaseActions;
use Webkul\Chatter\Filament\Actions\ChatterAction;
use Webkul\Support\Traits\HasRecordNavigationTabs;
use Webkul\Support\Traits\RefreshesRecordState;

class EditPayment extends EditRecord
{
    use HasRecordNavigationTabs;
    use RefreshesRecordState;

    protected static string $resource = PaymentResource::class;

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
                ->resource(static::$resource)
                ->activityPlans($this->getRecord()->activityPlans()),
            BaseActions\ConfirmAction::make(),
            BaseActions\ResetToDraftAction::make(),
            BaseActions\MarkAsSendAdnUnsentAction::make(),
            BaseActions\CancelAction::make(),
            BaseActions\RejectAction::make(),
            DeleteAction::make(),
        ];
    }

    public function refreshFormData(array $statePaths): void
    {
        parent::refreshFormData($statePaths);

        $this->rememberData();
    }
}
