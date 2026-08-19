<?php

namespace Webkul\Purchase\Filament\Admin\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Carbon\Carbon;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Webkul\Purchase\Models\PurchaseOrder;

class PurchaseStatsWidget extends BaseWidget
{
    use HasWidgetShield;
    use InteractsWithPageFilters;

    protected ?string $pollingInterval = '5s';

    protected function getColumns(): int
    {
        return 4;
    }

    public function getStats(): array
    {
        $baseQuery = $this->getBaseFilteredQuery();
        $currentQuery = $this->applyDateRange(clone $baseQuery);
        $previousQuery = $this->applyDateRange(clone $baseQuery, previous: true);

        $stats = [
            [
                'title'   => 'Draft RFQs',
                'current' => (clone $currentQuery)->where('state', 'draft')->count(),
                'previous'=> (clone $previousQuery)->where('state', 'draft')->count(),
                'icon'    => 'heroicon-o-document-text',
            ],
            [
                'title'   => 'Sent RFQs',
                'current' => (clone $currentQuery)->where('state', 'sent')->count(),
                'previous'=> (clone $previousQuery)->where('state', 'sent')->count(),
                'icon'    => 'heroicon-o-paper-airplane',
            ],
            [
                'title'   => 'Confirmed Orders',
                'current' => (clone $currentQuery)->whereIn('state', ['purchase', 'done'])->count(),
                'previous'=> (clone $previousQuery)->whereIn('state', ['purchase', 'done'])->count(),
                'icon'    => 'heroicon-o-check-circle',
            ],
            [
                'title'   => 'Total Purchase Value',
                'current' => (float) (clone $currentQuery)->whereIn('state', ['purchase', 'done'])->sum('total_amount'),
                'previous'=> (float) (clone $previousQuery)->whereIn('state', ['purchase', 'done'])->sum('total_amount'),
                'icon'    => 'heroicon-o-currency-dollar',
                'money'   => true,
            ],
        ];

        return array_map(function (array $item): Stat {
            $trend = $this->calculateTrend($item['current'], $item['previous']);
            $value = ! empty($item['money'])
                ? money((float) $item['current'])
                : number_format((int) $item['current']);

            return Stat::make($item['title'], $value)
                ->description($trend['description'])
                ->descriptionIcon($trend['icon'])
                ->color($trend['color'])
                ->icon($item['icon'])
                ->chart([$item['previous'], $item['current']]);
        }, $stats);
    }

    protected function getBaseFilteredQuery()
    {
        $filters = $this->filters ?? [];

        return PurchaseOrder::query()
            ->when(! empty($filters['country_id']), fn ($query) => $query->whereHas('partner', fn ($partnerQuery) => $partnerQuery->whereIn('country_id', (array) $filters['country_id'])))
            ->when(! empty($filters['product_id']), fn ($query) => $query->whereHas('lines', fn ($lineQuery) => $lineQuery->whereIn('product_id', (array) $filters['product_id'])))
            ->when(! empty($filters['partner_id']), fn ($query) => $query->whereIn('partner_id', (array) $filters['partner_id']))
            ->when(! empty($filters['category_id']), fn ($query) => $query->whereHas('lines.product', fn ($productQuery) => $productQuery->whereIn('category_id', (array) $filters['category_id'])))
            ->when(! empty($filters['buyer_id']), fn ($query) => $query->whereIn('user_id', (array) $filters['buyer_id']))
            ->when(! empty($filters['state']), fn ($query) => $query->whereIn('state', (array) $filters['state']));
    }

    protected function applyDateRange($query, bool $previous = false)
    {
        $filters = $this->filters ?? [];
        $start = ! empty($filters['start_date']) ? Carbon::parse($filters['start_date']) : now()->subMonth();
        $end = ! empty($filters['end_date']) ? Carbon::parse($filters['end_date']) : now();

        if ($previous) {
            $periodLength = $end->diffInDays($start) + 1;
            $previousEnd = $start->copy()->subDay();
            $previousStart = $previousEnd->copy()->subDays($periodLength - 1);

            return $query->whereBetween('ordered_at', [$previousStart->startOfDay(), $previousEnd->endOfDay()]);
        }

        return $query->whereBetween('ordered_at', [$start->startOfDay(), $end->endOfDay()]);
    }

    protected function calculateTrend(float|int $current, float|int $previous): array
    {
        if ($previous == 0 && $current == 0) {
            return [
                'description' => 'No change',
                'icon'        => 'heroicon-m-minus',
                'color'       => 'gray',
            ];
        }

        if ($previous == 0 && $current > 0) {
            return [
                'description' => '100% increase',
                'icon'        => 'heroicon-m-arrow-trending-up',
                'color'       => 'success',
            ];
        }

        $percentage = round((($current - $previous) / $previous) * 100, 1);

        if ($percentage > 0) {
            return [
                'description' => abs($percentage).'% increase',
                'icon'        => 'heroicon-m-arrow-trending-up',
                'color'       => 'success',
            ];
        }

        if ($percentage < 0) {
            return [
                'description' => abs($percentage).'% decrease',
                'icon'        => 'heroicon-m-arrow-trending-down',
                'color'       => 'danger',
            ];
        }

        return [
            'description' => 'No change',
            'icon'        => 'heroicon-m-minus',
            'color'       => 'gray',
        ];
    }
}
