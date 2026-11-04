<?php

namespace Webkul\Crm\Filament\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Illuminate\Contracts\Support\Htmlable;
use Webkul\Crm\Filament\Widgets\Concerns\HasCRMDashboardFilters;
use Webkul\Crm\Models\Stage;
use Webkul\Support\Filament\Widgets\DashboardChartWidget;

class PipelineByStageChart extends DashboardChartWidget
{
    use HasCRMDashboardFilters, HasWidgetShield;

    protected static ?int $sort = 4;

    public function getHeading(): string|Htmlable|null
    {
        return __('crm::filament/widgets/crm-dashboard.pipeline.heading');
    }

    public function getDescription(): string|Htmlable|null
    {
        return __('crm::filament/widgets/crm-dashboard.pipeline.description');
    }

    protected function getData(): array
    {
        $revenues = $this->openOpportunities()
            ->selectRaw('crm_leads.stage_id, SUM(crm_leads.expected_revenue) as revenue')
            ->groupBy('crm_leads.stage_id')
            ->pluck('revenue', 'stage_id');

        $stages = Stage::query()->where('is_won', false)->orderBy('sort')->get(['id', 'name']);

        $ramp = $this->chartRamp();

        return [
            'datasets' => [
                [
                    'label'           => __('crm::filament/widgets/crm-dashboard.pipeline.expected-revenue'),
                    'data'            => $stages->map(fn (Stage $stage): float => round((float) ($revenues[$stage->id] ?? 0), 2))->all(),
                    'backgroundColor' => $stages->keys()->map(fn (int $index): string => $ramp[$index % count($ramp)])->all(),
                    'borderRadius'    => 8,
                    'borderSkipped'   => 'bottom',
                    'maxBarThickness' => 34,
                ],
            ],
            'labels' => $stages->pluck('name')->all(),
        ];
    }

    protected function getOptions(): array
    {
        return $this->barChartOptions();
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
