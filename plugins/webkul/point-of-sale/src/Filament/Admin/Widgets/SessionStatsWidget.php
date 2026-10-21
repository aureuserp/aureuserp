<?php

namespace Webkul\PointOfSale\Filament\Admin\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;
use Webkul\PointOfSale\Enums\OrderState;
use Webkul\PointOfSale\Enums\SessionState;
use Webkul\PointOfSale\Filament\Admin\Widgets\Concerns\FiltersDashboardOrders;
use Webkul\PointOfSale\Models\Order;
use Webkul\PointOfSale\Models\Session;

class SessionStatsWidget extends BaseWidget
{
    use FiltersDashboardOrders, HasWidgetShield;

    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $settledOrders = $this->getOrdersQuery()
            ->whereIn('state', [OrderState::PAID, OrderState::DONE, OrderState::INVOICED]);

        return [
            Stat::make(
                __('point-of-sale::filament/admin/widgets/session-stats.stats.open-sessions.label'),
                Session::query()
                    ->whereIn('state', [
                        SessionState::OPENING_CONTROL,
                        SessionState::OPENED,
                        SessionState::CLOSING_CONTROL,
                    ])
                    ->when($this->getFilterIds('selectedTerminals'), fn (Builder $query, array $ids) => $query->whereIn('config_id', $ids))
                    ->count(),
            )
                ->description(__('point-of-sale::filament/admin/widgets/session-stats.stats.open-sessions.description'))
                ->icon('heroicon-o-play-circle')
                ->color('success'),

            Stat::make(
                __('point-of-sale::filament/admin/widgets/session-stats.stats.orders.label'),
                $settledOrders->clone()->count(),
            )
                ->description(__('point-of-sale::filament/admin/widgets/session-stats.stats.orders.description'))
                ->icon('heroicon-o-receipt-percent')
                ->color('primary'),

            Stat::make(
                __('point-of-sale::filament/admin/widgets/session-stats.stats.revenue.label'),
                number_format((float) $settledOrders->clone()->sum('amount_total'), 2),
            )
                ->description(__('point-of-sale::filament/admin/widgets/session-stats.stats.revenue.description'))
                ->icon('heroicon-o-banknotes')
                ->color('info'),

            Stat::make(
                __('point-of-sale::filament/admin/widgets/session-stats.stats.failed-operations.label'),
                $this->getOrdersQuery()->where('has_failed_operation', true)->count(),
            )
                ->description(__('point-of-sale::filament/admin/widgets/session-stats.stats.failed-operations.description'))
                ->icon('heroicon-o-exclamation-triangle')
                ->color('danger'),
        ];
    }

    protected function getOrdersQuery(): Builder
    {
        return $this->applyOrderFilters(Order::query());
    }
}
