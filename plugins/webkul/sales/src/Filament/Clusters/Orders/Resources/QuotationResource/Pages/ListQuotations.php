<?php

namespace Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Webkul\Sale\Enums\OrderState;
use Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationResource;
use Webkul\TableViews\Filament\Components\PresetView;
use Webkul\TableViews\Filament\Concerns\HasTableViews;

class ListQuotations extends ListRecords
{
    use HasTableViews;

    protected static string $resource = QuotationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->icon('heroicon-o-plus-circle'),
        ];
    }

    public function getPresetTableViews(): array
    {
        return [
            'my_quotations' => PresetView::make(__('sales::filament/clusters/orders/resources/quotation/pages/list-quotation.tabs.my-quotations'))
                ->icon('heroicon-s-user')
                ->favorite()
                ->setAsDefault()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('user_id', Auth::id())),
            'quotations' => PresetView::make(__('sales::filament/clusters/orders/resources/quotation/pages/list-quotation.tabs.quotations'))
                ->icon('heroicon-s-receipt-percent')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('state', [OrderState::DRAFT, OrderState::SENT])),
            'sale_orders' => PresetView::make(__('sales::filament/clusters/orders/resources/quotation/pages/list-quotation.tabs.sales-orders'))
                ->icon('heroicon-s-shopping-bag')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('state', OrderState::SALE)),
            'archived' => PresetView::make(__('sales::filament/clusters/orders/resources/quotation/pages/list-quotation.tabs.archived'))
                ->icon('heroicon-s-archive-box')
                ->favorite()
                ->modifyQueryUsing(fn ($query) => $query->onlyTrashed()),
        ];
    }
}
