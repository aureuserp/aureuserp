<?php

namespace Webkul\Sale\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Webkul\Sale\Filament\Widgets\Concerns\HasSaleDashboardFilters;

class SaleStatsOverview extends BaseWidget
{
    use HasSaleDashboardFilters;

    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = 'full';

    public function getHeading(): ?string
    {
        return __('sales::filament/widgets/sales-dashboard.stats.heading');
    }

    protected function getStats(): array
    {
        $totalQuotations = $this->quotations()->count();

        $totalSalesOrders = $this->saleOrders()->count();

        $totalRevenue = $this->saleOrders()->sum('amount_total');

        $averageRevenue = $totalSalesOrders > 0 ? $totalRevenue / $totalSalesOrders : 0;

        return [
            Stat::make(__('sales::filament/widgets/sales-dashboard.stats.total-quotations'), $totalQuotations),
            Stat::make(__('sales::filament/widgets/sales-dashboard.stats.total-sales-orders'), $totalSalesOrders),
            Stat::make(__('sales::filament/widgets/sales-dashboard.stats.total-revenue'), money($totalRevenue)),
            Stat::make(__('sales::filament/widgets/sales-dashboard.stats.average-revenue'), money($averageRevenue)),
        ];
    }
}
