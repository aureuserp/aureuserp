<?php

namespace Webkul\Crm\Filament\Support\Reports\Pages;

use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Table;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Session;
use Webkul\Crm\Enums\ViewLayout;
use Webkul\Crm\Filament\Concerns\HasCRMClusterNavigation;
use Webkul\Crm\Filament\Concerns\HasCRMTableViews;
use Webkul\Crm\Filament\Concerns\HasViewLayouts;
use Webkul\Crm\Filament\Support\Reports\Widgets\ReportChart;

abstract class ReportPage extends ListRecords
{
    use HasCRMClusterNavigation, HasCRMTableViews, HasViewLayouts;

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

        if ($this->isViewLayout(ViewLayout::SUMMARY)) {
            $this->table = $this->table($this->makeTable());
        }
    }

    public function updatedTableGrouping(): void
    {
        parent::updatedTableGrouping();

        if ($this->isViewLayout(ViewLayout::SUMMARY)) {
            $this->table = $this->table($this->makeTable());
        }
    }

    public function getViewLayouts(): array
    {
        return static::getResource()::getReportLayouts();
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
        return $this->getViewLayout()->getLabel();
    }

    public function getPresetTableViews(): array
    {
        return static::getResource()::getReportTableViews();
    }

    public function table(Table $table): Table
    {
        $resource = static::getResource();
        $report = $this->getReport();

        return match ($this->getViewLayout()) {
            ViewLayout::SUMMARY => $table
                ->columns($report::getSummaryColumns(str((string) $this->tableGrouping)->before(':')->toString() ?: null))
                ->groups($report::getTableGroups())
                ->defaultGroup($report::getDefaultGroupBy())
                ->groupsOnly()
                ->extraAttributes(['class' => 'crm-report-summary'])
                ->filters($resource::getReportTableFilters())
                ->recordActions([])
                ->toolbarActions([])
                ->emptyStateHeading(__('crm::filament/clusters/reporting/report.empty-state.heading'))
                ->emptyStateDescription(__('crm::filament/clusters/reporting/report.empty-state.description'))
                ->emptyStateIcon('heroicon-o-chart-bar'),
            ViewLayout::GRAPH => $resource::table($table)
                ->filters($resource::getReportTableFilters())
                ->groups($report::getTableGroups())
                ->defaultGroup($report::getDefaultGroupBy())
                ->paginated(false)
                ->recordActions([])
                ->toolbarActions([])
                ->emptyState(fn () => view('crm::filament.reports.graph', $this->getGraph())),
            default => $resource::table($table)
                ->filters($resource::getReportTableFilters())
                ->recordActions([])
                ->toolbarActions($resource::getReportBulkActions()),
        };
    }

    public function getTableRecords(): Collection|Paginator|CursorPaginator
    {
        return $this->isViewLayout(ViewLayout::GRAPH) ? new Collection : parent::getTableRecords();
    }

    public function getGraphFilters(): array
    {
        $report = $this->getReport();

        $defaults = $report::getDefaultFilters();

        $splitBy = $this->graphSplitBy === null ? $defaults['split_by'] : $this->graphSplitBy;

        return [
            'measure'  => array_key_exists((string) $this->graphMeasure, $report::measures()) ? $this->graphMeasure : $defaults['measure'],
            'group_by' => $this->getTableGrouping()?->getId() ?? $defaults['group_by'],
            'split_by' => filled($splitBy) && $report::getDimension($splitBy) ? $splitBy : null,
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
