<?php

namespace Webkul\Account\Filament\Resources\CreditNoteResource\Pages;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Filament\Resources\CreditNoteResource;
use Webkul\Account\Filament\Resources\InvoiceResource\Pages\ListInvoices as ListRecords;
use Webkul\TableViews\Filament\Components\PresetView;
use Webkul\TableViews\Filament\Concerns\HasTableViews;

class ListCreditNotes extends ListRecords
{
    use HasTableViews;

    protected static string $resource = CreditNoteResource::class;

    public function getPresetTableViews(): array
    {
        $predefinedViews = parent::getPresetTableViews();

        return [
            'out_refund' => PresetView::make(__('accounts::filament/resources/credit-note/pages/list-credit-note.tabs.credit-notes'))
                ->favorite()
                ->default()
                ->icon('heroicon-s-receipt-percent')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('move_type', MoveType::OUT_REFUND)),
            ...Arr::except($predefinedViews, ['invoice']),
        ];
    }
}
