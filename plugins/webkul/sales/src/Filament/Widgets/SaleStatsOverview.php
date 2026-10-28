<?php

namespace Webkul\Sale\Filament\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Webkul\Sale\Filament\Clusters\Orders\Resources\OrderResource;
use Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationResource;
use Webkul\Sale\Filament\Widgets\Concerns\HasSaleDashboardFilters;
use Webkul\Support\Filament\Widgets\DashboardStatsWidget;

class SaleStatsOverview extends DashboardStatsWidget
{
    use HasSaleDashboardFilters, HasWidgetShield;

    protected static ?int $sort = 1;

    public function getHeading(): ?string
    {
        return __('sales::filament/widgets/sales-dashboard.stats.heading');
    }

    protected function getStats(): array
    {
        [$start, $end] = $this->periodRange();

        [$previousStart, $previousEnd] = $this->previousPeriodRange();

        $currency = current_company()?->currency?->name;

        $quotations = $this->quotations()->count();
        $previousQuotations = $this->quotations($previousStart, $previousEnd)->count();

        $orders = $this->saleOrders()->count();
        $previousOrders = $this->saleOrders($previousStart, $previousEnd)->count();

        $ccExpr = 'SUM(CASE WHEN currency_rate > 0 THEN amount_total / currency_rate ELSE amount_total END) as cc_total';

        $revenue = (float) ($this->saleOrders()->selectRaw($ccExpr)->value('cc_total') ?? 0.0);
        $previousRevenue = (float) ($this->saleOrders($previousStart, $previousEnd)->selectRaw($ccExpr)->value('cc_total') ?? 0.0);

        $average = $orders > 0 ? $revenue / $orders : 0;
        $previousAverage = $previousOrders > 0 ? $previousRevenue / $previousOrders : 0;

        $revenueColumn = 'CASE WHEN currency_rate > 0 THEN amount_total / currency_rate ELSE amount_total END';

        $orderCountSeries = $this->trendData($this->saleOrders(), 'COUNT', '*', $start, $end);

        $revenueSeries = $this->trendData($this->saleOrders(), 'SUM', $revenueColumn, $start, $end);

        $averageSeries = array_map(
            fn (float $revenue, float $count): float => $count > 0 ? round($revenue / $count, 2) : 0.0,
            $revenueSeries,
            $orderCountSeries,
        );

        $quotationsUrl = QuotationResource::getUrl('index', ['activeTableView' => 'quotations']);

        $ordersUrl = OrderResource::getUrl('index');

        return [
            $this->themedStat(
                __('sales::filament/widgets/sales-dashboard.stats.total-quotations'),
                'heroicon-o-document-text',
                'info',
                number_format($quotations),
                $quotations,
                $previousQuotations,
                $this->trendData($this->quotations(), 'COUNT', '*', $start, $end),
            )->url($quotationsUrl),
            $this->themedStat(
                __('sales::filament/widgets/sales-dashboard.stats.total-sales-orders'),
                'heroicon-o-shopping-cart',
                'primary',
                number_format($orders),
                $orders,
                $previousOrders,
                $orderCountSeries,
            )->url($ordersUrl),
            $this->themedStat(
                __('sales::filament/widgets/sales-dashboard.stats.total-revenue'),
                'heroicon-o-banknotes',
                'success',
                compact_money($revenue, $currency),
                $revenue,
                $previousRevenue,
                $revenueSeries,
                money($revenue, $currency),
            )->url($ordersUrl),
            $this->themedStat(
                __('sales::filament/widgets/sales-dashboard.stats.average-revenue'),
                'heroicon-o-chart-bar',
                'warning',
                compact_money($average, $currency),
                $average,
                $previousAverage,
                $averageSeries,
                money($average, $currency),
            )->url($ordersUrl),
        ];
    }

    /**
     * @return array<int, float>
     */
    protected function trendData(Builder $query, string $aggregate, string $column, Carbon $start, Carbon $end): array
    {
        return Trend::query($query)
            ->dateColumn('date_order')
            ->between(start: $start, end: $end)
            ->perDay()
            ->aggregate($column, $aggregate)
            ->map(fn (TrendValue $value) => round((float) $value->aggregate, 2))
            ->toArray();
    }
}
