<?php

namespace Webkul\Crm\Filament\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\ChartWidget;
use Illuminate\Contracts\Support\Htmlable;
use Webkul\Crm\Filament\Widgets\Concerns\HasCRMDashboardFilters;
use Webkul\Crm\Filament\Widgets\Concerns\StylesCRMDashboardCharts;
use Webkul\Security\Models\User;

class SalespersonWonRevenueChart extends ChartWidget
{
    use HasCRMDashboardFilters, HasWidgetShield, StylesCRMDashboardCharts;

    protected static ?int $sort = 5;

    protected static bool $isLazy = false;

    protected ?string $maxHeight = '300px';

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
            ->limit(10)
            ->pluck('revenue', 'user_id')
            ->filter(fn ($revenue): bool => (float) $revenue > 0);

        $names = User::query()->whereIn('id', $revenues->keys())->pluck('name', 'id');

        return [
            'datasets' => [$this->barDataset(
                __('crm::filament/widgets/crm-dashboard.salespeople.revenue'),
                $revenues->values()->map(fn ($revenue): float => round((float) $revenue, 2))->all(),
            )],
            'labels' => $revenues->keys()->map(fn ($userId): string => $names[$userId] ?? '—')->all(),
        ];
    }

    protected function getOptions(): array
    {
        return [...$this->barOptions(), 'indexAxis' => 'y'];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
