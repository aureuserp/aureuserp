<?php

namespace Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\OrderResource\Actions;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Livewire\Component;
use Webkul\Purchase\Enums\OrderState;
use Webkul\Purchase\Models\Order;

class ConfirmReceiptDateAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'purchases.orders.confirm-receipt-date';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('purchases::filament/admin/clusters/orders/resources/order/actions/confirm-receipt-date.label'))
            ->requiresConfirmation()
            ->color('gray')
            ->action(function (Order $record, Component $livewire): void {
                $record->update([
                    'mail_reminder_confirmed' => true,
                ]);

                $livewire->updateForm();

                Notification::make()
                    ->title(__('purchases::filament/admin/clusters/orders/resources/order/actions/confirm-receipt-date.action.notification.success.title'))
                    ->body(__('purchases::filament/admin/clusters/orders/resources/order/actions/confirm-receipt-date.action.notification.success.body'))
                    ->success()
                    ->send();
            })
            ->hidden(fn () => ! in_array($this->getRecord()->state, [
                    OrderState::PURCHASE,
                    OrderState::DONE,
                ])
                || $this->getRecord()->mail_reminder_confirmed
                || ! $this->getRecord()->planned_at
                || true
            );
    }
}
