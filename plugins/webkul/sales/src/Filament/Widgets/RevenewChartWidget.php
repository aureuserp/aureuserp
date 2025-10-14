<?php

namespace Webkul\Sale\Filament\Widgets;

use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Webkul\Sale\Enums\OrderState;
use Webkul\Sale\Models\Order;

class RevenewChartWidget extends ChartWidget
{
    use InteractsWithPageFilters;

    protected int|string|array $columnSpan = 'full';

    protected ?string $maxHeight = '400px';

    public function getHeading(): ?string
    {
        return 'Revenue Trends';
    }

    protected function getData(): array
    {
        [$startDate, $endDate] = $this->getDateRange();

        $orders = $this->getFilteredQuery()
            ->where('state', OrderState::SALE)
            ->whereBetween('date_order', [$startDate, $endDate])
            ->get(['date_order', 'amount_total']);

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

    protected function getFilteredQuery()
    {
        $filters = $this->filters;

        $query = Order::query();

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
        $revenueData = [];

        $period = new \DatePeriod(
            $startDate,
            new \DateInterval('P1D'),
            $endDate->copy()->addDay()
        );

        foreach ($period as $dt) {
            $dateKey = $dt->format('Y-m-d');
            $labels[] = $dateKey;

            $dailyOrders = $ordersByDay[$dateKey] ?? collect();
            $revenueData[] = $dailyOrders->sum('amount_total');
        }

        return [
            $labels,
            [
                [
                    'label'           => 'Revenue',
                    'data'            => $revenueData,
                    'borderColor'     => '#1e851eff',
                    'backgroundColor' => 'rgba(59,130,246,0.2)',
                    'fill'            => true,
                    'tension'         => 0.3,
                ],
            ],
        ];
    }
}
