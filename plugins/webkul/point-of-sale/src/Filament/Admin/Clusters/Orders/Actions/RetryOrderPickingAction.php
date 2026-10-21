<?php

namespace Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Actions;

use Closure;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Throwable;
use Webkul\PointOfSale\Facades\PointOfSale;
use Webkul\PointOfSale\Models\Order;

class RetryOrderPickingAction extends Action
{
    protected bool|Closure $hasDatabaseTransactions = true;

    public static function getDefaultName(): ?string
    {
        return 'point-of-sale.order.retry-picking';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('point-of-sale::filament/admin/clusters/orders/actions/retry-order-picking.label'))
            ->icon('heroicon-o-arrow-path')
            ->color('warning')
            ->requiresConfirmation()
            ->action(function (Order $record): void {
                try {
                    PointOfSale::retryOrderPicking($record);

                    Notification::make()
                        ->success()
                        ->title(__('point-of-sale::filament/admin/clusters/orders/actions/retry-order-picking.notification.success.title'))
                        ->body(__('point-of-sale::filament/admin/clusters/orders/actions/retry-order-picking.notification.success.body'))
                        ->send();
                } catch (Throwable $exception) {
                    Notification::make()
                        ->danger()
                        ->body($exception->getMessage())
                        ->send();

                    $this->halt(shouldRollBackDatabaseTransaction: true);
                }
            })
            ->visible(fn (Order $record): bool => $record->has_failed_operation);
    }
}
