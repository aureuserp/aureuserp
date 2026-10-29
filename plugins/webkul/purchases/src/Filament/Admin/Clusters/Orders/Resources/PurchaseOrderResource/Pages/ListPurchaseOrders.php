<?php

namespace Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\PurchaseOrderResource\Pages;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Webkul\Purchase\Enums\OrderState;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\OrderResource\Pages\ListOrders;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\PurchaseOrderResource;
use Webkul\TableViews\Filament\Components\PresetView;

class ListPurchaseOrders extends ListOrders
{
    protected static string $resource = PurchaseOrderResource::class;

    public function getPresetTableViews(): array
    {
        return [
            'my_orders' => PresetView::make(__('purchases::filament/admin/clusters/orders/resources/purchase-order/pages/list-purchase-orders.tabs.my-orders'))
                ->icon('heroicon-o-shopping-cart')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('user_id', Auth::id())),

            'starred_orders' => PresetView::make(__('purchases::filament/admin/clusters/orders/resources/purchase-order/pages/list-purchase-orders.tabs.starred'))
                ->icon('heroicon-s-star')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('priority', true)),

            'waiting-bills' => PresetView::make(__('purchases::filament/admin/clusters/orders/resources/purchase-order/pages/list-purchase-orders.tabs.waiting-bills'))
                ->icon('heroicon-o-clock')
                ->favorite()
                ->modifyQueryUsing(function (Builder $query) {
                    return $query
                        ->where('state', OrderState::SENT)
                        ->where('ordered_at', '<', now());
                }),

            'received-bills' => PresetView::make(__('purchases::filament/admin/clusters/orders/resources/purchase-order/pages/list-purchase-orders.tabs.received-bills'))
                ->icon('heroicon-o-document-check')
                ->favorite()
                ->modifyQueryUsing(function (Builder $query) {
                    return $query
                        ->whereIn('state', [OrderState::PURCHASE, OrderState::DONE])
                        ->where('invoice_status', 'invoiced');
                }),
        ];
    }
}
