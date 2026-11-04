<?php

namespace Webkul\Crm\Filament\Support\Reports\Widgets;

use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Locked;
use Webkul\Crm\Filament\Clusters\Reporting\Resources\ActivityAnalysisResource;
use Webkul\Crm\Filament\Clusters\Reporting\Resources\ForecastResource;
use Webkul\Crm\Filament\Clusters\Reporting\Resources\LeadAnalysisResource;
use Webkul\Crm\Filament\Clusters\Reporting\Resources\PipelineAnalysisResource;

class ReportChart extends ChartWidget
{
    use InteractsWithPageFilters;

    public const RESOURCES = [
        ForecastResource::class,
        PipelineAnalysisResource::class,
        LeadAnalysisResource::class,
        ActivityAnalysisResource::class,
    ];

    #[Locked]
    public string $resource;

    public function mount(): void
    {
        $this->authorizeReport();

        parent::mount();
    }

    protected function authorizeReport(): void
    {
        abort_unless(in_array($this->resource, static::RESOURCES, true) && $this->resource::canViewAny(), 403);
    }

    protected int|string|array $columnSpan = 'full';

    protected ?string $maxHeight = '420px';

    protected ?string $pollingInterval = null;

    protected const COLORS = [
        '#ef4444', '#3b82f6', '#22c55e', '#f59e0b', '#0ea5e9', '#8b5cf6',
        '#f97316', '#14b8a6', '#ec4899', '#84cc16', '#64748b', '#a855f7',
    ];

    protected function getReport(): string
    {
        return $this->resource::getReport();
    }

    protected function getReportFilters(): array
    {
        return [...$this->getReport()::getDefaultFilters(), ...($this->pageFilters ?? [])];
    }

    public function getHeading(): string
    {
        return static::heading($this->getReport(), $this->getReportFilters());
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return static::options($this->getReportFilters());
    }

    protected function getData(): array
    {
        $this->authorizeReport();

        $report = $this->getReport();

        $filters = $this->getReportFilters();

        return static::data($report, $report::applyFilters($this->resource::getEloquentQuery(), $filters), $filters);
    }

    public static function build(string $report, Builder $query, array $filters): array
    {
        return [
            'heading' => static::heading($report, $filters),
            'type'    => 'bar',
            'data'    => static::data($report, $query, $filters),
            'options' => static::options($filters),
        ];
    }

    public static function heading(string $report, array $filters): string
    {
        return __('crm::filament/clusters/reporting/report.chart.heading', [
            'measure' => $report::getMeasure($filters['measure'] ?? null)->label,
            'group'   => ($report::getDimension($filters['group_by'] ?? null) ?? $report::getDimension($report::getDefaultGroupBy()))->label,
        ]);
    }

    public static function options(array $filters): array
    {
        $isStacked = filled($filters['split_by'] ?? null);

        return [
            'scales' => [
                'x' => ['stacked' => $isStacked],
                'y' => ['stacked' => $isStacked, 'beginAtZero' => true],
            ],
        ];
    }

    public static function data(string $report, Builder $query, array $filters): array
    {
        $data = $report::aggregate(
            $query,
            (string) ($filters['group_by'] ?? $report::getDefaultGroupBy()),
            $filters['split_by'] ?? null,
            (string) ($filters['measure'] ?? $report::getDefaultMeasure()),
        );

        return [
            'datasets' => collect($data['series'])->values()->map(fn (array $series, int $index): array => [
                'label'           => $series['label'],
                'data'            => $series['data'],
                'backgroundColor' => static::COLORS[$index % count(static::COLORS)],
                'maxBarThickness' => 56,
            ])->all(),
            'labels' => $data['labels'],
        ];
    }
}
