<?php

namespace Webkul\Sale\Filament\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Webkul\Sale\Enums\OrderState;
use Webkul\Sale\Models\Order;
use Webkul\Support\Models\Currency;

class StatsOverviewWidget extends BaseWidget
{
    use HasWidgetShield, InteractsWithPageFilters;

    protected static ?string $pollingInterval = '15s';

    protected function getHeading(): ?string
    {
        return __('sales::filament/pages/stats-overview.heading.title');
    }

    protected function getStats(): array
    {
        $filters = $this->filters;

        $baseQuery = Order::query();

        // 🔹 Apply filters
        if (! empty($filters['start_date'])) {
            $baseQuery->whereDate('date_order', '>=', $filters['start_date']);
        }

        if (! empty($filters['end_date'])) {
            $baseQuery->whereDate('date_order', '<=', $filters['end_date']);
        }

        if (! empty($filters['salesperson_id'])) {
            $baseQuery->where('user_id', $filters['salesperson_id']);
        }

        // 🔹 Calculate Stats
        $confirmedOrdersTotal = (clone $baseQuery)->where('state', OrderState::SENT)->sum('amount_total');
        $avgOrderValue = (clone $baseQuery)->where('state', OrderState::SENT)->avg('amount_total');
        $confirmedOrdersCount = (clone $baseQuery)->where('state', OrderState::SENT)->count();
        $draftOrdersCount = (clone $baseQuery)->where('state', OrderState::DRAFT)->count();
        $cancelledOrdersCount = (clone $baseQuery)->where('state', OrderState::CANCEL)->count();

        return [

            Stat::make(__('sales::filament/pages/stats-overview.title.confirm-order'), number_format($confirmedOrdersCount))
                ->description(__('sales::filament/pages/stats-overview.descriptions.confirm-order'))
                ->icon('heroicon-o-shopping-cart')
                ->color('primary'),

            Stat::make(__('sales::filament/pages/stats-overview.title.total-revenue'), $this->getActiveCurrency().' '.number_format($confirmedOrdersTotal, 2))
                ->description(__('sales::filament/pages/stats-overview.descriptions.total-revenue'))
                ->icon('heroicon-o-currency-dollar')
                ->color('success'),

            Stat::make(__('sales::filament/pages/stats-overview.title.average-order'), $this->getActiveCurrency().' '.number_format($avgOrderValue, 2))
                ->description(__('sales::filament/pages/stats-overview.descriptions.average-order'))
                ->icon('heroicon-o-chart-bar')
                ->color('info'),

            Stat::make(__('sales::filament/pages/stats-overview.title.cancel-order'), number_format($cancelledOrdersCount))
                ->description(__('sales::filament/pages/stats-overview.descriptions.cancel-order'))
                ->icon('heroicon-o-x-circle')
                ->color('danger'),

            Stat::make(__('sales::filament/pages/stats-overview.title.draft-order'), number_format($draftOrdersCount))
                ->description(__('sales::filament/pages/stats-overview.descriptions.draft-order'))
                ->icon('heroicon-o-document-text')
                ->color('warning'),
        ];
    }

    protected function getActiveCurrency()
    {
        return Currency::where('active', true)->value('symbol');
    }
}
