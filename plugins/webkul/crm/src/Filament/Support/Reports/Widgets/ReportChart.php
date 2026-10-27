<?php

namespace Webkul\Crm\Filament\Support\Reports\Widgets;

use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Locked;
use Webkul\Crm\Enums\ReportChartType;
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
        '#6366f1', '#f59e0b', '#10b981', '#ef4444', '#0ea5e9', '#8b5cf6',
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
        return static::chartType($this->getReportFilters())->value;
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
            'type'    => static::chartType($filters)->value,
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

    public static function chartType(array $filters): ReportChartType
    {
        return ReportChartType::tryFrom((string) ($filters['chart_type'] ?? '')) ?? ReportChartType::BAR;
    }

    public static function options(array $filters): array
    {
        if (static::chartType($filters) === ReportChartType::PIE) {
            return [
                'scales' => [
                    'x' => ['display' => false],
                    'y' => ['display' => false],
                ],
            ];
        }

        $isStacked = static::chartType($filters) === ReportChartType::BAR && filled($filters['split_by'] ?? null);

        return [
            'scales' => [
                'x' => ['stacked' => $isStacked],
                'y' => ['stacked' => $isStacked, 'beginAtZero' => true],
            ],
        ];
    }

    public static function data(string $report, Builder $query, array $filters): array
    {
        $isPie = static::chartType($filters) === ReportChartType::PIE;

        $data = $report::aggregate(
            $query,
            (string) ($filters['group_by'] ?? $report::getDefaultGroupBy()),
            $isPie ? null : ($filters['split_by'] ?? null),
            (string) ($filters['measure'] ?? $report::getDefaultMeasure()),
        );

        if ($isPie) {
            return [
                'datasets' => [[
                    'label'           => $data['series'][0]['label'] ?? '',
                    'data'            => $data['series'][0]['data'] ?? [],
                    'backgroundColor' => array_map(fn (int $index): string => static::COLORS[$index % count(static::COLORS)], array_keys($data['labels'])),
                ]],
                'labels' => $data['labels'],
            ];
        }

        return [
            'datasets' => collect($data['series'])->values()->map(fn (array $series, int $index): array => [
                'label'           => $series['label'],
                'data'            => $series['data'],
                'backgroundColor' => static::COLORS[$index % count(static::COLORS)],
                'borderColor'     => static::COLORS[$index % count(static::COLORS)],
                'maxBarThickness' => 56,
            ])->all(),
            'labels' => $data['labels'],
        ];
    }
}
