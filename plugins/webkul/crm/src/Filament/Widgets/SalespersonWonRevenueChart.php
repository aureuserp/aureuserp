<?php

namespace Webkul\Crm\Filament\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Illuminate\Contracts\Support\Htmlable;
use Webkul\Crm\Filament\Widgets\Concerns\HasCRMDashboardFilters;
use Webkul\Security\Models\User;
use Webkul\Support\Filament\Widgets\DashboardChartWidget;

class SalespersonWonRevenueChart extends DashboardChartWidget
{
    use HasCRMDashboardFilters, HasWidgetShield;

    protected static ?int $sort = 5;

    public function getHeading(): string|Htmlable|null
    {
        return __('crm::filament/widgets/crm-dashboard.salespeople.heading');
    }

    protected function getData(): array
    {
        $revenues = $this->wonOpportunities()
            ->whereNotNull('crm_leads.user_id')
            ->selectRaw('crm_leads.user_id, SUM(crm_leads.expected_revenue) as revenue')
            ->groupBy('crm_leads.user_id')
            ->orderByDesc('revenue')
            ->limit(6)
            ->pluck('revenue', 'user_id')
            ->filter(fn ($revenue): bool => (float) $revenue > 0);

        $names = User::query()->whereIn('id', $revenues->keys())->pluck('name', 'id');

        $ramp = $this->chartRamp();

        return [
            'datasets' => [
                [
                    'label'           => __('crm::filament/widgets/crm-dashboard.salespeople.revenue'),
                    'data'            => $revenues->values()->map(fn ($revenue): float => round((float) $revenue, 2))->all(),
                    'backgroundColor' => $revenues->values()->keys()->map(fn (int $index): string => $ramp[$index % count($ramp)])->all(),
                    'borderRadius'    => 8,
                    'borderSkipped'   => 'bottom',
                    'maxBarThickness' => 34,
                ],
            ],
            'labels' => $revenues->keys()->map(fn ($userId): string => $names[$userId] ?? '—')->values()->all(),
        ];
    }

    protected function getOptions(): array
    {
        return $this->barChartOptions(6);
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
