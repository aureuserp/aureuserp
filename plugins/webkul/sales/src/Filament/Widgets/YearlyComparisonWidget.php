<?php

namespace Webkul\Sale\Filament\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Sale\Enums\OrderState;
use Webkul\Sale\Models\Order;

class YearlyComparisonWidget extends ChartWidget
{
    use HasWidgetShield, InteractsWithPageFilters;

    protected ?string $maxHeight = '450px';

    protected function getData(): array
    {
        $baseQuery = $this->applyFilters($this->baseQuery());
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

    protected function baseQuery(): Builder
    {
        return Order::query()
            ->where('state', OrderState::SALE);
    }

    protected function applyFilters(Builder $query): Builder
    {
        $filters = $this->filters;

        $query->when(! empty($filters['salesperson_id']), function ($query) use ($filters) {
            $query->whereIn('user_id', (array) $filters['salesperson_id']);
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

        return $query;

    }

    protected function getType(): string
    {
        return 'bar';
    }

    public function getHeading(): ?string
    {
        return __('sales::filament/pages/sales-dashboard.widgets.yearly-comparison.heading');
    }
}
