<?php

namespace Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationResource\Actions;

use Closure;
use Exception;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Support\Facades\FilamentView;
use Webkul\Sale\Enums\OrderState;
use Webkul\Sale\Facades\SaleOrder;
use Webkul\Sale\Filament\Clusters\Orders\Resources\OrderResource;

class ConfirmAction extends Action
{
    protected bool|Closure $hasDatabaseTransactions = true;

    public static function getDefaultName(): ?string
    {
        return 'orders.sales.confirm';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->color(fn (): string => $this->getRecord()->state === OrderState::DRAFT ? 'gray' : 'primary')
            ->label(__('sales::filament/clusters/orders/resources/quotation/actions/confirm.title'))
            ->hidden(fn ($record) => in_array($record->state, [OrderState::SALE, OrderState::CANCEL]))
            ->action(function ($record, $livewire) {
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

                $livewire->refreshFormData(['state']);

                $livewire->redirect(OrderResource::getUrl('edit', ['record' => $record]), navigate: FilamentView::hasSpaMode());

                Notification::make()
                    ->success()
                    ->title(__('sales::filament/clusters/orders/resources/quotation/actions/confirm.notification.confirmed.title'))
                    ->body(__('sales::filament/clusters/orders/resources/quotation/actions/confirm.notification.confirmed.body'))
                    ->send();
            });
    }
}
