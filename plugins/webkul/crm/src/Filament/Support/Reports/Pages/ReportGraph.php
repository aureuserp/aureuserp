<?php

namespace Webkul\Crm\Filament\Support\Reports\Pages;

use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Table;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Session;
use Webkul\Crm\Filament\Concerns\HasCRMClusterNavigation;
use Webkul\Crm\Filament\Concerns\HasCRMTableViews;
use Webkul\Crm\Filament\Support\Reports\Widgets\ReportChart;

abstract class ReportGraph extends ListRecords
{
    use HasCRMClusterNavigation, HasCRMTableViews;

    #[Session]
    public ?string $graphMeasure = null;

    #[Session]
    public ?string $graphSplitBy = null;

    public function mount(): void
    {
        parent::mount();

        $defaults = $this->getReport()::getDefaultFilters();

        $this->graphMeasure ??= $defaults['measure'];
        $this->graphSplitBy ??= $defaults['split_by'] ?? '';
    }

    protected function getReport(): string
    {
        return static::getResource()::getReport();
    }

    public function getTitle(): string
    {
        return static::getResource()::getReportTitle();
    }

    public function getBreadcrumb(): string
    {
        return __('crm::filament/clusters/reporting/report.views.graph');
    }

    public function getRecordNavigationTabs(): array
    {
        return static::getResource()::getViewTabs(static::getResourcePageName());
    }

    public function getPresetTableViews(): array
    {
        return static::getResource()::getReportTableViews();
    }

    public function table(Table $table): Table
    {
        $report = $this->getReport();

        return static::getResource()::table($table)
            ->filters(static::getResource()::getReportTableFilters())
            ->groups($report::getTableGroups())
            ->defaultGroup($report::getDefaultGroupBy())
            ->paginated(false)
            ->recordActions([])
            ->toolbarActions([])
            ->emptyState(fn () => view('crm::filament.reports.graph', $this->getGraph()));
    }

    public function getTableRecords(): Collection|Paginator|CursorPaginator
    {
        return new Collection;
    }

    public function getGraphFilters(): array
    {
        $report = $this->getReport();

        $defaults = $report::getDefaultFilters();

        $splitBy = $this->graphSplitBy === null ? $defaults['split_by'] : $this->graphSplitBy;

        return [
            'measure'    => array_key_exists((string) $this->graphMeasure, $report::measures()) ? $this->graphMeasure : $defaults['measure'],
            'group_by'   => $this->getTableGrouping()?->getId() ?? $defaults['group_by'],
            'split_by'   => filled($splitBy) && $report::getDimension($splitBy) ? $splitBy : null,
        ];
    }

    public function getGraph(): array
    {
        $report = $this->getReport();

        $filters = $this->getGraphFilters();

        return [
            'filters'    => $filters,
            'measures'   => $report::getMeasureOptions(),
            'dimensions' => $report::getDimensionOptions(),
            'chart'      => ReportChart::build($report, $this->getFilteredTableQuery(), $filters),
        ];
    }
}
