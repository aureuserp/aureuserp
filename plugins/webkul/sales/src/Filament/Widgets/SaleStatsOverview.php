<?php

namespace Webkul\Sale\Filament\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Webkul\Sale\Filament\Widgets\Concerns\HasSaleDashboardFilters;

class SaleStatsOverview extends BaseWidget
{
    use HasSaleDashboardFilters, HasWidgetShield;

    protected static ?int $sort = 1;

    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 'full';

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

        $revenue = (float) $this->saleOrders()->sum('amount_total');
        $previousRevenue = (float) $this->saleOrders($previousStart, $previousEnd)->sum('amount_total');

        $averageRevenue = $orders > 0 ? $revenue / $orders : 0;
        $previousAverageRevenue = $previousOrders > 0 ? $previousRevenue / $previousOrders : 0;

        return [
            $this->makeStat(
                __('sales::filament/widgets/sales-dashboard.stats.total-quotations'),
                $quotations,
                $quotations,
                $previousQuotations,
                $this->trendData($this->quotations(), 'COUNT', '*', $start, $end),
            ),
            $this->makeStat(
                __('sales::filament/widgets/sales-dashboard.stats.total-sales-orders'),
                $orders,
                $orders,
                $previousOrders,
                $this->trendData($this->saleOrders(), 'COUNT', '*', $start, $end),
            ),
            $this->makeStat(
                __('sales::filament/widgets/sales-dashboard.stats.total-revenue'),
                money($revenue, $currency),
                $revenue,
                $previousRevenue,
                $this->trendData($this->saleOrders(), 'SUM', 'amount_total', $start, $end),
            ),
            $this->makeStat(
                __('sales::filament/widgets/sales-dashboard.stats.average-revenue'),
                money($averageRevenue, $currency),
                $averageRevenue,
                $previousAverageRevenue,
                [],
            ),
        ];
    }

    /**
     * @param  array<int, float>  $chart
     */
    protected function makeStat(string $label, mixed $display, float $current, float $previous, array $chart): Stat
    {
        [$percentage, $trend] = $this->change($current, $previous);

        $stat = Stat::make($label, $display)
            ->description($percentage.'% '.($trend === 'success'
                ? __('sales::filament/widgets/sales-dashboard.stats.increase')
                : __('sales::filament/widgets/sales-dashboard.stats.decrease')))
            ->descriptionIcon($trend === 'success' ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
            ->color($trend);

        return $chart === [] ? $stat : $stat->chart($chart);
    }

    /**
     * @return array{0: float, 1: string}
     */
    protected function change(float $current, float $previous): array
    {
        if ($previous == 0.0) {
            return [$current > 0 ? 100.0 : 0.0, $current < 0 ? 'danger' : 'success'];
        }

        $change = (($current - $previous) / abs($previous)) * 100;

        return [abs(round($change, 1)), $change >= 0 ? 'success' : 'danger'];
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
