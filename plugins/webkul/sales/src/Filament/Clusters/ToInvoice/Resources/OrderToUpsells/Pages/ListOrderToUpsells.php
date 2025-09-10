<?php

namespace Webkul\Sale\Filament\Clusters\ToInvoice\Resources\OrderToUpsells\Pages;

use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Webkul\Sale\Filament\Clusters\ToInvoice\Resources\OrderToUpsells\OrderToUpsellResource;
use Webkul\TableViews\Filament\Components\PresetView;
use Webkul\TableViews\Filament\Concerns\HasTableViews;

class ListOrderToUpsells extends ListRecords
{
    use HasTableViews;

    protected static string $resource = OrderToUpsellResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function getPresetTableViews(): array
    {
        return [
            'my_orders' => PresetView::make(__('sales::filament/clusters/to-invoice/resources/order-to-upsell/pages/list-order-to-upsell.tabs.my-orders'))
                ->icon('heroicon-s-shopping-bag')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('user_id', Auth::id())),
            'archived' => PresetView::make(__('sales::filament/clusters/to-invoice/resources/order-to-upsell/pages/list-order-to-upsell.tabs.archived'))
                ->icon('heroicon-s-archive-box')
                ->favorite()
                ->modifyQueryUsing(fn ($query) => $query->onlyTrashed()),
        ];
    }
}
