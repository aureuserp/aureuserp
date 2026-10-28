<?php

namespace Webkul\Sale\Filament\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Illuminate\Contracts\Support\Htmlable;
use Webkul\Sale\Filament\Widgets\Concerns\HasSaleDashboardFilters;
use Webkul\Support\Filament\Widgets\DashboardChartWidget;

class SalesPersonPerformanceChart extends DashboardChartWidget
{
    use HasSaleDashboardFilters, HasWidgetShield;

    protected static ?int $sort = 4;

    public function getHeading(): string|Htmlable|null
    {
        return __('sales::filament/widgets/sales-dashboard.sales-persons.heading');
    }

    protected function getData(): array
    {
        $revenue = 'SUM(CASE WHEN sales_orders.currency_rate > 0 THEN sales_orders.amount_total / sales_orders.currency_rate ELSE sales_orders.amount_total END)';

        $rows = $this->saleOrders()
            ->whereNotNull('sales_orders.user_id')
            ->join('users', 'users.id', '=', 'sales_orders.user_id')
            ->groupBy('users.id', 'users.name')
            ->addSelect('users.name as name')
            ->selectRaw("{$revenue} as revenue")
            ->havingRaw("{$revenue} > 0")
            ->orderByDesc('revenue')
            ->limit(6)
            ->get();

        $values = $rows->map(fn ($row): float => round((float) $row->revenue, 2))->all();

        $ramp = $this->chartRamp();

        $colors = array_map(fn (int $index): string => $ramp[$index % count($ramp)], array_keys($values));

        return [
            'datasets' => [
                [
                    'label'           => __('sales::filament/widgets/sales-dashboard.sales-persons.revenue'),
                    'data'            => $values,
                    'backgroundColor' => $colors,
                    'borderRadius'    => 8,
                    'borderSkipped'   => 'bottom',
                    'maxBarThickness' => 34,
                ],
            ],
            'labels' => $rows
                ->map(fn ($row): string => $row->name ?: __('sales::filament/widgets/sales-dashboard.unknown'))
                ->all(),
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
