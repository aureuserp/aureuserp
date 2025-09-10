<?php

namespace Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\Orders\Actions;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Livewire\Component;
use Webkul\Purchase\Enums\OrderState;
use Webkul\Purchase\Facades\PurchaseOrder;
use Webkul\Purchase\Models\Order;

class DraftAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'purchases.orders.draft';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('purchases::filament/admin/clusters/orders/resources/order/actions/draft.label'))
            ->color('gray')
            ->action(function (Order $record, Component $livewire): void {
                $record = PurchaseOrder::draftPurchaseOrder($record);

                $livewire->updateForm();

                Notification::make()
                    ->title(__('purchases::filament/admin/clusters/orders/resources/order/actions/draft.action.notification.success.title'))
                    ->body(__('purchases::filament/admin/clusters/orders/resources/order/actions/draft.action.notification.success.body'))
                    ->success()
                    ->send();
            })
            ->visible(fn () => $this->getRecord()->state == OrderState::CANCELED);
    }
}
