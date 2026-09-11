<?php

namespace Webkul\Purchase\Filament\Admin\Widgets;

use Carbon\Carbon;
use DateInterval;
use DatePeriod;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Collection;
use Webkul\Purchase\Models\PurchaseOrder;

class PurchaseTrendWidget extends ChartWidget
{
    use InteractsWithPageFilters;

    protected ?string $heading = 'Purchase Orders Trend by State';

    protected static bool $isLazy = false;

    protected ?string $maxHeight = '320px';

    protected function getType(): string
    {
        return 'line';
    }

    public function getColumnSpan(): int|string
    {
        return 'full';
    }

    protected function getData(): array
    {
        [$startDate, $endDate] = $this->getDateRange();

        $orders = $this->getFilteredQuery()
            ->whereBetween('ordered_at', [$startDate, $endDate])
            ->get(['ordered_at', 'state']);

        [$labels, $datasets] = $this->buildChartDataset($orders, $startDate, $endDate);

        return compact('labels', 'datasets');
    }

    protected function getDateRange(): array
    {
        $filters = $this->filters ?? [];

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
        $filters = $this->filters ?? [];

        $query = PurchaseOrder::query();

        $query->when(! empty($filters['start_date']), fn ($query) => $query->whereDate('ordered_at', '>=', $filters['start_date']));
        $query->when(! empty($filters['end_date']), fn ($query) => $query->whereDate('ordered_at', '<=', $filters['end_date']));
        $query->when(! empty($filters['country_id']), fn ($query) => $query->whereHas('partner', fn ($partnerQuery) => $partnerQuery->whereIn('country_id', (array) $filters['country_id'])));
        $query->when(! empty($filters['product_id']), fn ($query) => $query->whereHas('lines', fn ($lineQuery) => $lineQuery->whereIn('product_id', (array) $filters['product_id'])));
        $query->when(! empty($filters['partner_id']), fn ($query) => $query->whereIn('partner_id', (array) $filters['partner_id']));
        $query->when(! empty($filters['category_id']), fn ($query) => $query->whereHas('lines.product', fn ($productQuery) => $productQuery->whereIn('category_id', (array) $filters['category_id'])));
        $query->when(! empty($filters['buyer_id']), fn ($query) => $query->whereIn('user_id', (array) $filters['buyer_id']));
        $query->when(! empty($filters['state']), fn ($query) => $query->whereIn('state', (array) $filters['state']));

        return $query;
    }

    protected function buildChartDataset(Collection $orders, Carbon $startDate, Carbon $endDate): array
    {
        $ordersByDay = $orders->groupBy(fn ($order) => Carbon::parse($order->ordered_at)->format('Y-m-d'));

        $labels = [];
        $draft = [];
        $sent = [];
        $purchase = [];
        $done = [];
        $canceled = [];

        $period = new DatePeriod($startDate, new DateInterval('P1D'), $endDate->copy()->addDay());

        foreach ($period as $date) {
            $dateKey = $date->format('Y-m-d');
            $labels[] = $date->format('M d');
            $dailyOrders = $ordersByDay[$dateKey] ?? collect();

            $draft[] = $dailyOrders->where('state', 'draft')->count();
            $sent[] = $dailyOrders->where('state', 'sent')->count();
            $purchase[] = $dailyOrders->where('state', 'purchase')->count();
            $done[] = $dailyOrders->where('state', 'done')->count();
            $canceled[] = $dailyOrders->where('state', 'canceled')->count();
        }

        return [
            $labels,
            [
                ['label' => 'Draft', 'data' => $draft, 'borderColor' => '#6b7280', 'backgroundColor' => 'rgba(107,114,128,0.2)'],
                ['label' => 'Sent', 'data' => $sent, 'borderColor' => '#f59e0b', 'backgroundColor' => 'rgba(245,158,11,0.2)'],
                ['label' => 'Purchase', 'data' => $purchase, 'borderColor' => '#3b82f6', 'backgroundColor' => 'rgba(59,130,246,0.2)'],
                ['label' => 'Done', 'data' => $done, 'borderColor' => '#10b981', 'backgroundColor' => 'rgba(16,185,129,0.2)'],
                ['label' => 'Canceled', 'data' => $canceled, 'borderColor' => '#ef4444', 'backgroundColor' => 'rgba(239,68,68,0.2)'],
            ],
        ];
    }
}
