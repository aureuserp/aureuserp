<?php

namespace Webkul\PointOfSale\Filament\Admin\Widgets\Concerns;

use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Carbon;

trait FiltersDashboardOrders
{
    use InteractsWithPageFilters;

    protected function applyOrderFilters(Builder $query, string $table = 'pos_orders'): Builder
    {
        return $query
            ->when($this->getFilterIds('selectedTerminals'), fn (Builder $query, array $ids) => $query->whereIn("{$table}.config_id", $ids))
            ->when($this->getFilterIds('selectedSalespersons'), fn (Builder $query, array $ids) => $query->whereIn("{$table}.user_id", $ids))
            ->when($this->getFilterIds('selectedCustomers'), fn (Builder $query, array $ids) => $query->whereIn("{$table}.partner_id", $ids))
            ->when($this->getFilterStartDate(), fn (Builder $query, Carbon $date) => $query->where("{$table}.ordered_at", '>=', $date))
            ->when($this->getFilterEndDate(), fn (Builder $query, Carbon $date) => $query->where("{$table}.ordered_at", '<=', $date))
            ->when(
                $this->hasProductFilters(),
                fn (Builder $query) => $query->whereHas('lines', fn (Builder $query) => $this->applyOrderLineProductFilters($query)),
            );
    }

    protected function applyOrderLineProductFilters(Builder $query, string $table = 'pos_order_lines'): Builder
    {
        return $query
            ->when($this->getFilterIds('selectedProducts'), fn (Builder $query, array $ids) => $query->whereIn("{$table}.product_id", $ids))
            ->when(
                $this->getFilterIds('selectedCategories'),
                fn (Builder $query, array $ids) => $query->whereIn(
                    "{$table}.product_id",
                    fn (QueryBuilder $query) => $query
                        ->select('product_id')
                        ->from('pos_category_products')
                        ->whereIn('category_id', $ids),
                ),
            );
    }

    protected function hasProductFilters(): bool
    {
        return filled($this->getFilterIds('selectedProducts'))
            || filled($this->getFilterIds('selectedCategories'));
    }

    /**
     * @return array<int, int>
     */
    protected function getFilterIds(string $key): array
    {
        return array_values(array_filter((array) ($this->pageFilters[$key] ?? []), 'filled'));
    }

    protected function getFilterStartDate(): ?Carbon
    {
        $date = $this->pageFilters['startDate'] ?? null;

        return filled($date) ? Carbon::parse($date)->startOfDay() : null;
    }

    protected function getFilterEndDate(): ?Carbon
    {
        $date = $this->pageFilters['endDate'] ?? null;

        return filled($date) ? Carbon::parse($date)->endOfDay() : null;
    }
}
