<?php

namespace Webkul\Crm\Filament\Exports;

use Filament\Actions\Exports\ExportColumn;
use Webkul\Support\Models\ActivityPlan;

class ActivityPlanExporter extends CRMRecordExporter
{
    protected static ?string $model = ActivityPlan::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('name')->label(__('crm::filament/clusters/configurations/resources/activity-plan.table.columns.name')),
            ExportColumn::make('company.name')->label(__('crm::filament/clusters/configurations/resources/activity-plan.table.columns.company')),
            ExportColumn::make('is_active')->label(__('crm::filament/clusters/configurations/resources/activity-plan.table.columns.status')),
            ExportColumn::make('creator.name')->label(__('crm::filament/clusters/configurations/resources/activity-plan.table.columns.created-by')),
            ExportColumn::make('created_at')->label(__('crm::filament/clusters/configurations/resources/activity-plan.table.columns.created-at')),
        ];
    }

    protected static function getFileNamePrefix(): string
    {
        return 'activity-plans';
    }
}
