<?php

namespace Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources\OrderResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Webkul\PointOfSale\Enums\OrderState;
use Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources\OrderResource;
use Webkul\TableViews\Filament\Components\PresetView;
use Webkul\TableViews\Filament\Concerns\HasTableViews;

class ListOrders extends ListRecords
{
    use HasTableViews;

    protected static string $resource = OrderResource::class;

    public function getPresetTableViews(): array
    {
        return [
            'settled' => PresetView::make(__('point-of-sale::filament/admin/clusters/orders/resources/order/pages/list-orders.tabs.settled'))
                ->icon('heroicon-s-banknotes')
                ->favorite()
                ->setAsDefault()
                ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('state', [
                    OrderState::PAID,
                    OrderState::DONE,
                    OrderState::INVOICED,
                ])),

            'draft' => PresetView::make(__('point-of-sale::filament/admin/clusters/orders/resources/order/pages/list-orders.tabs.draft'))
                ->icon('heroicon-s-pencil-square')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('state', OrderState::DRAFT)),

            'refunds' => PresetView::make(__('point-of-sale::filament/admin/clusters/orders/resources/order/pages/list-orders.tabs.refunds'))
                ->icon('heroicon-s-arrow-uturn-left')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query) => $query->whereNotNull('refunded_order_id')),

            'failed-operations' => PresetView::make(__('point-of-sale::filament/admin/clusters/orders/resources/order/pages/list-orders.tabs.failed-operations'))
                ->icon('heroicon-s-exclamation-triangle')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('has_failed_operation', true)),
        ];
    }
}
