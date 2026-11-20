<?php

namespace Webkul\Crm\Filament\Exports;

use Filament\Actions\Exports\ExportColumn;
use Webkul\Crm\Models\PartnerLevel;

class PartnerLevelExporter extends CRMRecordExporter
{
    protected static ?string $model = PartnerLevel::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('name')->label(__('crm::filament/clusters/configurations/resources/partner-level.table.columns.name')),
            ExportColumn::make('partner_weight')->label(__('crm::filament/clusters/configurations/resources/partner-level.table.columns.weight')),
        ];
    }

    protected static function getFileNamePrefix(): string
    {
        return 'partner-levels';
    }
}
