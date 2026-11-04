<?php

namespace Webkul\Crm\Filament\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Illuminate\Contracts\Support\Htmlable;
use Webkul\Crm\Filament\Widgets\Concerns\HasCRMDashboardFilters;
use Webkul\Support\Filament\Widgets\DashboardChartWidget;

class WonRevenueChart extends DashboardChartWidget
{
    use HasCRMDashboardFilters, HasWidgetShield;

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 1;

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
            'datasets' => [
                $this->gradientLineDataset($data, __('crm::filament/widgets/crm-dashboard.won-revenue.revenue')),
            ],
            'labels' => $labels,
        ];
    }

    protected function getOptions(): array
    {
        $options = $this->lineChartOptions(12);

        $options['plugins']['lineGradient'] = $this->revenueLineGradient();

        return $options;
    }

    protected function getType(): string
    {
        return 'line';
    }
}
