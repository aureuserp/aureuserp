<?php

namespace Webkul\Sale\Filament\Widgets;

use Carbon\Carbon;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Webkul\Sale\Enums\OrderState;
use Webkul\Sale\Models\Order;

class StatsOverviewWidget extends BaseWidget
{
    use InteractsWithPageFilters;

    protected function getHeading(): ?string
    {
        return 'Sales Stats Overview';
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
            $currentCount = $this->countOrdersByState(clone $query, $config['state']->value);
            $previousCount = $this->countOrdersByState(clone $query, $config['state']->value, previous: true);

            $trend = $this->calculateTrend($currentCount, $previousCount);

            $stats[] = Stat::make($config['title'], number_format($currentCount))
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
            $query->whereHas('orderLines', function ($q) use ($filters) {
                $q->whereIn('product_id', (array) $filters['product_id']);
            });
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
                'state'       => OrderState::SENT,
                'title'       => 'Quotation',
                'description' => 'Number of quotations sent to customers',
                'color'       => 'primary',
            ],
            [
                'state'       => OrderState::SALE,
                'title'       => 'Confirmed Order',
                'description' => 'Orders confirmed by customers',
                'color'       => 'success',
            ],
            [
                'state'       => OrderState::DRAFT,
                'title'       => 'Draft Quotation',
                'description' => 'Quotations still in draft state',
                'color'       => 'info',
            ],
            [
                'state'       => OrderState::CANCEL,
                'title'       => 'Cancel Quotation',
                'description' => 'Quotations that were cancelled',
                'color'       => 'danger',
            ],
        ];
    }

    protected function countOrdersByState($query, string $state, bool $previous = false): int
    {
        $cloneQuery = clone $query;

        $filters = $this->filters;
        $start = ! empty($filters['start_date']) ? Carbon::parse($filters['start_date']) : now()->subMonth();
        $end = ! empty($filters['end_date']) ? Carbon::parse($filters['end_date']) : now();

        if ($previous) {
            $periodLength = $end->diffInDays($start) + 1;
            $prevEnd = $start->copy()->subDay();
            $prevStart = $prevEnd->copy()->subDays($periodLength - 1);
            $cloneQuery->whereBetween('date_order', [$prevStart->startOfDay(), $prevEnd->endOfDay()]);
        } else {
            $cloneQuery->whereBetween('date_order', [$start->startOfDay(), $end->endOfDay()]);
        }

        return $cloneQuery->where('state', $state)->count() ?? 0;
    }

    protected function calculateTrend(int $current, int $previous): array
    {
        if ($previous === 0 && $current === 0) {
            return ['text' => '—', 'icon' => null, 'color' => 'gray'];
        }

        if ($previous === 0 && $current > 0) {
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
}
