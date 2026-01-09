<?php

namespace Webkul\Accounting\Filament\Clusters\Vendors\Resources\PaymentResource\Pages;

use Illuminate\Database\Eloquent\Builder;
use Webkul\Account\Filament\Resources\PaymentResource\Pages\ListPayments as BaseListPayments;
use Webkul\Accounting\Filament\Clusters\Vendors\Resources\PaymentResource;
use Webkul\TableViews\Filament\Components\PresetView;

class ListPayments extends BaseListPayments
{
    protected static string $resource = PaymentResource::class;

    public function getPresetTableViews(): array
    {
        $presets = parent::getPresetTableViews();

        return [
            ...$presets,
            'vendor_payments' => PresetView::make(__('accounting::filament/clusters/vendors/resources/payment/pages/list-payments.tabs.vendor-payments'))
                ->favorite()
                ->setAsDefault()
                ->icon('heroicon-s-banknotes')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('partner_type', 'supplier')),
        ];
    }
}
