<?php

namespace Webkul\Account\Filament\Resources\RefundResource\Pages;

use Filament\Notifications\Notification;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Facades\Account as AccountFacade;
use Webkul\Account\Filament\Resources\BillResource\Pages\CreateBill as CreateBaseRefund;
use Webkul\Account\Filament\Resources\RefundResource;

class CreateRefund extends CreateBaseRefund
{
    protected static string $resource = RefundResource::class;

    protected function getMoveType(): MoveType
    {
        return MoveType::IN_REFUND;
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title(__('accounts::filament/resources/refund/pages/create-refund.notification.title'))
            ->body(__('accounts::filament/resources/refund/pages/create-refund.notification.body'));
    }

    protected function afterCreate(): void
    {
        AccountFacade::computeAccountMove($this->getRecord());
    }
}
