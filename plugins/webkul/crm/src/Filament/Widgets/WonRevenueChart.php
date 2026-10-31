<?php

namespace Webkul\Crm\Filament\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\ChartWidget;
use Illuminate\Contracts\Support\Htmlable;
use Webkul\Crm\Filament\Widgets\Concerns\HasCRMDashboardFilters;
use Webkul\Crm\Filament\Widgets\Concerns\StylesCRMDashboardCharts;

class WonRevenueChart extends ChartWidget
{
    use HasCRMDashboardFilters, HasWidgetShield, StylesCRMDashboardCharts;

    protected static ?int $sort = 2;

    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 'full';

    protected ?string $maxHeight = '300px';

    public function getHeading(): string|Htmlable|null
    {
        return __('crm::filament/widgets/crm-dashboard.won-revenue.heading');
    }

    protected function getData(): array
    {
        $bucket = db_dialect()->monthBucket('crm_leads.closed_at');

        $revenues = $this->wonOpportunities()
            ->selectRaw("{$bucket} as period, SUM(crm_leads.expected_revenue) as revenue")
            ->groupByRaw($bucket)
            ->pluck('revenue', 'period');

        [$start, $end] = $this->periodRange();

        $labels = [];
        $data = [];

        for ($cursor = $start->copy()->startOfMonth(); $cursor <= $end; $cursor->addMonth()) {
            $labels[] = $cursor->translatedFormat('M Y');
            $data[] = round((float) ($revenues[$cursor->format('Y-m')] ?? 0), 2);
        }

        return [
            'datasets' => [$this->barDataset(__('crm::filament/widgets/crm-dashboard.won-revenue.revenue'), $data)],
            'labels'   => $labels,
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
