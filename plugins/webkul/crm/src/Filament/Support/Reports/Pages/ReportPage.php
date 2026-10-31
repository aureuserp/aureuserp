<?php

namespace Webkul\Crm\Filament\Support\Reports\Pages;

use Filament\Forms\Components\Select;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Html;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Session;
use Webkul\Crm\Enums\ViewLayout;
use Webkul\Crm\Filament\Concerns\HasCRMClusterNavigation;
use Webkul\Crm\Filament\Concerns\HasCRMTableViews;
use Webkul\Crm\Filament\Concerns\HasViewLayouts;
use Webkul\Crm\Filament\Support\Reports\Widgets\ReportChart;
use Webkul\Crm\Filament\Widgets\RecordsChart;

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

        if (filled($this->tableFilters['team_ids']['values'] ?? null)) {
            $this->activeTableView ??= 'default';
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
            ViewLayout::GRAPH => $resource::table($table)
                ->filters($resource::getReportTableFilters())
                ->groups($report::getTableGroups())
                ->defaultGroup($report::getDefaultGroupBy())
                ->paginated(false)
                ->recordActions([])
                ->toolbarActions([])
                ->emptyState(fn (): Schema => $this->getSchema('graph')),
            default => $resource::table($table)
                ->filters($resource::getReportTableFilters())
                ->recordActions([])
                ->toolbarActions($resource::getReportBulkActions()),
        };
    }

    public function graph(Schema $schema): Schema
    {
        $report = $this->getReport();

        return $schema
            ->extraAttributes(['class' => 'crm-leads-graph crm-report-graph'])
            ->columns(['sm' => 2, 'xl' => 4])
            ->components([
                Select::make('graphMeasure')
                    ->label(__('crm::filament/clusters/reporting/report.filters.measure'))
                    ->options($report::getMeasureOptions())
                    ->selectablePlaceholder(false)
                    ->live(),
                Select::make('graphSplitBy')
                    ->label(__('crm::filament/clusters/reporting/report.filters.split-by'))
                    ->options($report::getDimensionOptions())
                    ->placeholder(__('crm::filament/clusters/reporting/report.filters.no-split'))
                    ->live(),
                Html::make(function (): Htmlable {
                    $chart = $this->getGraph()['chart'];

                    return RecordsChart::embed($chart['data'], $chart['options'], $chart['heading'], '26rem');
                })->columnSpanFull(),
            ]);
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
            'filters' => $filters,
            'chart'   => ReportChart::build($report, $this->getFilteredTableQuery(), $filters),
        ];
    }
}
