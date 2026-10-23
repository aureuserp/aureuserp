<?php

namespace Webkul\Crm\Filament\Exports;

use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Models\Export;
use Webkul\Crm\Models\Lead;

class ForecastExporter extends OpportunityExporter
{
    public static function getColumns(): array
    {
        $columns = parent::getColumns();

        $position = collect($columns)->search(fn (ExportColumn $column): bool => $column->getName() === 'expected_revenue') + 1;

        array_splice($columns, $position, 0, [
            ExportColumn::make('prorated_revenue')
                ->label(__('crm::filament/transfers.columns.prorated-revenue'))
                ->state(fn (Lead $record): float => round((float) $record->expected_revenue * (float) $record->probability / 100, 2)),
            ExportColumn::make('forecast_status')
                ->label(__('crm::filament/transfers.columns.status'))
                ->state(fn (Lead $record): string => __('crm::filament/clusters/reporting/report.presets.'.match (true) {
                    $record->trashed() => 'lost',
                    $record->isWon()   => 'won',
                    default            => 'ongoing',
                })),
        ]);

        return $columns;
    }

    public function getFileName(Export $export): string
    {
        return 'forecast-'.$export->getKey().'-'.now()->format('Y-m-d');
    }
}
