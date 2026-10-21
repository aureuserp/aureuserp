<?php

namespace Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Actions;

use Closure;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Throwable;
use Webkul\PointOfSale\Enums\SessionState;
use Webkul\PointOfSale\Facades\PointOfSale;
use Webkul\PointOfSale\Models\Order;

class InvoiceOrderAction extends Action
{
    protected bool|Closure $hasDatabaseTransactions = true;

    public static function getDefaultName(): ?string
    {
        return 'point-of-sale.order.invoice';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('point-of-sale::filament/admin/clusters/orders/actions/invoice-order.label'))
            ->icon('heroicon-o-document-text')
            ->color('info')
            ->requiresConfirmation()
            ->action(function (Order $record): void {
                try {
                    PointOfSale::invoiceOrder($record);

                    Notification::make()
                        ->success()
                        ->title(__('point-of-sale::filament/admin/clusters/orders/actions/invoice-order.notification.success.title'))
                        ->body(__('point-of-sale::filament/admin/clusters/orders/actions/invoice-order.notification.success.body'))
                        ->send();
                } catch (Throwable $exception) {
                    Notification::make()
                        ->danger()
                        ->body($exception->getMessage())
                        ->send();

                    $this->halt(shouldRollBackDatabaseTransaction: true);
                }
            })
            ->visible(fn (Order $record): bool => $record->state->isSettled()
                && ! $record->is_invoiced
                && $record->session?->state !== SessionState::CLOSED);
    }
}
