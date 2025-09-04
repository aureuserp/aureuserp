<?php

namespace Webkul\Sale\Filament\Widgets;

use Carbon\Carbon;
use Filament\Widgets\BarChartWidget;
use Webkul\Sale\Models\Order;

class YearlyComparisonWidget extends BarChartWidget
{
    protected static ?string $heading = 'Yearly Sales Comparison';

    protected function getData(): array
    {
        $currentYear = now()->year;
        $previousYear = $currentYear - 1;

        $currentYearSales = Order::whereYear('created_at', $currentYear)->sum('amount_total');
        $previousYearSales = Order::whereYear('created_at', $previousYear)->sum('amount_total');

        return [
            'datasets' => [
                [
                    'label' => 'Sales',
                    'data' => [
                        $previousYearSales,
                        $currentYearSales,
                    ],
                    'backgroundColor' => ['#a3e635', '#3b82f6'],
                ],
            ],
            'labels' => [
                (string)$previousYear,
                (string)$currentYear,
            ],
        ];
    }
}
