<?php

namespace Webkul\Sale\Filament\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Carbon\Carbon;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Webkul\Sale\Models\Order;

class StatsOverviewWidget extends BaseWidget
{
    use HasWidgetShield, InteractsWithPageFilters;

    protected static ?string $pollingInterval = '15s';

    protected function getHeading(): ?string
    {
        return __('sales::filament/pages/stats-overview.heading.title');
    }

    protected function getData(): array
    {
        // 🔹 Extract filters
        $startDate = filled($this->filters['startDate'] ?? null)
            ? Carbon::parse($this->filters['startDate'])
            : null;

        $endDate = filled($this->filters['endDate'] ?? null)
            ? Carbon::parse($this->filters['endDate'])
            : null;

        // 🔹 Get only confirmed orders
        $ordersQuery = Order::query()->where('state', 'sale');

        if ($startDate && $endDate) {
            $ordersQuery->whereBetween('created_at', [$startDate, $endDate]);
        } elseif ($startDate) {
            $ordersQuery->where('created_at', '>=', $startDate);
        } elseif ($endDate) {
            $ordersQuery->where('created_at', '<=', $endDate);
        }

        $totalOrders = $ordersQuery->count();

        $totalRevenue = (float) $ordersQuery->sum('amount_total');
        $avgOrderValue = $totalOrders > 0
            ? round($totalRevenue / $totalOrders, 2)
            : 0;

        return [
            Stat::make('Total Orders', $totalOrders)
                ->description('Confirmed sales orders')
                ->icon('heroicon-o-shopping-cart')
                ->color('primary'),

            Stat::make('Total Revenue', number_format($totalRevenue, 2))
                ->description('Total revenue from confirmed orders')
                ->icon('heroicon-o-currency-dollar')
                ->color('success'),

            Stat::make('Avg. Order Value', number_format($avgOrderValue, 2))
                ->description('Average revenue per order')
                ->icon('heroicon-o-chart-bar')
                ->color('info'),
        ];
    }
}
