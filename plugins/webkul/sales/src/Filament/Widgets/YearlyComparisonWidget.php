<?php

namespace Webkul\Sale\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Webkul\Sale\Enums\OrderState;
use Webkul\Sale\Models\Order;

class YearlyComparisonWidget extends ChartWidget
{
    use InteractsWithPageFilters;

    public function getHeading(): ?string
    {
        return __('sales::filament/widgets/year-sales.heading');
    }

    protected function getData(): array
    {
        $filters = $this->filters;

        $baseQuery = Order::where('state', OrderState::SALE);

        if (! empty($filters['salesperson_id'])) {
            $baseQuery->where('user_id', $filters['salesperson_id']);
        }

        $currentYear = now()->year;
        $previousYear = $currentYear - 1;

        $currentYearSales = (clone $baseQuery)
            ->whereYear('date_order', $currentYear)
            ->sum('amount_total');

        $previousYearSales = (clone $baseQuery)
            ->whereYear('date_order', $previousYear)
            ->sum('amount_total');

        return [
            'datasets' => [
                [
                    'label' => 'Sales',
                    'data'  => [
                        $previousYearSales,
                        $currentYearSales,
                    ],
                    'backgroundColor'    => ['#a3e635', '#3b82f6'],
                    'borderColor'        => '#1e293b', // Optional border for bars
                    'borderWidth'        => 1,
                    'barPercentage'      => 0.5, // Slimmer bars
                    'categoryPercentage' => 0.6,
                ],
            ],
            'labels' => [
                (string) $previousYear,
                (string) $currentYear,
            ],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
