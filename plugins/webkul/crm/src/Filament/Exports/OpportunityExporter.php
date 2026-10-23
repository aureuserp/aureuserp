<?php

namespace Webkul\Crm\Filament\Exports;

use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Models\Export;

class OpportunityExporter extends LeadRecordExporter
{
    public static function getColumns(): array
    {
        $label = fn (string $key): string => __('crm::filament/transfers.columns.'.$key);

        $columns = static::leadColumns();

        array_splice($columns, 2, 0, [
            ExportColumn::make('partner.name')->label($label('customer')),
            ExportColumn::make('expected_revenue')->label($label('expected-revenue')),
            ExportColumn::make('expected_closing_date')
                ->label($label('expected-closing'))
                ->formatStateUsing(fn (mixed $state): ?string => $state?->toDateString()),
        ]);

        return $columns;
    }

    public function getFileName(Export $export): string
    {
        return 'opportunities-'.$export->getKey().'-'.now()->format('Y-m-d');
    }
}
