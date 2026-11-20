<?php

namespace Webkul\Crm\Filament\Exports;

use Filament\Actions\Exports\ExportColumn;
use Webkul\Partner\Models\Partner;

class CustomerExporter extends CRMRecordExporter
{
    protected static ?string $model = Partner::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('name')->label(__('crm::filament/transfers.columns.name')),
            ExportColumn::make('parent.name')->label(__('crm::filament/transfers.columns.company-name')),
            ExportColumn::make('job_title')->label(__('crm::filament/transfers.columns.job-title')),
            ExportColumn::make('email')->label(__('crm::filament/transfers.columns.email')),
            ExportColumn::make('phone')->label(__('crm::filament/transfers.columns.phone')),
            ExportColumn::make('mobile')->label(__('crm::filament/transfers.columns.mobile')),
            ExportColumn::make('city')->label(__('crm::filament/transfers.columns.city')),
            ExportColumn::make('country.name')->label(__('crm::filament/transfers.columns.country')),
            ExportColumn::make('tags.name')->label(__('crm::filament/transfers.columns.tags')),
        ];
    }

    protected static function getFileNamePrefix(): string
    {
        return 'contacts';
    }
}
