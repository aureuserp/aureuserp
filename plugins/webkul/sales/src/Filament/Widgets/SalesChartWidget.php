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
    use InteractsWithPageFilters;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $maxHeight = '400px';

    public function getHeading(): ?string
    {
        return __('sales::filament/widgets/sale-chart.heading.sale-trends');
    }

    protected function getData(): array
    {
        [$startDate, $endDate] = $this->getDateRange();

        $orders = $this->getFilteredOrders($startDate, $endDate);

        [$labels, $datasets] = $this->prepareChartData($orders, $startDate, $endDate);

        return [
            'datasets' => $datasets,
            'labels'   => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    /**
     * 🔹 Get the date range from filters or defaults.
     */
    protected function getDateRange(): array
    {
        $filters = $this->filters;

        $startDate = ! empty($filters['start_date'])
            ? Carbon::parse($filters['start_date'])->startOfDay()
            : now()->subMonth()->startOfDay();

        $endDate = ! empty($filters['end_date'])
            ? Carbon::parse($filters['end_date'])->endOfDay()
            : now()->endOfDay();

        return [$startDate, $endDate];
    }

    /**
     * 🔹 Fetch filtered orders within the date range.
     */
    protected function getFilteredOrders(Carbon $startDate, Carbon $endDate)
    {
        $query = Order::query()->whereBetween('date_order', [$startDate, $endDate]);

        if (! empty($this->filters['salesperson_id'])) {
            $query->where('user_id', $this->filters['salesperson_id']);
        }

        return $query->get();
    }

    /**
     * 🔹 Prepare datasets and labels for the chart.
     */
    protected function prepareChartData($orders, Carbon $startDate, Carbon $endDate): array
    {
        $ordersByDay = $orders->groupBy(fn($order) => Carbon::parse($order->date_order)->format('Y-m-d'));

        $labels     = [];
        $saleData   = [];
        $draftData  = [];
        $sentData   = [];
        $cancelData = [];

        $period = new \DatePeriod(
            $startDate,
            new \DateInterval('P1D'),
            $endDate->copy()->addDay()
        );

        foreach ($period as $dt) {
            $dateKey = $dt->format('Y-m-d');
            $labels[] = $dateKey;

            $dailyOrders = $ordersByDay[$dateKey] ?? collect();

            $saleData[]   = $this->countOrdersByState($dailyOrders, OrderState::SALE->value);
            $draftData[]  = $this->countOrdersByState($dailyOrders, OrderState::DRAFT->value);
            $sentData[]   = $this->countOrdersByState($dailyOrders, OrderState::SENT->value);
            $cancelData[] = $this->countOrdersByState($dailyOrders, OrderState::CANCEL->value);
        }

        return [
            $labels,
            [
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
        ];
    }

    /**
     * 🔹 Count orders by state.
     */
    protected function countOrdersByState($orders, string $state): int
    {
        return $orders->where('state', $state)->count();
    }
}
