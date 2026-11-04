<?php

namespace Webkul\Crm\Filament\Exports;

use Filament\Actions\Exports\ExportColumn;
use Webkul\Sale\Models\Team;

class TeamExporter extends CRMRecordExporter
{
    protected static ?string $model = Team::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('name')->label(__('crm::filament/clusters/sales/resources/team.table.columns.name')),
            ExportColumn::make('members.name')->label(__('crm::filament/clusters/sales/resources/team.table.columns.members')),
            ExportColumn::make('company.name')->label(__('crm::filament/clusters/sales/resources/team.table.columns.company')),
        ];
    }

    protected static function getFileNamePrefix(): string
    {
        return 'sales-teams';
    }
}
