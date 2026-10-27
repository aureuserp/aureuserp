<?php

namespace Webkul\Crm\Filament\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\ChartWidget;
use Illuminate\Contracts\Support\Htmlable;
use Webkul\Crm\Filament\Widgets\Concerns\HasCRMDashboardFilters;
use Webkul\Crm\Filament\Widgets\Concerns\StylesCRMDashboardCharts;
use Webkul\Crm\Models\Stage;

class PipelineByStageChart extends ChartWidget
{
    use HasCRMDashboardFilters, HasWidgetShield, StylesCRMDashboardCharts;

    protected static ?int $sort = 3;

    protected static bool $isLazy = false;

    protected ?string $maxHeight = '300px';

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

        return [
            'datasets' => [$this->barDataset(
                __('crm::filament/widgets/crm-dashboard.pipeline.expected-revenue'),
                $stages->map(fn (Stage $stage): float => round((float) ($revenues[$stage->id] ?? 0), 2))->all(),
            )],
            'labels' => $stages->pluck('name')->all(),
        ];
    }

    protected function getOptions(): array
    {
        return $this->barOptions();
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
