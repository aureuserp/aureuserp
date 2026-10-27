<?php

namespace Webkul\Crm\Filament\Exports;

use Filament\Actions\Exports\ExportColumn;
use Webkul\Support\Models\ActivityType;

class ActivityTypeExporter extends CRMRecordExporter
{
    protected static ?string $model = ActivityType::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('name')->label(__('crm::filament/transfers.columns.name')),
            ExportColumn::make('summary')->label(__('support::filament/resources/activity-type.table.columns.summary')),
            ExportColumn::make('delay_count')->label(__('support::filament/resources/activity-type.table.columns.planned-in')),
            ExportColumn::make('is_active')->label(__('support::filament/resources/activity-type.table.columns.status')),
            ExportColumn::make('created_at')->label(__('support::filament/resources/activity-type.table.columns.created-at')),
        ];
    }

    protected static function getFileNamePrefix(): string
    {
        return 'activity-types';
    }
}
