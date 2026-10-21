<?php

namespace Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources\PaymentResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources\PaymentResource;
use Webkul\TableViews\Filament\Components\PresetView;
use Webkul\TableViews\Filament\Concerns\HasTableViews;

class ListPayments extends ListRecords
{
    use HasTableViews;

    protected static string $resource = PaymentResource::class;

    public function getPresetTableViews(): array
    {
        return [
            'all' => PresetView::make(__('point-of-sale::filament/admin/clusters/orders/resources/payment/pages/list-payments.tabs.all'))
                ->icon('heroicon-s-banknotes')
                ->favorite()
                ->setAsDefault(),

            'today' => PresetView::make(__('point-of-sale::filament/admin/clusters/orders/resources/payment/pages/list-payments.tabs.today'))
                ->icon('heroicon-s-calendar-days')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query) => $query->whereDate('paid_at', today())),

            'change' => PresetView::make(__('point-of-sale::filament/admin/clusters/orders/resources/payment/pages/list-payments.tabs.change'))
                ->icon('heroicon-s-arrow-uturn-left')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_change', true)),
        ];
    }
}
