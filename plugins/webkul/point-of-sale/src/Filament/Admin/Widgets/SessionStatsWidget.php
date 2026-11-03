<?php

namespace Webkul\PointOfSale\Filament\Admin\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Webkul\PointOfSale\Enums\OrderState;
use Webkul\PointOfSale\Enums\SessionState;
use Webkul\PointOfSale\Filament\Admin\Widgets\Concerns\FiltersDashboardOrders;
use Webkul\PointOfSale\Models\Order;
use Webkul\PointOfSale\Models\Session;
use Webkul\Support\Filament\Widgets\DashboardStatsWidget;

class SessionStatsWidget extends DashboardStatsWidget
{
    use FiltersDashboardOrders, HasWidgetShield;

    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        [$start, $end] = $this->currentOrderRange();

        [$previousStart, $previousEnd] = $this->previousOrderRange();

        $settled = [OrderState::PAID->value, OrderState::DONE->value, OrderState::INVOICED->value];

        $currentSettled = $this->ordersInWindow($start, $end)->whereIn('state', $settled);

        $previousSettled = $this->ordersInWindow($previousStart, $previousEnd)->whereIn('state', $settled);

        $openSessions = (float) Session::query()
            ->whereIn('state', [
                SessionState::OPENING_CONTROL,
                SessionState::OPENED,
                SessionState::CLOSING_CONTROL,
            ])
            ->when($this->getFilterIds('selectedTerminals'), fn (Builder $query, array $ids) => $query->whereIn('config_id', $ids))
            ->count();

        $orders = (float) $currentSettled->clone()->count();

        $previousOrders = (float) $previousSettled->clone()->count();

        $revenue = (float) $currentSettled->clone()->sum('amount_total');

        $previousRevenue = (float) $previousSettled->clone()->sum('amount_total');

        $failed = (float) $this->ordersInWindow($start, $end)->where('has_failed_operation', true)->count();

        $previousFailed = (float) $this->ordersInWindow($previousStart, $previousEnd)->where('has_failed_operation', true)->count();

        return [
            $this->themedStat(
                __('point-of-sale::filament/admin/widgets/session-stats.stats.open-sessions.label'),
                'heroicon-o-play-circle',
                'success',
                number_format($openSessions),
                $openSessions,
                $openSessions,
                $this->sessionSeries($start, $end),
            ),
            $this->themedStat(
                __('point-of-sale::filament/admin/widgets/session-stats.stats.orders.label'),
                'heroicon-o-receipt-percent',
                'primary',
                number_format($orders),
                $orders,
                $previousOrders,
                $this->orderSeries($start, $end, $settled, 'count'),
            ),
            $this->themedStat(
                __('point-of-sale::filament/admin/widgets/session-stats.stats.revenue.label'),
                'heroicon-o-banknotes',
                'info',
                compact_money($revenue, $this->dashboardCurrency()),
                $revenue,
                $previousRevenue,
                $this->orderSeries($start, $end, $settled, 'sum', 'amount_total'),
            ),
            $this->themedStat(
                __('point-of-sale::filament/admin/widgets/session-stats.stats.failed-operations.label'),
                'heroicon-o-exclamation-triangle',
                'danger',
                number_format($failed),
                $failed,
                $previousFailed,
                $this->failedSeries($start, $end),
            ),
        ];
    }

    protected function ordersInWindow(Carbon $start, Carbon $end): Builder
    {
        return $this->applyNonDateOrderFilters(Order::query())
            ->where('pos_orders.ordered_at', '>=', $start)
            ->where('pos_orders.ordered_at', '<=', $end);
    }

    /**
     * @param  list<string>  $states
     * @return list<float>
     */
    protected function orderSeries(Carbon $start, Carbon $end, array $states, string $aggregate, ?string $column = null): array
    {
        $query = $this->applyNonDateOrderFilters(Order::query())->whereIn('pos_orders.state', $states);

        $trend = Trend::query($query)
            ->dateColumn('ordered_at')
            ->between(start: $start, end: $end)
            ->perDay();

        $result = $aggregate === 'sum' ? $trend->sum($column) : $trend->count();

        return $result->map(fn (TrendValue $value): float => round((float) $value->aggregate, 2))->all();
    }

    /**
     * @return list<int>
     */
    protected function failedSeries(Carbon $start, Carbon $end): array
    {
        $query = $this->applyNonDateOrderFilters(Order::query())->where('pos_orders.has_failed_operation', true);

        return Trend::query($query)
            ->dateColumn('ordered_at')
            ->between(start: $start, end: $end)
            ->perDay()
            ->count()
            ->map(fn (TrendValue $value): int => (int) $value->aggregate)
            ->all();
    }

    /**
     * @return list<int>
     */
    protected function sessionSeries(Carbon $start, Carbon $end): array
    {
        $query = Session::query()
            ->when($this->getFilterIds('selectedTerminals'), fn (Builder $query, array $ids) => $query->whereIn('config_id', $ids));

        return Trend::query($query)
            ->dateColumn('started_at')
            ->between(start: $start, end: $end)
            ->perDay()
            ->count()
            ->map(fn (TrendValue $value): int => (int) $value->aggregate)
            ->all();
    }
}
