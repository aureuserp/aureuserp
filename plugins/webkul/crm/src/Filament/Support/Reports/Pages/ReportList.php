<?php

namespace Webkul\Crm\Filament\Support\Reports\Pages;

use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Table;
use Webkul\Crm\Filament\Concerns\HasCRMClusterNavigation;
use Webkul\Crm\Filament\Concerns\HasCRMTableViews;

abstract class ReportList extends ListRecords
{
    use HasCRMClusterNavigation, HasCRMTableViews;

    public function getPresetTableViews(): array
    {
        return static::getResource()::getReportTableViews();
    }

    public function getTitle(): string
    {
        return static::getResource()::getReportTitle();
    }

    public function getBreadcrumb(): string
    {
        return __('crm::filament/clusters/reporting/report.views.list');
    }

    public function getRecordNavigationTabs(): array
    {
        return static::getResource()::getViewTabs(static::getResourcePageName());
    }

    public function table(Table $table): Table
    {
        $report = static::getResource()::getReport();

        return static::getResource()::table($table)
            ->filters(static::getResource()::getReportTableFilters())
            ->recordActions([])
            ->toolbarActions(static::getResource()::getReportBulkActions());
    }
}
