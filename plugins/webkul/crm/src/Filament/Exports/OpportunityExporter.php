<?php

namespace Webkul\Crm\Filament\Exports;

use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Models\Export;

class OpportunityExporter extends LeadRecordExporter
{
    public static function getColumns(): array
    {
        $columns = static::leadColumns();

        array_splice($columns, 2, 0, [
            ExportColumn::make('partner.name')->label(__('crm::filament/transfers.columns.customer')),
            ExportColumn::make('expected_revenue')->label(__('crm::filament/transfers.columns.expected-revenue')),
            ExportColumn::make('expected_closing_date')
                ->label(__('crm::filament/transfers.columns.expected-closing'))
                ->formatStateUsing(fn (mixed $state): ?string => $state?->toDateString()),
        ]);

        return $columns;
    }

    public function getFileName(Export $export): string
    {
        return 'opportunities-'.$export->getKey().'-'.now()->format('Y-m-d');
    }
}
