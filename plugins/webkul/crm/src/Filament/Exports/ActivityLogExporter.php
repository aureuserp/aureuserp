<?php

namespace Webkul\Crm\Filament\Exports;

use Filament\Actions\Exports\ExportColumn;
use Webkul\Crm\Models\ActivityLog;

class ActivityLogExporter extends CRMRecordExporter
{
    protected static ?string $model = ActivityLog::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('done_at')->label(__('crm::filament/clusters/reporting/report.table.columns.done-at')),
            ExportColumn::make('activityType.name')->label(__('crm::filament/clusters/reporting/report.table.columns.activity-type')),
            ExportColumn::make('summary')->label(__('crm::filament/clusters/reporting/report.table.columns.summary')),
            ExportColumn::make('lead.name')->label(__('crm::filament/clusters/reporting/report.table.columns.lead')),
            ExportColumn::make('lead_type')->label(__('crm::filament/clusters/reporting/report.table.columns.type'))->formatStateUsing(fn (mixed $state): string => static::enumLabel($state)),
            ExportColumn::make('assignedTo.name')->label(__('crm::filament/clusters/reporting/report.table.columns.assigned-to')),
            ExportColumn::make('doneBy.name')->label(__('crm::filament/clusters/reporting/report.table.columns.done-by')),
            ExportColumn::make('user.name')->label(__('crm::filament/clusters/reporting/report.table.columns.salesperson')),
            ExportColumn::make('team.name')->label(__('crm::filament/clusters/reporting/report.table.columns.team')),
            ExportColumn::make('stage.name')->label(__('crm::filament/clusters/reporting/report.table.columns.stage')),
            ExportColumn::make('date_deadline')->label(__('crm::filament/clusters/reporting/report.table.columns.due-date')),
        ];
    }

    protected static function getFileNamePrefix(): string
    {
        return 'activity-analysis';
    }
}
