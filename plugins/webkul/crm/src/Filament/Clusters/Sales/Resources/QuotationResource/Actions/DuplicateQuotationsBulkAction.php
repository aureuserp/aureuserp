<?php

namespace Webkul\Crm\Filament\Clusters\Sales\Resources\QuotationResource\Actions;

use Filament\Actions\BulkAction;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Webkul\Crm\Filament\Clusters\Sales\Resources\QuotationResource;
use Webkul\Sale\Enums\OrderState;
use Webkul\Sale\Facades\SaleOrder;
use Webkul\Sale\Models\Order;
use Webkul\Sale\Models\OrderLine;

class DuplicateQuotationsBulkAction extends BulkAction
{
    public static function getDefaultName(): ?string
    {
        return 'duplicateQuotations';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('crm::filament/clusters/sales/resources/quotation.actions.duplicate.label'))
            ->icon('heroicon-o-document-duplicate')
            ->visible(fn (): bool => QuotationResource::canCreate())
            ->deselectRecordsAfterCompletion()
            ->action(function (Collection $records): void {
                $orders = $records->filter(fn (Order $order): bool => static::canDuplicate($order));

                $orders->each(fn (Order $order): Order => static::duplicate($order));

                Notification::make()
                    ->success()
                    ->title(__('crm::filament/clusters/sales/resources/quotation.actions.duplicate.notification', ['count' => $orders->count()]))
                    ->send();
            });
    }

    public static function canDuplicate(Order $order): bool
    {
        return ! $order->trashed() && QuotationResource::canCreate() && QuotationResource::canView($order);
    }

    public static function duplicate(Order $order): Order
    {
        return DB::transaction(function () use ($order): Order {
            $copy = $order->replicate([
                'name', 'state', 'invoice_status', 'delivery_status', 'locked', 'signed_by', 'signed_on',
                'access_token', 'procurement_group_id', 'date_order', 'creator_id',
            ]);
            $copy->state = OrderState::DRAFT;
            $copy->date_order = now();
            $copy->creator_id = Auth::id();
            $copy->save();

            $order->lines()->get()->each(function (OrderLine $line) use ($copy): void {
                $lineCopy = $line->replicate([
                    'order_id', 'state', 'invoice_status', 'qty_delivered', 'qty_invoiced', 'qty_to_invoice',
                    'untaxed_amount_invoiced', 'untaxed_amount_to_invoice', 'linked_sale_order_sale_id',
                ]);
                $lineCopy->order_id = $copy->getKey();
                $lineCopy->state = OrderState::DRAFT;
                $lineCopy->save();

                $lineCopy->taxes()->sync($line->taxes()->allRelatedIds());
            });

            return SaleOrder::computeSaleOrder($copy->refresh());
        });
    }
}
