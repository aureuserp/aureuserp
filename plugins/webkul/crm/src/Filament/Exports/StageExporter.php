<?php

namespace Webkul\Crm\Filament\Exports;

use Filament\Actions\Exports\ExportColumn;
use Webkul\Crm\Models\Stage;

class StageExporter extends CRMRecordExporter
{
    protected static ?string $model = Stage::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('name')->label(__('crm::filament/clusters/configurations/resources/stage.table.columns.name')),
            ExportColumn::make('teams.name')->label(__('crm::filament/clusters/configurations/resources/stage.table.columns.teams')),
            ExportColumn::make('is_won')->label(__('crm::filament/clusters/configurations/resources/stage.table.columns.is-won')),
            ExportColumn::make('requirements')->label(__('crm::filament/clusters/configurations/resources/stage.table.columns.requirements')),
            ExportColumn::make('created_at')->label(__('crm::filament/clusters/configurations/resources/stage.table.columns.created-at')),
        ];
    }

    protected static function getFileNamePrefix(): string
    {
        return 'stages';
    }
}
