<?php

namespace Webkul\Crm\Filament\Support\Reports\Pages;

use Filament\Forms\Components\Select;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Flex;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\Summarizers\Summarizer;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\QueryBuilder as QueryBuilderFilter;
use Filament\Tables\Table;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Livewire\Attributes\Session;
use Webkul\Crm\Enums\ViewLayout;
use Webkul\Crm\Filament\Concerns\HasCRMClusterNavigation;
use Webkul\Crm\Filament\Concerns\HasCRMTableViews;
use Webkul\Crm\Filament\Concerns\HasViewLayouts;
use Webkul\Crm\Filament\Support\Reports\Measure;
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

    public function removeTableFilter(string $filterName, ?string $field = null, bool $isRemovingAllFilters = false): void
    {
        if (filled($field)
            || ! $this->getTable()->getFilter($filterName) instanceof QueryBuilderFilter) {
            parent::removeTableFilter($filterName, $field, $isRemovingAllFilters);

            return;
        }

        $this->getTableFiltersForm()->getComponentByStatePath($filterName)?->getChildSchema()->fill(['rules' => []]);

        if ($isRemovingAllFilters) {
            return;
        }

        $this->getTable()->hasDeferredFilters() ? $this->applyTableFilters() : $this->handleTableFilterUpdates();
    }

    public function updatedGraphMeasure(): void
    {
        if ($this->isViewLayout(ViewLayout::LIST)) {
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
            ViewLayout::GRAPH => $resource::table($table)
                ->filters($resource::getReportTableFilters())
                ->groups($report::getTableGroups())
                ->defaultGroup($report::getDefaultGroupBy())
                ->paginated(false)
                ->recordActions([])
                ->toolbarActions([])
                ->emptyState(fn (): Htmlable => $this->getGraphChart()),
            default => $resource::table($table)
                ->pushColumns([$this->getMeasureColumn()])
                ->filters($resource::getReportTableFilters())
                ->groups($report::getTableGroups())
                ->recordActions([])
                ->toolbarActions($resource::getReportBulkActions()),
        };
    }

    protected function getTableQuery(): ?Builder
    {
        $query = parent::getTableQuery();

        $expression = $this->getSelectedMeasure()->rowExpression();

        if (! $query
            || ! $expression
            || ! $this->isViewLayout(ViewLayout::LIST)) {
            return $query;
        }

        if (blank($query->getQuery()->columns)) {
            $query->select($query->getModel()->qualifyColumn('*'));
        }

        return $query->selectRaw("{$expression} as report_measure");
    }

    protected function getMeasureColumn(): TextColumn
    {
        $measure = $this->getSelectedMeasure();
        $currency = $this->getReport()::getCurrencyCode();
        $hasRowValue = filled($measure->rowExpression());

        return TextColumn::make('report_measure')
            ->label($measure->label)
            ->state(fn ($record): mixed => $hasRowValue ? $record->getAttribute('report_measure') : null)
            ->formatStateUsing(fn (mixed $state): string => $measure->format($state, $currency))
            ->placeholder('')
            ->alignEnd()
            ->sortable($hasRowValue, query: fn (Builder $query, string $direction): Builder => $query->orderBy('report_measure', $direction))
            ->summarize(
                Summarizer::make()
                    ->using(fn (QueryBuilder $query): mixed => $query->selectRaw("{$measure->aggregate} as aggregate")->value('aggregate'))
                    ->formatStateUsing(fn (mixed $state): string => $measure->format($state, $currency)),
            );
    }

    public function getSelectedMeasure(): Measure
    {
        $measures = $this->getReport()::measures();

        return $measures[$this->graphMeasure] ?? $measures[$this->getReport()::getDefaultFilters()['measure']];
    }

    public function reportMeasures(Schema $schema): Schema
    {
        $report = $this->getReport();

        return $schema
            ->extraAttributes(['class' => 'crm-report-measures'])
            ->components([
                Flex::make([
                    Select::make('graphMeasure')
                        ->label(__('crm::filament/clusters/reporting/report.filters.measure'))
                        ->hiddenLabel()
                        ->prefix(__('crm::filament/clusters/reporting/report.filters.measure'))
                        ->options($report::getMeasureOptions())
                        ->selectablePlaceholder(false)
                        ->live(),
                    Select::make('graphSplitBy')
                        ->label(__('crm::filament/clusters/reporting/report.filters.split-by'))
                        ->hiddenLabel()
                        ->prefix(__('crm::filament/clusters/reporting/report.filters.split-by'))
                        ->options($report::getDimensionOptions())
                        ->placeholder(__('crm::filament/clusters/reporting/report.filters.no-split'))
                        ->live()
                        ->visible(fn (): bool => $this->isViewLayout(ViewLayout::GRAPH)),
                ]),
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

    protected function getGraphChart(): Htmlable
    {
        $chart = $this->getGraph()['chart'];

        return RecordsChart::embed($chart['data'], $chart['options'], $chart['heading'], '26rem', 'crm-leads-graph crm-report-graph');
    }
}
