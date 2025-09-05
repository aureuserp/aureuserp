<?php

namespace Webkul\Sale\Filament\Widgets;

use Carbon\Carbon;
use Filament\Widgets\LineChartWidget;
use Webkul\Sale\Models\Order;

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
        $startDate = request()->input('filters.startDate') ? Carbon::parse(request()->input('filters.startDate')) : now()->subYear();
        $endDate = request()->input('filters.endDate') ? Carbon::parse(request()->input('filters.endDate')) : now();

        $orders = Order::query()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get()
            ->groupBy(function ($order) {
                return Carbon::parse($order->created_at)->format('Y-m');
            });

        $labels = [];
        $data = [];
        $period = new \DatePeriod(
            $startDate->startOfMonth(),
            new \DateInterval('P1M'),
            $endDate->copy()->addMonth()->startOfMonth()
        );
        foreach ($period as $dt) {
            $label = $dt->format('Y-m');
            $labels[] = $label;
            $data[] = isset($orders[$label]) ? $orders[$label]->sum('amount_total') : 0;
        }

        return [
            'datasets' => [
                [
                    'label'           => 'Sales',
                    'data'            => $data,
                    'borderColor'     => '#3b82f6',
                    'backgroundColor' => 'rgba(59,130,246,0.2)',
                ],
            ],
            'labels' => $labels,
        ];
    }
}
