<?php

namespace Webkul\Crm\Filament\Support\Reports\Pages;

use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Table;
use Webkul\Crm\Filament\Concerns\HasCrmClusterNavigation;

abstract class ReportList extends ListRecords
{
    use HasCrmClusterNavigation;

    public function getTitle(): string
    {
        return static::getResource()::getReportTitle();
    }

    public function getBreadcrumb(): string
    {
        return __('crm::filament/clusters/reporting/report.views.list');
    }

    protected function getHeaderActions(): array
    {
        return [
            ...static::getResource()::getViewSwitcherActions(static::getResourcePageName()),
            ...static::getResource()::getExportActions(),
        ];
    }

    public function table(Table $table): Table
    {
        $report = static::getResource()::getReport();

        return static::getResource()::table($table)
            ->filters($report::getTableFilters())
            ->recordActions([])
            ->toolbarActions([]);
    }
}
