<?php

namespace Webkul\Sale\Filament\Widgets\Concerns;

use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Webkul\Sale\Enums\OrderState;
use Webkul\Sale\Models\Order;

trait HasSaleDashboardFilters
{
    use InteractsWithPageFilters;

    /**
     * Apply the shared dashboard filters to a sales order query.
     */
    protected function applyFilters(Builder $query): Builder
    {
        $filters = $this->pageFilters ?? [];

        if (! empty($filters['startDate'])) {
            $query->whereDate($query->qualifyColumn('date_order'), '>=', Carbon::parse($filters['startDate']));
        }

        if (! empty($filters['endDate'])) {
            $query->whereDate($query->qualifyColumn('date_order'), '<=', Carbon::parse($filters['endDate']));
        }

        if (! empty($filters['countries'])) {
            $query->whereHas('partner', fn (Builder $q) => $q->whereIn('country_id', $filters['countries']));
        }

        if (! empty($filters['products'])) {
            $query->whereHas('lines', fn (Builder $q) => $q->whereIn('product_id', $filters['products']));
        }

        if (! empty($filters['categories'])) {
            $query->whereHas('lines.product', fn (Builder $q) => $q->whereIn('category_id', $filters['categories']));
        }

        if (! empty($filters['teams'])) {
            $query->whereIn($query->qualifyColumn('team_id'), $filters['teams']);
        }

        if (! empty($filters['salesPersons'])) {
            $query->whereIn($query->qualifyColumn('user_id'), $filters['salesPersons']);
        }

        return $query;
    }

    /**
     * Apply the filters and scope the query to confirmed sales orders.
     */
    protected function applyConfirmedScope(Builder $query): Builder
    {
        return $this->applyFilters($query)->where($query->qualifyColumn('state'), OrderState::SALE->value);
    }

    /**
     * Filtered query scoped to confirmed sales orders.
     */
    protected function saleOrders(): Builder
    {
        return $this->applyConfirmedScope(Order::query());
    }

    /**
     * Filtered query scoped to quotations (draft and sent).
     */
    protected function quotations(): Builder
    {
        $query = Order::query();

        return $this->applyFilters($query)
            ->whereIn($query->qualifyColumn('state'), [OrderState::DRAFT->value, OrderState::SENT->value]);
    }
}
