<?php

namespace Webkul\Crm\Filament\Support\Reports\Widgets;

use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Livewire\Attributes\Locked;
use Webkul\Crm\Filament\Clusters\Reporting\Resources\ActivityAnalysisResource;
use Webkul\Crm\Filament\Clusters\Reporting\Resources\ForecastResource;
use Webkul\Crm\Filament\Clusters\Reporting\Resources\LeadAnalysisResource;
use Webkul\Crm\Filament\Clusters\Reporting\Resources\PipelineAnalysisResource;
use Webkul\Crm\Filament\Support\Reports\Report;

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
        return [...$this->getReport()::getDefaultFilters(), ...array_filter($this->pageFilters ?? [], fn (mixed $value): bool => $value !== null)];
    }

    public function getHeading(): string
    {
        $report = $this->getReport();

        $filters = $this->getReportFilters();

        return __('crm::filament/clusters/reporting/report.chart.heading', [
            'measure' => $report::getMeasure($filters['measure'])->label,
            'group'   => ($report::getDimension($filters['group_by']) ?? $report::getDimension($report::getDefaultGroupBy()))->label,
        ]);
    }

    protected function getType(): string
    {
        return in_array($type = $this->getReportFilters()['chart_type'], [Report::CHART_BAR, Report::CHART_LINE, Report::CHART_PIE], true)
            ? $type
            : Report::CHART_BAR;
    }

    protected function getOptions(): array
    {
        if ($this->getType() === Report::CHART_PIE) {
            return [
                'scales' => [
                    'x' => ['display' => false],
                    'y' => ['display' => false],
                ],
            ];
        }

        $isStacked = $this->getType() === Report::CHART_BAR && filled($this->getReportFilters()['split_by']);

        return [
            'scales' => [
                'x' => ['stacked' => $isStacked],
                'y' => ['stacked' => $isStacked, 'beginAtZero' => true],
            ],
        ];
    }

    protected function getData(): array
    {
        $this->authorizeReport();

        $report = $this->getReport();

        $filters = $this->getReportFilters();

        $isPie = $this->getType() === Report::CHART_PIE;

        $data = $report::aggregate(
            $report::applyFilters($this->resource::getEloquentQuery(), $filters),
            (string) $filters['group_by'],
            $isPie ? null : $filters['split_by'],
            (string) $filters['measure'],
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
