<?php

namespace Webkul\Invoice\Filament\Clusters\Vendors\Resources\PaymentsResource\Pages;

use Illuminate\Database\Eloquent\Builder;
use Webkul\Account\Filament\Resources\PaymentsResource\Pages\ListPayments as BaseListPayments;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\PaymentsResource;
use Webkul\TableViews\Filament\Components\PresetView;

class ListPayments extends BaseListPayments
{
    protected static string $resource = PaymentsResource::class;

    public function getPresetTableViews(): array
    {
        $presets = parent::getPresetTableViews();

        return [
            ...$presets,
            'vendor_payments' => PresetView::make(__('invoices::filament/clusters/vendors/resources/payments/pages/list-payment.tabs.vendor-payments'))
                ->favorite()
                ->setAsDefault()
                ->icon('heroicon-s-banknotes')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('partner_type', 'supplier')),
        ];
    }
}
