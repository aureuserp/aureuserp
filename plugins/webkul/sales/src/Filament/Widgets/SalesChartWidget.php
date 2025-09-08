<?php

namespace Webkul\Sale\Filament\Widgets;

use Carbon\Carbon;
use Filament\Widgets\LineChartWidget;
use Webkul\Sale\Models\Order;
use Webkul\Sale\Models\OrderLine;

class SalesChartWidget extends LineChartWidget
{
    protected int|string|array $columnSpan = 'full';

    protected static ?string $maxHeight = '400px';

    public function getHeading(): ?string
    {
        return __('sales::filament/widgets/sale-chart.heading.sale-trends');
    }

    protected function getData(): array
    {
        // 🔹 Get date range: Last 15 days
        $startDate = now()->subDays(14)->startOfDay();
        $endDate = now()->endOfDay();

        // 🔹 Fetch all order lines in that range
        $lines = OrderLine::query()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        // 🔹 Group lines by date
        $linesByDay = $lines->groupBy(function ($line) {
            return Carbon::parse($line->created_at)->format('Y-m-d');
        });

        $labels = [];
        $saleData = [];
        $draftData = [];
        $sentData = [];
        $cancelData = [];

        // 🔹 Loop through each day in the range
        $period = new \DatePeriod(
            $startDate,
            new \DateInterval('P1D'),
            $endDate->copy()->addDay()
        );

        foreach ($period as $dt) {
            $dateKey = $dt->format('Y-m-d');
            $labels[] = $dateKey;

            $dailyLines = $linesByDay[$dateKey] ?? collect();

            // 🔹 Sum totals by state
            $saleData[] = $dailyLines->where('state', 'sale')->sum('price_total');
            $draftData[] = $dailyLines->where('state', 'draft')->sum('price_total');
            $sentData[] = $dailyLines->where('state', 'sent')->sum('price_total');
            $cancelData[] = $dailyLines->where('state', 'cancel')->sum('price_total');
        }

        return [
            'datasets' => [
                [
                    'label'           => 'Sales (Confirmed)',
                    'data'            => $saleData,
                    'borderColor'     => '#3b82f6',
                    'backgroundColor' => 'rgba(59,130,246,0.2)',
                ],
                [
                    'label'           => 'Draft Orders',
                    'data'            => $draftData,
                    'borderColor'     => '#fbbf24',
                    'backgroundColor' => 'rgba(251,191,36,0.2)',
                ],
                [
                    'label'           => 'Sent Orders',
                    'data'            => $sentData,
                    'borderColor'     => '#10b981',
                    'backgroundColor' => 'rgba(16,185,129,0.2)',
                ],
                [
                    'label'           => 'Cancelled Orders',
                    'data'            => $cancelData,
                    'borderColor'     => '#ef4444',
                    'backgroundColor' => 'rgba(239,68,68,0.2)',
                ],
            ],
            'labels' => $labels,
        ];
    }
}
