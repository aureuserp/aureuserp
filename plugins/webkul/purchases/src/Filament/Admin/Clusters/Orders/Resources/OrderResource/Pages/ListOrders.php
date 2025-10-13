<?php

namespace Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\OrderResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Webkul\Purchase\Enums\OrderState;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\OrderResource;
use Webkul\TableViews\Filament\Components\PresetView;
use Webkul\TableViews\Filament\Concerns\HasTableViews;

class ListOrders extends ListRecords
{
    use HasTableViews;

    protected static string $resource = OrderResource::class;

    public function getPresetTableViews(): array
    {
        return [
            'my_purchases' => PresetView::make(__('purchases::filament/admin/clusters/orders/resources/order/pages/list-orders.tabs.my-purchases'))
                ->icon('heroicon-o-shopping-cart')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('user_id', Auth::id())),

            'purchase_orders' => PresetView::make(__('purchases::filament/admin/clusters/orders/resources/order/pages/list-orders.tabs.purchase-orders'))
                ->icon('heroicon-o-document-text')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('state', [
                    OrderState::PURCHASE,
                    OrderState::DONE,
                ])),

            'starred_orders' => PresetView::make(__('purchases::filament/admin/clusters/orders/resources/order/pages/list-orders.tabs.starred'))
                ->icon('heroicon-s-star')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('priority', true)),

            'orders' => PresetView::make(__('purchases::filament/admin/clusters/orders/resources/order/pages/list-orders.tabs.orders'))
                ->icon('heroicon-s-user')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('state', [OrderState::DRAFT, OrderState::SENT])),

            'draft_orders' => PresetView::make(__('purchases::filament/admin/clusters/orders/resources/order/pages/list-orders.tabs.draft-orders'))
                ->icon('heroicon-o-pencil-square')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('state', OrderState::DRAFT)),

            'waiting_orders' => PresetView::make(__('purchases::filament/admin/clusters/orders/resources/order/pages/list-orders.tabs.waiting-orders'))
                ->icon('heroicon-o-clock')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('state', OrderState::SENT)),

            'late_orders' => PresetView::make(__('purchases::filament/admin/clusters/orders/resources/order/pages/list-orders.tabs.late-orders'))
                ->icon('heroicon-o-exclamation-circle')
                ->modifyQueryUsing(function (Builder $query) {
                    return $query
                        ->whereIn('state', [OrderState::DRAFT, OrderState::SENT])
                        ->where('ordered_at', '<', now());
                }),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('purchases::filament/admin/clusters/orders/resources/order/pages/list-orders.header-actions.create.label'))
                ->icon('heroicon-o-plus-circle'),
        ];
    }
}
