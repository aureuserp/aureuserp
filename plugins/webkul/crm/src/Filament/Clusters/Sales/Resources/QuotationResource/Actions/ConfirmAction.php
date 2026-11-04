<?php

namespace Webkul\Crm\Filament\Clusters\Sales\Resources\QuotationResource\Actions;

use Exception;
use Filament\Notifications\Notification;
use Filament\Support\Facades\FilamentView;
use Webkul\Sale\Facades\SaleOrder;
use Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationResource\Actions\ConfirmAction as BaseConfirmAction;

class ConfirmAction extends BaseConfirmAction
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->action(function ($record, $livewire): void {
            try {
                $record = SaleOrder::confirmSaleOrder($record);
            } catch (Exception $e) {
                Notification::make()
                    ->danger()
                    ->title(__('sales::filament/clusters/orders/resources/quotation/actions/confirm.notification.error.title'))
                    ->body($e->getMessage())
                    ->send();

                $this->halt(shouldRollBackDatabaseTransaction: true);

                return;
            }

            $livewire->redirect($livewire::getResource()::getUrl('edit', ['record' => $record]), navigate: FilamentView::hasSpaMode());

            Notification::make()
                ->success()
                ->title(__('sales::filament/clusters/orders/resources/quotation/actions/confirm.notification.confirmed.title'))
                ->body(__('sales::filament/clusters/orders/resources/quotation/actions/confirm.notification.confirmed.body'))
                ->send();
        });
    }
}
