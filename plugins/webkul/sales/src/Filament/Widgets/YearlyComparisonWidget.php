<?php

namespace Webkul\Sale\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Webkul\Sale\Enums\OrderState;
use Webkul\Sale\Models\Order;

class YearlyComparisonWidget extends ChartWidget
{
    use InteractsWithPageFilters;

    protected ?string $maxHeight = '450px';

    public function getHeading(): ?string
    {
        return __('sales::filament/pages/sales-dashboard.widgets.yearly-comparison.heading');
    }

    protected function getData(): array
    {
        $filters = $this->filters;

        $baseQuery = Order::query()->where('state', OrderState::SALE);

        // Apply filters
        if (! empty($filters['salesperson_id'])) {
            $baseQuery->whereIn('user_id', (array) $filters['salesperson_id']);
        }

        if (! empty($filters['country_id'])) {
            $baseQuery->whereHas('partner', fn ($q) => $q->whereIn('country_id', (array) $filters['country_id']));
        }

        if (! empty($filters['product_id'])) {
            $baseQuery->whereHas('orderLines', fn ($q) => $q->whereIn('product_id', (array) $filters['product_id']));
        }

        if (! empty($filters['customer_id'])) {
            $baseQuery->whereIn('partner_id', (array) $filters['customer_id']);
        }

        if (! empty($filters['category_id'])) {
            $baseQuery->whereHas('orderLines.product.category', fn ($q) => $q->whereIn('category_id', (array) $filters['category_id']));
        }

        if (! empty($filters['salesteam_id'])) {
            $baseQuery->whereIn('team_id', (array) $filters['salesteam_id']);
        }

        $currentYear = now()->year;
        $previousYear = $currentYear - 1;

        $currentYearSales = (clone $baseQuery)
            ->whereYear('date_order', $currentYear)
            ->sum('amount_total');

        $previousYearSales = (clone $baseQuery)
            ->whereYear('date_order', $previousYear)
            ->sum('amount_total');

        return [
            'datasets' => [
                [
                    'label' => __('sales::filament/pages/sales-dashboard.widgets.yearly-comparison.label'),
                    'data'  => [
                        $previousYearSales,
                        $currentYearSales,
                    ],
                    'backgroundColor'    => ['#a3e635', '#3b82f6'],
                    'borderColor'        => '#1e293b',
                    'borderWidth'        => 1,
                    'barPercentage'      => 0.5,
                    'categoryPercentage' => 0.6,
                ],
            ],
            'labels' => [
                (string) $previousYear,
                (string) $currentYear,
            ],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
