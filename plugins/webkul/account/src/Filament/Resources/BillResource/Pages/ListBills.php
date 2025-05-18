<?php

namespace Webkul\Account\Filament\Resources\BillResource\Pages;

use Filament\Actions;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Filament\Resources\BillResource;
use Webkul\Account\Filament\Resources\InvoiceResource\Pages\ListInvoices as BaseListBills;
use Webkul\TableViews\Filament\Components\PresetView;
use Webkul\TableViews\Filament\Concerns\HasTableViews;

class ListBills extends BaseListBills
{
    use HasTableViews;

    protected static string $resource = BillResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->icon('heroicon-o-plus-circle'),
        ];
    }

    public function getPresetTableViews(): array
    {
        return [
            'bill' => PresetView::make(__('accounts::filament/resources/bill/pages/list-bill.tabs.bills'))
                ->favorite()
                ->default()
                ->icon('heroicon-s-receipt-percent')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('move_type', MoveType::IN_INVOICE)),
            ...Arr::except(parent::getPresetTableViews(), 'invoice'),
        ];
    }
}
