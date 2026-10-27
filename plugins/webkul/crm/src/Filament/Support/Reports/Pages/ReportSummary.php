<?php

namespace Webkul\Crm\Filament\Support\Reports\Pages;

use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Table;
use Webkul\Crm\Filament\Concerns\HasCRMClusterNavigation;

abstract class ReportSummary extends ListRecords
{
    use HasCRMClusterNavigation;

    public function mount(): void
    {
        parent::mount();

        $this->rebuildTable();
    }

    public function updatedTableGrouping(): void
    {
        parent::updatedTableGrouping();

        $this->rebuildTable();
    }

    protected function rebuildTable(): void
    {
        $this->table = $this->table($this->makeTable());
    }

    public function getTitle(): string
    {
        return static::getResource()::getReportTitle();
    }

    public function getBreadcrumb(): string
    {
        return __('crm::filament/clusters/reporting/report.views.summary');
    }

    public function getRecordNavigationTabs(): array
    {
        return static::getResource()::getViewTabs(static::getResourcePageName());
    }

    public function table(Table $table): Table
    {
        $report = static::getResource()::getReport();

        return $table
            ->columns($report::getSummaryColumns(str((string) $this->tableGrouping)->before(':')->toString() ?: null))
            ->groups($report::getTableGroups())
            ->defaultGroup($report::getDefaultGroupBy())
            ->groupsOnly()
            ->extraAttributes(['class' => 'crm-report-summary'])
            ->filters(static::getResource()::getReportTableFilters())
            ->recordActions([])
            ->toolbarActions([])
            ->emptyStateHeading(__('crm::filament/clusters/reporting/report.empty-state.heading'))
            ->emptyStateDescription(__('crm::filament/clusters/reporting/report.empty-state.description'))
            ->emptyStateIcon('heroicon-o-chart-bar');
    }
}
