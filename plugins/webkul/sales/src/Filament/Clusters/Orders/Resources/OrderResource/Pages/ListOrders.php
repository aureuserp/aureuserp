<?php

namespace Webkul\Sale\Filament\Clusters\Orders\Resources\OrderResource\Pages;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Webkul\Sale\Enums\InvoiceStatus;
use Webkul\Sale\Filament\Clusters\Orders\Resources\OrderResource;
use Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationResource\Pages\ListQuotations as BaseListOrders;
use Webkul\TableViews\Filament\Components\PresetView;
use Webkul\TableViews\Filament\Concerns\HasTableViews;

class ListOrders extends BaseListOrders
{
    use HasTableViews;

    protected static string $resource = OrderResource::class;

    public function getPresetTableViews(): array
    {
        return [
            'my_orders' => PresetView::make(__('sales::filament/clusters/orders/resources/order/pages/list-orders.tabs.my-orders'))
                ->icon('heroicon-s-shopping-bag')
                ->favorite()
                ->default()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('user_id', Auth::id())),
            'to_invoice' => PresetView::make(__('sales::filament/clusters/orders/resources/order/pages/list-orders.tabs.to-invoice'))
                ->icon('heroicon-s-receipt-percent')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('invoice_status', InvoiceStatus::TO_INVOICE)),
            'up_selling' => PresetView::make(__('sales::filament/clusters/orders/resources/order/pages/list-orders.tabs.up-selling'))
                ->icon('heroicon-s-receipt-refund')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('invoice_status', InvoiceStatus::UP_SELLING)),
            'archived' => PresetView::make(__('sales::filament/clusters/orders/resources/order/pages/list-orders.tabs.archived'))
                ->icon('heroicon-s-archive-box')
                ->favorite()
                ->modifyQueryUsing(fn ($query) => $query->onlyTrashed()),
        ];
    }
}
