<?php

namespace Webkul\Sale\Filament\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Carbon\Carbon;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Webkul\Sale\Enums\InvoiceStatus;
use Webkul\Sale\Enums\OrderState;
use Webkul\Sale\Models\Order;
use Webkul\Support\Models\Currency;

class StatsOverviewWidget extends BaseWidget
{
    use HasWidgetShield, InteractsWithPageFilters;

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
            if ($config['key'] === 'total_revenue') {
                $currentValue = $this->calculateTotalRevenue(clone $query);
                $previousValue = $this->calculateTotalRevenue(clone $query, previous: true);
            } elseif ($config['key'] === 'avg_revenue') {
                $currentValue = $this->calculateAverageRevenue(clone $query);
                $previousValue = $this->calculateAverageRevenue(clone $query, previous: true);
            } elseif ($config['key'] === 'fully_invoiced') {
                $currentValue = $this->countFullyInvoicedOrder(clone $query);
                $previousValue = $this->countFullyInvoicedOrder(clone $query, previous: true);
            } elseif ($config['key'] === 'archived') {
                $currentValue = $this->countArchivedOrders(clone $query);
                $previousValue = $this->countArchivedOrders(clone $query, previous: true);
            } else {
                $currentValue = $this->countOrdersByState(clone $query, $config['state']->value);
                $previousValue = $this->countOrdersByState(clone $query, $config['state']->value, previous: true);
            }

            $trend = $this->calculateTrend($currentValue, $previousValue);
            $chartData = $this->getChartData(clone $query, $config);

            $stat = Stat::make($config['title'], $this->formatNumber($currentValue, $config['key']))
                ->description($trend['description'])
                ->descriptionIcon($trend['icon'])
                ->color($trend['color']);

            if (! empty($chartData)) {
                $stat->chart($chartData);
            }

            $stats[] = $stat;
        }

        return $stats;
    }

    protected function getFilteredQuery()
    {
        $filters = $this->filters ?? [];
        $query = Order::query();

        $query->when(! empty($filters['start_date']), function ($query) use ($filters) {
            $query->whereDate('date_order', '>=', $filters['start_date']);
        });
        $query->when(! empty($filters['end_date']), function ($query) use ($filters) {
            $query->whereDate('date_order', '<=', $filters['end_date']);
        });

        $query->when(! empty($filters['country_id']), function ($query) use ($filters) {
            $query->whereHas('partner', fn ($q) => $q->whereIn('country_id', (array) $filters['country_id']));
        });

        $query->when(! empty($filters['product_id']), function ($query) use ($filters) {
            $query->whereHas('orderLines', fn ($q) => $q->whereIn('product_id', (array) $filters['product_id']));
        });

        $query->when(! empty($filters['customer_id']), function ($query) use ($filters) {
            $query->whereIn('partner_id', (array) $filters['customer_id']);
        });

        $query->when(! empty($filters['category_id']), function ($query) use ($filters) {
            $query->whereHas('orderLines.product.category', fn ($q) => $q->whereIn('category_id', (array) $filters['category_id']));
        });

        $query->when(! empty($filters['salesteam_id']), function ($query) use ($filters) {
            $query->whereIn('team_id', (array) $filters['salesteam_id']);
        });

        $query->when(! empty($filters['salesperson_id']), function ($query) use ($filters) {
            $query->whereIn('user_id', (array) $filters['salesperson_id']);
        });

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
            [
                'key'         => 'fully_invoiced',
                'state'       => OrderState::SALE,
                'title'       => __('sales::filament/pages/sales-dashboard.widgets.stats-overview.fully-invoiced'),
                'description' => __('sales::filament/pages/sales-dashboard.widgets.stats-overview.descriptions.fully-invoiced'),
                'color'       => 'success',
            ],
            [
                'key'         => 'archived',
                'state'       => OrderState::SALE,
                'title'       => __('sales::filament/pages/sales-dashboard.widgets.stats-overview.archived'),
                'description' => __('sales::filament/pages/sales-dashboard.widgets.stats-overview.descriptions.archived'),
                'color'       => 'secondary',
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

    protected function countFullyInvoicedOrder($query, bool $previous = false): int
    {
        $cloneQuery = $this->applyDateRange($query, $previous);

        return $cloneQuery->where('invoice_status', InvoiceStatus::INVOICED)
            ->count() ?? 0;
    }

    protected function countArchivedOrders($query, bool $previous = false): int
    {
        $cloneQuery = $this->applyDateRange($query, $previous);

        return $cloneQuery->onlyTrashed()
            ->count();
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
            return [
                'description' => __('sales::filament/pages/sales-dashboard.widgets.stats-overview.no-change'),
                'icon'        => null,
                'color'       => 'gray',
            ];
        }

        if ($previous == 0 && $current > 0) {
            return [
                'description' => '100% '.__('sales::filament/pages/sales-dashboard.widgets.stats-overview.increase'),
                'icon'        => 'heroicon-m-arrow-trending-up',
                'color'       => 'success',
            ];
        }

        $percentage = round((($current - $previous) / $previous) * 100, 1);

        if ($percentage > 0) {
            return [
                'description' => abs($percentage).'% '.__('sales::filament/pages/sales-dashboard.widgets.stats-overview.increase'),
                'icon'        => 'heroicon-m-arrow-trending-up',
                'color'       => 'success',
            ];
        } elseif ($percentage < 0) {
            return [
                'description' => abs($percentage).'% '.__('sales::filament/pages/sales-dashboard.widgets.stats-overview.decrease'),
                'icon'        => 'heroicon-m-arrow-trending-down',
                'color'       => 'danger',
            ];
        }

        return [
            'description' => __('sales::filament/pages/sales-dashboard.widgets.stats-overview.no-change'),
            'icon'        => 'heroicon-m-minus',
            'color'       => 'gray',
        ];
    }

    protected function getChartData($query, array $config): array
    {
        $filters = $this->filters;
        $start = ! empty($filters['start_date']) ? Carbon::parse($filters['start_date']) : now()->subMonth();
        $end = ! empty($filters['end_date']) ? Carbon::parse($filters['end_date']) : now();

        $periodDays = $end->diffInDays($start) + 1;

        // Limit chart data points to maximum 30 for better performance
        $interval = max(1, ceil($periodDays / 30));

        $chartData = [];
        $currentDate = $start->copy();

        while ($currentDate <= $end) {
            $rangeEnd = $currentDate->copy()->addDays($interval - 1);
            if ($rangeEnd > $end) {
                $rangeEnd = $end->copy();
            }

            $dateQuery = clone $query;
            $dateQuery->whereBetween('date_order', [$currentDate->startOfDay(), $rangeEnd->endOfDay()]);

            if ($config['key'] === 'total_revenue') {
                $value = (float) $dateQuery->where('state', OrderState::SALE)->sum('amount_total');
            } elseif ($config['key'] === 'avg_revenue') {
                $value = (float) $dateQuery->where('state', OrderState::SALE)->avg('amount_total');
            } elseif ($config['key'] === 'fully_invoiced') {
                $value = $dateQuery->where('invoice_status', InvoiceStatus::INVOICED)->count();
            } elseif ($config['key'] === 'archived') {
                $value = $dateQuery->onlyTrashed()->count();
            } else {
                $value = $dateQuery->where('state', $config['state']->value)->count();
            }

            $chartData[] = $value;
            $currentDate->addDays($interval);
        }

        return $chartData;
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
        return Currency::where('active', 1)->first()->symbol ?? '$';
    }

    protected function getHeading(): ?string
    {
        return __('sales::filament/pages/sales-dashboard.widgets.stats-overview.heading');
    }
}
