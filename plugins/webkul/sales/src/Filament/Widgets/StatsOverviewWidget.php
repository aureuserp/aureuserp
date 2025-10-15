<?php

namespace Webkul\Sale\Filament\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Carbon\Carbon;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Webkul\Sale\Enums\OrderState;
use Webkul\Sale\Models\Order;
use Webkul\Support\Models\Currency;

class StatsOverviewWidget extends BaseWidget
{
    use InteractsWithPageFilters, HasWidgetShield;

    protected function getHeading(): ?string
    {
        return __('sales::filament/pages/sales-dashboard.widgets.stats-overview.heading');
    }

    protected function getColumns(): int
    {
        return 4;
    }

    protected function getStats(): array
    {
        $query = $this->getFilteredQuery();
        $statsConfig = $this->getStatsConfig();
        $stats = [];

        foreach ($statsConfig as $config) {
            // If it's a revenue-type stat, handle differently
            if ($config['key'] === 'total_revenue') {
                $currentValue = $this->calculateTotalRevenue(clone $query);
                $previousValue = $this->calculateTotalRevenue(clone $query, previous: true);
            } elseif ($config['key'] === 'avg_revenue') {
                $currentValue = $this->calculateAverageRevenue(clone $query);
                $previousValue = $this->calculateAverageRevenue(clone $query, previous: true);
            } else {
                $currentValue = $this->countOrdersByState(clone $query, $config['state']->value);
                $previousValue = $this->countOrdersByState(clone $query, $config['state']->value, previous: true);
            }

            $trend = $this->calculateTrend($currentValue, $previousValue);

            $stats[] = Stat::make($config['title'], $this->formatNumber($currentValue, $config['key']))
                ->description($config['description'].' '.$trend['text'])
                ->descriptionIcon($trend['icon'])
                ->color($trend['color']);
        }

        return $stats;
    }

    protected function getFilteredQuery()
    {
        $filters = $this->filters;
        $query = Order::query();

        if (! empty($filters['start_date'])) {
            $query->whereDate('date_order', '>=', $filters['start_date']);
        }
        if (! empty($filters['end_date'])) {
            $query->whereDate('date_order', '<=', $filters['end_date']);
        }
        if (! empty($filters['country_id'])) {
            $query->whereHas('partner', fn ($q) => $q->whereIn('country_id', (array) $filters['country_id']));
        }
        if (! empty($filters['product_id'])) {
            $query->whereHas('orderLines', fn ($q) => $q->whereIn('product_id', (array) $filters['product_id']));
        }
        if (! empty($filters['customer_id'])) {
            $query->whereIn('partner_id', (array) $filters['customer_id']);
        }
        if (! empty($filters['category_id'])) {
            $query->whereHas('orderLines.product.category', fn ($q) => $q->whereIn('category_id', (array) $filters['category_id']));
        }
        if (! empty($filters['salesteam_id'])) {
            $query->whereIn('team_id', (array) $filters['salesteam_id']);
        }
        if (! empty($filters['salesperson_id'])) {
            $query->whereIn('user_id', (array) $filters['salesperson_id']);
        }

        return $query;
    }

