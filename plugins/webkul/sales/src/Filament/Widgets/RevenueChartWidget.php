<?php

namespace Webkul\Sale\Filament\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Carbon\Carbon;
use DateInterval;
use DatePeriod;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Webkul\Sale\Enums\OrderState;
use Webkul\Sale\Models\Order;

class RevenueChartWidget extends ChartWidget
{
    use HasWidgetShield, InteractsWithPageFilters;

    protected int|string|array $columnSpan = 'half';

    protected ?string $maxHeight = '400px';

    /**
     * Build and return the chart dataset.
     */
    protected function getData(): array
    {
        $query = $this->buildFilteredQuery()
            ->where('state', OrderState::SALE);

        $orders = $query->get(['date_order', 'amount_total']);

        [$startDate, $endDate] = $this->getQueryDateRange($query);
        [$labels, $datasets] = $this->buildChartDataset($orders, $startDate, $endDate);

        return compact('labels', 'datasets');
    }

    protected function getType(): string
    {
        return 'line';
    }

    /**
     * Builds the Order query applying all filter conditions, including date range.
     */
    protected function buildFilteredQuery(): Builder
    {
        $filters = $this->filters ?? [];

        $startDate = ! empty($filters['start_date'])
            ? Carbon::parse($filters['start_date'])->startOfDay()
            : now()->subMonth()->startOfDay();

        $endDate = ! empty($filters['end_date'])
            ? Carbon::parse($filters['end_date'])->endOfDay()
            : now()->endOfDay();

        $query = Order::query()
            ->when($startDate && $endDate, fn (Builder $q) => $q->whereBetween('date_order', [$startDate, $endDate])
            )
            ->when(! empty($filters['country_id']), function (Builder $q) use ($filters) {
                $q->whereHas('partner', fn (Builder $sub) => $sub->whereIn('country_id', (array) $filters['country_id'])
                );
            })
            ->when(! empty($filters['product_id']), function (Builder $q) use ($filters) {
                $q->whereHas('orderLines', fn (Builder $sub) => $sub->whereIn('product_id', (array) $filters['product_id'])
                );
            })
            ->when(! empty($filters['customer_id']), fn (Builder $q) => $q->whereIn('partner_id', (array) $filters['customer_id'])
            )
            ->when(! empty($filters['category_id']), function (Builder $q) use ($filters) {
                $q->whereHas('orderLines.product.category', fn (Builder $sub) => $sub->whereIn('category_id', (array) $filters['category_id'])
                );
            })
            ->when(! empty($filters['salesteam_id']), fn (Builder $q) => $q->whereIn('team_id', (array) $filters['salesteam_id'])
            )
            ->when(! empty($filters['salesperson_id']), fn (Builder $q) => $q->whereIn('user_id', (array) $filters['salesperson_id'])
            );

        $query->startDate = $startDate;
        $query->endDate = $endDate;

        return $query;
    }

    /**
     * Extracts date range info used in the query.
     */
    protected function getQueryDateRange(Builder $query): array
    {
        return [
            $query->startDate ?? now()->subMonth()->startOfDay(),
            $query->endDate ?? now()->endOfDay(),
        ];
    }

    /**
     * Transform orders into chart-ready format.
     */
    protected function buildChartDataset(Collection $orders, Carbon $startDate, Carbon $endDate): array
    {
        $ordersByDay = $orders->groupBy(fn ($order) => Carbon::parse($order->date_order)->format('Y-m-d'));

        $labels = [];
        $revenuePerDay = [];

        $period = new DatePeriod($startDate, new DateInterval('P1D'), $endDate->copy()->addDay());

        foreach ($period as $date) {
            $dateKey = $date->format('Y-m-d');
            $labels[] = $dateKey;

            $dailyOrders = $ordersByDay[$dateKey] ?? collect();
            $revenuePerDay[] = $dailyOrders->sum('amount_total');
        }

        $datasets = [
            [
                'label'           => __('sales::filament/pages/sales-dashboard.widgets.revenue-chart.label'),
                'data'            => $revenuePerDay,
                'borderColor'     => '#1E851E',
                'backgroundColor' => 'rgba(59,130,246,0.2)',
                'fill'            => true,
                'tension'         => 0.3,
                'pointRadius'     => 3,
                'pointHoverRadius'=> 5,
            ],
        ];

        return [$labels, $datasets];
    }

    public function getHeading(): ?string
    {
        return __('sales::filament/pages/sales-dashboard.widgets.revenue-chart.heading');
    }
}
