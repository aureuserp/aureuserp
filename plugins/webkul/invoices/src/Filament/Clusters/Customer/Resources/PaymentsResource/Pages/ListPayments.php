<?php

namespace Webkul\Invoice\Filament\Clusters\Customer\Resources\PaymentsResource\Pages;

use Illuminate\Database\Eloquent\Builder;
use Webkul\Account\Filament\Resources\PaymentsResource\Pages\ListPayments as BaseListPayments;
use Webkul\Invoice\Filament\Clusters\Customer\Resources\PaymentsResource;
use Webkul\TableViews\Filament\Components\PresetView;

class ListPayments extends BaseListPayments
{
    protected static string $resource = PaymentsResource::class;

    public function getPresetTableViews(): array
    {
        $presets = parent::getPresetTableViews();

        return [
            ...$presets,
            'customer_payments' => PresetView::make(__('Customer Payments'))
                ->favorite()
                ->setAsDefault()
                ->icon('heroicon-s-banknotes')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('partner_type', ['customer', 'company'])),
        ];
    }
}
