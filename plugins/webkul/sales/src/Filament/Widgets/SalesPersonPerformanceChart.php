<?php

namespace Webkul\Sale\Filament\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\ChartWidget;
use Illuminate\Contracts\Support\Htmlable;
use Webkul\Sale\Filament\Widgets\Concerns\HasSaleDashboardFilters;

class SalesPersonPerformanceChart extends ChartWidget
{
    use HasSaleDashboardFilters, HasWidgetShield;

    protected const BAR_COLOR = '#2a78d6';

    protected const INK_MUTED = '#898781';

    protected const GRID = 'rgba(137, 135, 129, 0.25)';

    protected static ?int $sort = 4;

    protected static bool $isLazy = false;

    protected ?string $maxHeight = '300px';

    public function getHeading(): string|Htmlable|null
    {
        return __('sales::filament/widgets/sales-dashboard.sales-persons.heading');
    }

    protected function getData(): array
    {
        $rows = $this->saleOrders()
            ->leftJoin('users', 'users.id', '=', 'sales_orders.user_id')
            ->groupBy('users.id', 'users.name')
            ->addSelect('users.name as name')
            ->selectRaw('SUM(sales_orders.amount_total) as revenue')
            ->havingRaw('SUM(sales_orders.amount_total) > 0')
            ->orderByDesc('revenue')
            ->limit(6)
            ->get();

        return [
            'datasets' => [
                [
                    'label'           => __('sales::filament/widgets/sales-dashboard.sales-persons.revenue'),
                    'data'            => $rows->map(fn ($row): float => round((float) $row->revenue, 2))->all(),
                    'backgroundColor' => self::BAR_COLOR,
                    'borderRadius'    => 4,
                    'borderSkipped'   => 'bottom',
                    'maxBarThickness' => 40,
                ],
            ],
            'labels' => $rows
                ->map(fn ($row): string => $row->name ?: __('sales::filament/widgets/sales-dashboard.unknown'))
                ->all(),
        ];
    }

    protected function getOptions(): array
    {
        return [
            'maintainAspectRatio' => false,
            'plugins'             => ['legend' => ['display' => false]],
            'scales'              => [
                'x' => [
                    'border' => ['display' => false],
                    'grid'   => ['display' => false],
                    'ticks'  => ['color' => self::INK_MUTED],
                ],
                'y' => [
                    'beginAtZero' => true,
                    'border'      => ['display' => false],
                    'grid'        => ['color' => self::GRID],
                    'ticks'       => ['color' => self::INK_MUTED],
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
