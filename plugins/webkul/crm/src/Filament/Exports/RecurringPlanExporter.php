<?php

namespace Webkul\Crm\Filament\Exports;

use Filament\Actions\Exports\ExportColumn;
use Webkul\Crm\Models\RecurringPlan;

class RecurringPlanExporter extends CRMRecordExporter
{
    protected static ?string $model = RecurringPlan::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('name')->label(__('crm::filament/clusters/configurations/resources/recurring-plan.table.columns.name')),
            ExportColumn::make('number_of_months')->label(__('crm::filament/clusters/configurations/resources/recurring-plan.table.columns.months')),
        ];
    }

    protected static function getFileNamePrefix(): string
    {
        return 'recurring-plans';
    }
}
