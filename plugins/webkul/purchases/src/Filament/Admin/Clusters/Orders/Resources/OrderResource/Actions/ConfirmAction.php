<?php

namespace Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\OrderResource\Actions;

use Closure;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Throwable;
use Webkul\Purchase\Enums\OrderState;
use Webkul\Purchase\Facades\PurchaseOrder;
use Webkul\Purchase\Models\Order;

class ConfirmAction extends Action
{
    protected bool|Closure $hasDatabaseTransactions = true;

    public static function getDefaultName(): ?string
    {
        return 'purchases.orders.confirm';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('purchases::filament/admin/clusters/orders/resources/order/actions/confirm.label'))
            ->requiresConfirmation()
            ->color(fn (): string => $this->getRecord()->state === OrderState::DRAFT ? 'gray' : 'primary')
            ->action(function (Order $record, Component $livewire): void {
                try {
                    $record = PurchaseOrder::confirmPurchaseOrder($record);

                    $livewire->updateForm();

                    Notification::make()
                        ->title(__('purchases::filament/admin/clusters/orders/resources/order/actions/confirm.action.notification.success.title'))
                        ->body(__('purchases::filament/admin/clusters/orders/resources/order/actions/confirm.action.notification.success.body'))
                        ->success()
                        ->send();
                } catch (Throwable $e) {
                    Notification::make()
                        ->danger()
                        ->body($e->getMessage())
                        ->send();

                    $this->halt(shouldRollBackDatabaseTransaction: true);
                }
            })
            ->visible(function () {
                $record = $this->getRecord();

                $user = Auth::user();

                if (in_array($record->state, [
                    OrderState::PURCHASE,
                    OrderState::DONE,
                    OrderState::CANCELED,
                ])) {
                    return false;
                }

                if (PurchaseOrder::canUserApprove($user)) {
                    return true;
                }

                return $record->state === OrderState::DRAFT;
            });
    }
}
