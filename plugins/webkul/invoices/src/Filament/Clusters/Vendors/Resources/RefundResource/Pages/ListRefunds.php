<?php

namespace Webkul\Invoice\Filament\Clusters\Vendors\Resources\RefundResource\Pages;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Filament\Resources\InvoiceResource\Pages\ListInvoices as BaseListInvoices;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\RefundResource;
use Webkul\TableViews\Filament\Components\PresetView;
use Webkul\TableViews\Filament\Concerns\HasTableViews;

class ListRefunds extends BaseListInvoices
{
    use HasTableViews;

    protected static string $resource = RefundResource::class;

    public function getPresetTableViews(): array
    {
        $predefinedViews = parent::getPresetTableViews();

        return [
            'in_refund' => PresetView::make(__('Refunds'))
                ->favorite()
                ->setAsDefault()
                ->icon('heroicon-s-receipt-percent')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('move_type', MoveType::IN_REFUND)),
            ...Arr::except($predefinedViews, ['invoice', 'in_refund']),
        ];
    }
}
