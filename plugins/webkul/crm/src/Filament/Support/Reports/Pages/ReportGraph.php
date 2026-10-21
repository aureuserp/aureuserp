<?php

namespace Webkul\Crm\Filament\Support\Reports\Pages;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\ToggleButtons;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Webkul\Crm\Filament\Concerns\HasCrmClusterNavigation;
use Webkul\Crm\Filament\Support\Reports\Report;
use Webkul\Crm\Filament\Support\Reports\Widgets\ReportChart;

abstract class ReportGraph extends Page
{
    use HasCrmClusterNavigation, HasFiltersForm;

    public static function canAccess(array $parameters = []): bool
    {
        return static::getResource()::canViewAny();
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

    protected function getHeaderActions(): array
    {
        $report = $this->getReport();

        return [
            ...static::getResource()::getViewSwitcherActions(static::getResourcePageName()),
            ...static::getResource()::getExportActions(fn (Builder $query): Builder => $query
                ->withoutGlobalScope(SoftDeletingScope::class)
                ->whereIn(
                    $query->getModel()->getQualifiedKeyName(),
                    $report::applyFilters(
                        static::getResource()::getEloquentQuery(),
                        [...$report::getDefaultFilters(), ...array_filter($this->filters ?? [], fn (mixed $value): bool => $value !== null)],
                    )->reorder()->pluck($query->getModel()->getQualifiedKeyName()),
                )),
        ];
    }

    public function filtersForm(Schema $schema): Schema
    {
        $report = $this->getReport();

        $defaults = $report::getDefaultFilters();

        $label = fn (string $key): string => __('crm::filament/clusters/reporting/report.filters.'.$key);

        return $schema->components([
            Section::make()
                ->columns(['md' => 2, 'xl' => 4])
                ->schema([
                    Select::make('measure')
                        ->label($label('measure'))
                        ->options($report::getMeasureOptions())
                        ->default($defaults['measure'])
                        ->selectablePlaceholder(false)
                        ->native(false),
                    Select::make('group_by')
                        ->label($label('group-by'))
                        ->options($report::getDimensionOptions())
                        ->default($defaults['group_by'])
                        ->selectablePlaceholder(false)
                        ->native(false),
                    Select::make('split_by')
                        ->label($label('split-by'))
                        ->options($report::getDimensionOptions())
                        ->default($defaults['split_by'])
                        ->placeholder($label('no-split'))
                        ->native(false),
                    ToggleButtons::make('chart_type')
                        ->label($label('chart-type'))
                        ->options([
                            Report::CHART_BAR  => __('crm::filament/clusters/reporting/report.chart.types.bar'),
                            Report::CHART_LINE => __('crm::filament/clusters/reporting/report.chart.types.line'),
                            Report::CHART_PIE  => __('crm::filament/clusters/reporting/report.chart.types.pie'),
                        ])
                        ->icons([
                            Report::CHART_BAR  => 'heroicon-o-chart-bar',
                            Report::CHART_LINE => 'heroicon-o-presentation-chart-line',
                            Report::CHART_PIE  => 'heroicon-o-chart-pie',
                        ])
                        ->default($defaults['chart_type'])
                        ->grouped(),
                    Select::make('presets')
                        ->label($label('quick-filters'))
                        ->options($report::getPresetOptions())
                        ->default($defaults['presets'])
                        ->multiple()
                        ->native(false),
                    Select::make('user_ids')
                        ->label($label('salespeople'))
                        ->options(fn (): array => $report::getSalespersonOptions())
                        ->multiple()
                        ->searchable(),
                    Select::make('team_ids')
                        ->label($label('teams'))
                        ->options(fn (): array => $report::getTeamOptions())
                        ->multiple()
                        ->searchable(),
                ]),
        ]);
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            EmbeddedSchema::make('filtersForm'),
            Livewire::make(ReportChart::class, fn (): array => [
                'resource'    => static::getResource(),
                'pageFilters' => $this->filters,
            ])->key(fn (): string => 'crm-report-chart-'.md5(json_encode($this->filters ?? []))),
        ]);
    }
}
