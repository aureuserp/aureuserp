<?php

namespace Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\Orders\Actions;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Livewire\Component;
use Webkul\Account\Enums\MoveState;
use Webkul\Purchase\Enums\OrderState;
use Webkul\Purchase\Facades\PurchaseOrder;
use Webkul\Purchase\Models\Order;

class CancelAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'purchases.orders.cancel';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('purchases::filament/admin/clusters/orders/resources/order/actions/cancel.label'))
            ->color('gray')
            ->requiresConfirmation()
            ->action(function (Order $record, Component $livewire): void {
                $record->lines->each(function ($move) {
                    if ($move->qty_received > 0) {
                        Notification::make()
                            ->title(__('purchases::filament/admin/clusters/orders/resources/order/actions/cancel.action.notification.warning.receipts.title'))
                            ->body(__('purchases::filament/admin/clusters/orders/resources/order/actions/cancel.action.notification.warning.receipts.body'))
                            ->warning()
                            ->send();

                        return;
                    }
                });

                $record->accountMoves->each(function ($move) {
                    if ($move->state !== MoveState::CANCEL) {
                        Notification::make()
                            ->title(__('purchases::filament/admin/clusters/orders/resources/order/actions/cancel.action.notification.warning.bills.title'))
                            ->body(__('purchases::filament/admin/clusters/orders/resources/order/actions/cancel.action.notification.warning.bills.body'))
                            ->warning()
                            ->send();

                        return;
                    }
                });

                $record = PurchaseOrder::cancelPurchaseOrder($record);

                $livewire->updateForm();

                Notification::make()
                    ->title(__('purchases::filament/admin/clusters/orders/resources/order/actions/cancel.action.notification.success.title'))
                    ->body(__('purchases::filament/admin/clusters/orders/resources/order/actions/cancel.action.notification.success.body'))
                    ->success()
                    ->send();
            })
            ->visible(fn () => ! in_array($this->getRecord()->state, [
                OrderState::DONE,
                OrderState::CANCELED,
            ]));
    }
}
