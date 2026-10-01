<?php

namespace Webkul\Purchase\Filament\Admin\Widgets\Concerns;

use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Webkul\Purchase\Models\OrderLine;
use Webkul\Purchase\Models\PurchaseOrder;

trait HasPurchaseDashboardFilters
{
    use InteractsWithPageFilters;

    /**
     * @return array<string, mixed>
     */
    protected function dashboardFilters(): array
    {
        return $this->pageFilters ?? [];
    }

    protected function applyFilters(Builder $query, ?Carbon $start = null, ?Carbon $end = null): Builder
    {
        $filters = $this->dashboardFilters();

        [$defaultStart, $defaultEnd] = $this->periodRange();

        $query->whereBetween($query->qualifyColumn('ordered_at'), [$start ?? $defaultStart, $end ?? $defaultEnd]);

        if (! empty($filters['country_id'])) {
            $query->whereHas('partner', fn (Builder $q) => $q->whereIn('country_id', (array) $filters['country_id']));
        }

        if (! empty($filters['product_id'])) {
            $query->whereHas('lines', fn (Builder $q) => $q->whereIn('product_id', (array) $filters['product_id']));
        }

        if (! empty($filters['partner_id'])) {
            $query->whereIn($query->qualifyColumn('partner_id'), (array) $filters['partner_id']);
        }

        if (! empty($filters['category_id'])) {
            $query->whereHas('lines.product', fn (Builder $q) => $q->whereIn('category_id', (array) $filters['category_id']));
        }

        if (! empty($filters['buyer_id'])) {
            $query->whereIn($query->qualifyColumn('user_id'), (array) $filters['buyer_id']);
        }

        if (! empty($filters['state'])) {
            $query->whereIn($query->qualifyColumn('state'), (array) $filters['state']);
        }

        return $query;
    }

    protected function purchaseOrders(?Carbon $start = null, ?Carbon $end = null): Builder
    {
        return $this->applyFilters(PurchaseOrder::query(), $start, $end);
    }

    protected function purchaseLines(?Carbon $start = null, ?Carbon $end = null): Builder
    {
        return OrderLine::query()
            ->whereHas('order', fn (Builder $query) => $this->applyFilters($query, $start, $end));
    }

    /**
     * @return array{0: Carbon, 1: Carbon}
     */
    protected function periodRange(): array
    {
        $filters = $this->dashboardFilters();

        $start = ! empty($filters['start_date'])
            ? Carbon::parse($filters['start_date'])->startOfDay()
            : now()->subMonth()->startOfDay();

        $end = ! empty($filters['end_date'])
            ? Carbon::parse($filters['end_date'])->endOfDay()
            : now()->endOfDay();

        return [$start, $end];
    }

    /**
     * @return array{0: Carbon, 1: Carbon}
     */
    protected function previousPeriodRange(): array
    {
        [$start, $end] = $this->periodRange();

        $length = $start->diffInDays($end) + 1;

        $previousEnd = (clone $start)->subDay()->endOfDay();

        return [(clone $previousEnd)->subDays($length - 1)->startOfDay(), $previousEnd];
    }

    protected function dashboardCurrency(): ?string
    {
        return current_company()?->currency?->name;
    }
}
