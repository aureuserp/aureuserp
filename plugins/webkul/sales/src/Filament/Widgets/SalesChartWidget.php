<?php

namespace Webkul\Sale\Filament\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Webkul\Sale\Enums\OrderState;
use Webkul\Sale\Models\Order;

class SalesChartWidget extends ChartWidget
{
    use HasWidgetShield, InteractsWithPageFilters;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $maxHeight = '400px';

    public function getHeading(): ?string
    {
        return __('sales::filament/widgets/sale-chart.heading.sale-trends');
    }

    protected function getData(): array
    {
        $filters = $this->filters;

        // 🔹 Determine the date range
        $startDate = ! empty($filters['start_date'])
            ? Carbon::parse($filters['start_date'])->startOfDay()
            : now()->subMonth()->startOfDay();

        $endDate = ! empty($filters['end_date'])
            ? Carbon::parse($filters['end_date'])->endOfDay()
            : now()->endOfDay();

        // 🔹 Build base query on Orders
        $baseQuery = Order::query()
            ->whereBetween('date_order', [$startDate, $endDate]);

        if (! empty($filters['salesperson_id'])) {
            $baseQuery->where('user_id', $filters['salesperson_id']);
        }

        // 🔹 Fetch all orders in that range
        $orders = $baseQuery->get();

        // 🔹 Group orders by date
        $ordersByDay = $orders->groupBy(function ($order) {
            return Carbon::parse($order->date_order)->format('Y-m-d');
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

            $dailyOrders = $ordersByDay[$dateKey] ?? collect();

            // 🔹 Sum totals by state
            $saleData[] = $dailyOrders->where('state', OrderState::SALE)->count();
            $draftData[] = $dailyOrders->where('state', OrderState::DRAFT)->count();
            $sentData[] = $dailyOrders->where('state', OrderState::SENT)->count();
            $cancelData[] = $dailyOrders->where('state', OrderState::CANCEL)->count();
        }

        return [
            'datasets' => [
                [
                    'label'           => __('sales::filament/widgets/sale-chart.dataset.order-confirm'),
                    'data'            => $saleData,
                    'borderColor'     => '#3b82f6',
                    'backgroundColor' => 'rgba(59,130,246,0.2)',
                ],
                [
                    'label'           => __('sales::filament/widgets/sale-chart.dataset.order-draft'),
                    'data'            => $draftData,
                    'borderColor'     => '#fbbf24',
                    'backgroundColor' => 'rgba(251,191,36,0.2)',
                ],
                [
                    'label'           => __('sales::filament/widgets/sale-chart.dataset.order-sent'),
                    'data'            => $sentData,
                    'borderColor'     => '#10b981',
                    'backgroundColor' => 'rgba(16,185,129,0.2)',
                ],
                [
                    'label'           => __('sales::filament/widgets/sale-chart.dataset.order-cancel'),
                    'data'            => $cancelData,
                    'borderColor'     => '#ef4444',
                    'backgroundColor' => 'rgba(239,68,68,0.2)',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
