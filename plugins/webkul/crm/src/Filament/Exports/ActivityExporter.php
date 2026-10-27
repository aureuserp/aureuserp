<?php

namespace Webkul\Crm\Filament\Exports;

use Filament\Actions\Exports\ExportColumn;
use Webkul\Crm\Models\Activity;

class ActivityExporter extends CRMRecordExporter
{
    protected static ?string $model = Activity::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('date_deadline')->label(__('crm::filament/clusters/sales/resources/activity.table.columns.due-date')),
            ExportColumn::make('lead.name')->label(__('crm::filament/clusters/sales/resources/activity.table.columns.opportunity')),
            ExportColumn::make('lead.partner.name')->label(__('crm::filament/clusters/sales/resources/activity.table.columns.customer')),
            ExportColumn::make('activityType.name')->label(__('crm::filament/clusters/sales/resources/activity.table.columns.activity-type')),
            ExportColumn::make('summary')->label(__('crm::filament/clusters/sales/resources/activity.table.columns.summary')),
            ExportColumn::make('assignedTo.name')->label(__('crm::filament/clusters/sales/resources/activity.table.columns.assigned-to')),
            ExportColumn::make('lead.stage.name')->label(__('crm::filament/clusters/sales/resources/activity.table.columns.stage')),
            ExportColumn::make('created_at')->label(__('crm::filament/clusters/sales/resources/activity.table.columns.created-at')),
        ];
    }

    protected static function getFileNamePrefix(): string
    {
        return 'activities';
    }
}
