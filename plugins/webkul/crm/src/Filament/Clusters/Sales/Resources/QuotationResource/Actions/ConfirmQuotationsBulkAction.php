<?php

namespace Webkul\Crm\Filament\Clusters\Sales\Resources\QuotationResource\Actions;

use Filament\Actions\BulkAction;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Throwable;
use Webkul\Crm\Filament\Clusters\Sales\Resources\QuotationResource;
use Webkul\Sale\Enums\OrderState;
use Webkul\Sale\Facades\SaleOrder;
use Webkul\Sale\Models\Order;

class ConfirmQuotationsBulkAction extends BulkAction
{
    public static function getDefaultName(): ?string
    {
        return 'confirmQuotations';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('crm::filament/clusters/sales/resources/quotation.actions.confirm.label'))
            ->icon('heroicon-o-check-badge')
            ->requiresConfirmation()
            ->modalDescription(__('crm::filament/clusters/sales/resources/quotation.actions.confirm.description'))
            ->deselectRecordsAfterCompletion()
            ->action(function (Collection $records): void {
                $confirmed = $records
                    ->filter(fn (Order $order): bool => static::canConfirm($order))
                    ->filter(fn (Order $order): bool => static::confirm($order))
                    ->count();

                Notification::make()
                    ->success()
                    ->title(__('crm::filament/clusters/sales/resources/quotation.actions.confirm.notification', ['confirmed' => $confirmed, 'skipped' => $records->count() - $confirmed]))
                    ->send();
            });
    }

    public static function canConfirm(Order $order): bool
    {
        return ! $order->trashed()
            && in_array($order->state, [OrderState::DRAFT, OrderState::SENT], true)
            && QuotationResource::canEdit($order);
    }

    public static function confirm(Order $order): bool
    {
        try {
            DB::transaction(fn (): Order => SaleOrder::confirmSaleOrder($order));
        } catch (Throwable $exception) {
            report($exception);

            return false;
        }

        return true;
    }
}
