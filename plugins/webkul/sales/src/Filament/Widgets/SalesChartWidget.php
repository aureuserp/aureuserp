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
    use InteractsWithPageFilters, HasWidgetShield;

    protected int|string|array $columnSpan = 'full';

    protected ?string $maxHeight = '400px';

    public function getHeading(): ?string
    {
        return __('sales::filament/pages/sales-dashboard.widgets.sales-chart.heading');
    }

    protected function getData(): array
    {
        [$startDate, $endDate] = $this->getDateRange();

        $orders = $this->getFilteredQuery()
            ->whereBetween('date_order', [$startDate, $endDate])
            ->get();

        [$labels, $datasets] = $this->prepareChartData($orders, $startDate, $endDate);

        return compact('labels', 'datasets');
    }

    protected function getType(): string
    {
        return 'line';
    }

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
     * 🔹 Apply all filters to the query
     */
    protected function getFilteredQuery()
    {
        $filters = $this->filters;

        $query = Order::query();

        if (! empty($filters['start_date'])) {
            $query->whereDate('date_order', '>=', $filters['start_date']);
        }

        if (! empty($filters['end_date'])) {
            $query->whereDate('date_order', '<=', $filters['end_date']);
        }

        if (! empty($filters['country_id'])) {
            $query->whereHas('partner', fn ($q) => $q->whereIn('country_id', (array) $filters['country_id']));
        }

        if (! empty($filters['product_id'])) {
            $query->whereHas('orderLines', fn ($q) => $q->whereIn('product_id', (array) $filters['product_id']));
        }

        if (! empty($filters['customer_id'])) {
            $query->whereIn('partner_id', (array) $filters['customer_id']);
        }

        if (! empty($filters['category_id'])) {
            $query->whereHas('orderLines.product.category', fn ($q) => $q->whereIn('category_id', (array) $filters['category_id']));
        }

        if (! empty($filters['salesteam_id'])) {
            $query->whereIn('team_id', (array) $filters['salesteam_id']);
        }

        if (! empty($filters['salesperson_id'])) {
            $query->whereIn('user_id', (array) $filters['salesperson_id']);
        }

        return $query;
    }

    protected function prepareChartData($orders, Carbon $startDate, Carbon $endDate): array
    {
        $ordersByDay = $orders->groupBy(fn ($order) => Carbon::parse($order->date_order)->format('Y-m-d'));

        $labels = [];
        $saleData = [];
        $draftData = [];
        $sentData = [];
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

            $saleData[] = $this->countOrdersByState($dailyOrders, OrderState::SALE->value);
            $draftData[] = $this->countOrdersByState($dailyOrders, OrderState::DRAFT->value);
            $sentData[] = $this->countOrdersByState($dailyOrders, OrderState::SENT->value);
            $cancelData[] = $this->countOrdersByState($dailyOrders, OrderState::CANCEL->value);
        }

        return [
            $labels,
            [
                [
                    'label'           => __('sales::filament/pages/sales-dashboard.widgets.sales-chart.confirmed-orders'),
                    'data'            => $saleData,
                    'borderColor'     => '#3b82f6',
                    'backgroundColor' => 'rgba(59,130,246,0.2)',
                ],
                [
                    'label'           => __('sales::filament/pages/sales-dashboard.widgets.sales-chart.draft-orders'),
                    'data'            => $draftData,
                    'borderColor'     => '#fbbf24',
                    'backgroundColor' => 'rgba(251,191,36,0.2)',
                ],
                [
                    'label'           => __('sales::filament/pages/sales-dashboard.widgets.sales-chart.sent-orders'),
                    'data'            => $sentData,
                    'borderColor'     => '#10b981',
                    'backgroundColor' => 'rgba(16,185,129,0.2)',
                ],
                [
                    'label'           => __('sales::filament/pages/sales-dashboard.widgets.sales-chart.cancelled-orders'),
                    'data'            => $cancelData,
                    'borderColor'     => '#ef4444',
                    'backgroundColor' => 'rgba(239,68,68,0.2)',
                ],
            ],
        ];
    }

    protected function countOrdersByState($orders, string $state): int
    {
        return $orders->where('state', $state)->count();
    }
}
