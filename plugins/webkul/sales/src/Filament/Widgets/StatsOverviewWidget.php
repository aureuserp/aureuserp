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

    protected function getStats(): array
    {
        $query = Order::query();

        $startDate = filled($this->filters['startDate'] ?? null)
            ? Carbon::parse($this->filters['startDate'])
            : null;

        $endDate = filled($this->filters['endDate'] ?? null)
            ? Carbon::parse($this->filters['endDate'])
            : null;

        $totalOrder = $this->getTotalOrder($query, $startDate, $endDate);
        $totalRevenue = $this->getTotalRevenue($query, $startDate, $endDate);

        $avgOrderValue = $totalOrder > 0
            ? round($totalRevenue / $totalOrder, 2)
            : 0;

        return [
            Stat::make('Total Orders', $totalOrder)
                ->description('Total number of sales orders'),

            Stat::make('Total Revenue', number_format($totalRevenue, 2))
                ->description('Total sales revenue'),

            Stat::make('Avg. Order Value', number_format($avgOrderValue, 2))
                ->description('Average value per order'),
        ];
    }

    protected function getTotalOrder($query, $startDate, $endDate): int
    {
        $q = (clone $query)->where('state', 'sale');

        if ($startDate && $endDate) {
            $q->whereBetween('created_at', [$startDate, $endDate]);
        } elseif ($startDate) {
            $q->where('created_at', '>=', $startDate);
        } elseif ($endDate) {
            $q->where('created_at', '<=', $endDate);
        }

        return $q->count();
    }

    protected function getTotalRevenue($query, $startDate, $endDate): float
    {
        $q = (clone $query)->where('state', 'sale');

        if ($startDate && $endDate) {
            $q->whereBetween('created_at', [$startDate, $endDate]);
        } elseif ($startDate) {
            $q->where('created_at', '>=', $startDate);
        } elseif ($endDate) {
            $q->where('created_at', '<=', $endDate);
        }

        return (float) $q->sum('amount_total'); // Adjust column name if needed
    }
}
