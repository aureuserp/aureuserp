<?php

namespace Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationResource\Actions;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Support\Facades\FilamentView;
use Webkul\Sale\Enums\OrderState;
use Webkul\Sale\Facades\SaleOrder;
use Webkul\Sale\Filament\Clusters\Orders\Resources\OrdersResource;

class ConfirmAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'orders.sales.confirm';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->color('primary')
            ->label(__('sales::filament/clusters/orders/resources/quotation/actions/confirm.title'))
            ->hidden(fn ($record) => $record->state != OrderState::DRAFT)
            ->action(function ($record, $livewire) {
                $record = SaleOrder::confirmSaleOrder($record);

                $livewire->refreshFormData(['state']);

                $livewire->redirect(OrdersResource::getUrl('edit', ['record' => $record]), navigate: FilamentView::hasSpaMode());

                Notification::make()
                    ->success()
                    ->title(__('sales::filament/clusters/orders/resources/quotation/actions/confirm.notification.confirmed.title'))
                    ->body(__('sales::filament/clusters/orders/resources/quotation/actions/confirm.notification.confirmed.body'))
                    ->send();
            });
    }
}
