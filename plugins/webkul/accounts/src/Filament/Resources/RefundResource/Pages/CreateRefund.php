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

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title(__('accounts::filament/resources/refund/pages/create-refund.notification.title'))
            ->body(__('accounts::filament/resources/refund/pages/create-refund.notification.body'));
    }

    public function mount(): void
    {
        parent::mount();

        $this->data['move_type'] ??= MoveType::IN_REFUND->value;

        $this->form->fill($this->data);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['move_type'] ??= MoveType::IN_REFUND;

        return $data;
    }

    protected function afterCreate(): void
    {
        AccountFacade::computeAccountMove($this->getRecord());
    }
}
