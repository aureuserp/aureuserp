<?php

namespace Webkul\Crm\Filament\Exports;

use Filament\Actions\Exports\ExportColumn;
use Webkul\Crm\Models\LostReason;

class LostReasonExporter extends CRMRecordExporter
{
    protected static ?string $model = LostReason::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('name')->label(__('crm::filament/clusters/configurations/resources/lost-reason.table.columns.name')),
            ExportColumn::make('creator.name')->label(__('crm::filament/clusters/configurations/resources/lost-reason.table.columns.created-by')),
            ExportColumn::make('created_at')->label(__('crm::filament/clusters/configurations/resources/lost-reason.table.columns.created-at')),
            ExportColumn::make('updated_at')->label(__('crm::filament/clusters/configurations/resources/lost-reason.table.columns.updated-at')),
        ];
    }

    protected static function getFileNamePrefix(): string
    {
        return 'lost-reasons';
    }
}
