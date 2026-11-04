<?php

namespace Webkul\Crm\Filament\Clusters\Sales\Resources\QuotationResource\Actions;

use Filament\Actions\BulkAction;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Collection;
use Webkul\Crm\Filament\Clusters\Sales\Resources\QuotationResource;
use Webkul\Sale\Enums\OrderState;
use Webkul\Sale\Models\Order;

class MarkQuotationsAsSentBulkAction extends BulkAction
{
    public static function getDefaultName(): ?string
    {
        return 'markQuotationsAsSent';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('crm::filament/clusters/sales/resources/quotation.actions.mark-as-sent.label'))
            ->icon('heroicon-o-paper-airplane')
            ->deselectRecordsAfterCompletion()
            ->action(function (Collection $records): void {
                $orders = $records->filter(fn (Order $order): bool => static::canMarkAsSent($order));

                $orders->each(fn (Order $order): bool => static::markAsSent($order));

                Notification::make()
                    ->success()
                    ->title(__('crm::filament/clusters/sales/resources/quotation.actions.mark-as-sent.notification', ['count' => $orders->count()]))
                    ->send();
            });
    }

    public static function canMarkAsSent(Order $order): bool
    {
        return ! $order->trashed() && $order->state === OrderState::DRAFT && QuotationResource::canEdit($order);
    }

    public static function markAsSent(Order $order): bool
    {
        $order->state = OrderState::SENT;

        return $order->save();
    }
}
