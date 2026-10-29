<?php

namespace Webkul\Purchase\Filament\Admin\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Webkul\Purchase\Filament\Admin\Widgets\Concerns\HasPurchaseDashboardFilters;
use Webkul\Support\Filament\Widgets\DashboardStatsWidget;

class PurchaseStatsWidget extends DashboardStatsWidget
{
    use HasPurchaseDashboardFilters, HasWidgetShield;

    protected static ?int $sort = 1;

    public function getHeading(): ?string
    {
        return __('purchases::filament/admin/widgets/purchase-dashboard.stats.heading');
    }

    protected function getStats(): array
    {
        $confirmed = ['purchase', 'done'];

        return [
            $this->buildStat(
                __('purchases::filament/admin/widgets/purchase-dashboard.stats.draft-rfqs'),
                'heroicon-o-document-text',
                'warning',
                ['draft'],
            ),
            $this->buildStat(
                __('purchases::filament/admin/widgets/purchase-dashboard.stats.sent-rfqs'),
                'heroicon-o-paper-airplane',
                'info',
                ['sent'],
            ),
            $this->buildStat(
                __('purchases::filament/admin/widgets/purchase-dashboard.stats.confirmed-orders'),
                'heroicon-o-check-circle',
                'success',
                $confirmed,
            ),
            $this->buildStat(
                __('purchases::filament/admin/widgets/purchase-dashboard.stats.total-purchase-value'),
                'heroicon-o-currency-dollar',
                'primary',
                $confirmed,
                money: true,
            ),
        ];
    }

    /**
     * @param  list<string>  $states
     */
    protected function buildStat(string $label, string $icon, string $color, array $states, bool $money = false): Stat
    {
        [$previousStart, $previousEnd] = $this->previousPeriodRange();

        $current = $money ? $this->sumByState($states) : $this->countByState($states);

        $previous = $money
            ? $this->sumByState($states, $previousStart, $previousEnd)
            : $this->countByState($states, $previousStart, $previousEnd);

        $value = $money ? compact_money($current, $this->dashboardCurrency()) : number_format($current);

        $series = $this->trendSeries($states, $money ? 'sum' : 'count', $money ? 'total_cc_amount' : null);

        return $this->themedStat($label, $icon, $color, $value, $current, $previous, $series);
    }

    /**
     * @param  list<string>  $states
     * @return list<float>
     */
    protected function trendSeries(array $states, string $aggregate, ?string $column = null): array
    {
        [$start, $end] = $this->periodRange();

        $query = $this->scopedToStates($states, $start, $end);

        $trend = Trend::query($query)
            ->dateColumn('ordered_at')
            ->between(start: $start, end: $end)
            ->perDay();

        $result = $aggregate === 'sum' ? $trend->sum($column) : $trend->count();

        return $result->map(fn (TrendValue $value): float => round((float) $value->aggregate, 2))->all();
    }

    protected function countByState(array $states, ?Carbon $start = null, ?Carbon $end = null): float
    {
        return (float) $this->scopedToStates($states, $start, $end)->count();
    }

    protected function sumByState(array $states, ?Carbon $start = null, ?Carbon $end = null): float
    {
        return (float) $this->scopedToStates($states, $start, $end)->sum('total_cc_amount');
    }

    protected function scopedToStates(array $states, ?Carbon $start, ?Carbon $end): Builder
    {
        $query = $this->purchaseOrders($start, $end);

        return $query->whereIn($query->qualifyColumn('state'), $states);
    }
}