    protected function getStatsConfig(): array
    {
        return [
            [
                'key'         => 'quotation',
                'state'       => OrderState::SENT,
                'title'       => __('sales::filament/pages/sales-dashboard.widgets.stats-overview.quotation'),
                'description' => __('sales::filament/pages/sales-dashboard.widgets.stats-overview.descriptions.quotation'),
                'color'       => 'primary',
            ],
            [
                'key'         => 'order',
                'state'       => OrderState::SALE,
                'title'       => __('sales::filament/pages/sales-dashboard.widgets.stats-overview.order'),
                'description' => __('sales::filament/pages/sales-dashboard.widgets.stats-overview.descriptions.order'),
                'color'       => 'success',
            ],
            [
                'key'         => 'draft',
                'state'       => OrderState::DRAFT,
                'title'       => __('sales::filament/pages/sales-dashboard.widgets.stats-overview.draft'),
                'description' => __('sales::filament/pages/sales-dashboard.widgets.stats-overview.descriptions.draft'),
                'color'       => 'info',
            ],
            [
                'key'         => 'cancel',
                'state'       => OrderState::CANCEL,
                'title'       => __('sales::filament/pages/sales-dashboard.widgets.stats-overview.cancel'),
                'description' => __('sales::filament/pages/sales-dashboard.widgets.stats-overview.descriptions.cancel'),
                'color'       => 'danger',
            ],
            [
                'key'         => 'total_revenue',
                'state'       => OrderState::SALE,
                'title'       => __('sales::filament/pages/sales-dashboard.widgets.stats-overview.total-revenue'),
                'description' => __('sales::filament/pages/sales-dashboard.widgets.stats-overview.descriptions.total-revenue'),
                'color'       => 'success',
            ],
            [
                'key'         => 'avg_revenue',
                'state'       => OrderState::SALE,
                'title'       => __('sales::filament/pages/sales-dashboard.widgets.stats-overview.avg-revenue'),
                'description' => __('sales::filament/pages/sales-dashboard.widgets.stats-overview.descriptions.avg-revenue'),
                'color'       => 'warning',
            ],
        ];
    }

    protected function countOrdersByState($query, string $state, bool $previous = false): int
    {
        $cloneQuery = $this->applyDateRange($query, $previous);

        return $cloneQuery->where('state', $state)->count() ?? 0;
    }

    protected function calculateTotalRevenue($query, bool $previous = false): float
    {
        $cloneQuery = $this->applyDateRange($query, $previous);

        return (float) $cloneQuery->where('state', OrderState::SALE)->sum('amount_total');
    }

    protected function calculateAverageRevenue($query, bool $previous = false): float
    {
        $cloneQuery = $this->applyDateRange($query, $previous);

        return (float) $cloneQuery->where('state', OrderState::SALE)->avg('amount_total');
    }

    protected function applyDateRange($query, bool $previous = false)
    {
        $filters = $this->filters;
        $start = ! empty($filters['start_date']) ? Carbon::parse($filters['start_date']) : now()->subMonth();
        $end = ! empty($filters['end_date']) ? Carbon::parse($filters['end_date']) : now();

        if ($previous) {
            $periodLength = $end->diffInDays($start) + 1;
            $prevEnd = $start->copy()->subDay();
            $prevStart = $prevEnd->copy()->subDays($periodLength - 1);
            $query->whereBetween('date_order', [$prevStart->startOfDay(), $prevEnd->endOfDay()]);
        } else {
            $query->whereBetween('date_order', [$start->startOfDay(), $end->endOfDay()]);
        }

        return $query;
    }

    protected function calculateTrend(float|int $current, float|int $previous): array
    {
        if ($previous == 0 && $current == 0) {
            return ['text' => '—', 'icon' => null, 'color' => 'gray'];
        }

        if ($previous == 0 && $current > 0) {
            return ['text' => '↑ 100%', 'icon' => 'heroicon-m-arrow-trending-up', 'color' => 'success'];
        }

        $percentage = round((($current - $previous) / $previous) * 100, 1);

        if ($percentage > 0) {
            return ['text' => "↑ {$percentage}%", 'icon' => 'heroicon-m-arrow-trending-up', 'color' => 'success'];
        } elseif ($percentage < 0) {
            return ['text' => '↓ '.abs($percentage).'%', 'icon' => 'heroicon-m-arrow-trending-down', 'color' => 'danger'];
        }

        return ['text' => '0%', 'icon' => null, 'color' => 'gray'];
    }

    protected function formatNumber(float|int $value, string $key): string
    {
        if (in_array($key, ['total_revenue', 'avg_revenue'])) {
            return self::getActiveCurrencySymbol().' '.number_format($value, 2);
        }

        return number_format($value);
    }

    protected static function getActiveCurrencySymbol(): string
    {
        return Currency::where('active', 1)->first()->symbol;
    }
}
