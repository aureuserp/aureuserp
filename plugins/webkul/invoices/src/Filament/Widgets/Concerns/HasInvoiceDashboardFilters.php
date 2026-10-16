<?php

namespace Webkul\Invoice\Filament\Widgets\Concerns;

use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Webkul\Account\Enums\MoveType;
use Webkul\Invoice\Models\Invoice;

trait HasInvoiceDashboardFilters
{
    use InteractsWithPageFilters;

    /**
     * @return array<string, mixed>
     */
    protected function dashboardFilters(): array
    {
        return $this->pageFilters ?? [];
    }

    protected function applyFilters(Builder $query, string $partnerFilter, ?Carbon $start = null, ?Carbon $end = null): Builder
    {
        $filters = $this->dashboardFilters();

        [$defaultStart, $defaultEnd] = $this->periodRange();

        $query->whereBetween($query->qualifyColumn('invoice_date'), [$start ?? $defaultStart, $end ?? $defaultEnd]);

        if (! empty($filters['salesperson_id'])) {
            $query->whereIn($query->qualifyColumn('invoice_user_id'), (array) $filters['salesperson_id']);
        }

        if (! empty($filters['product_id'])) {
            $query->whereHas('lines', fn (Builder $q) => $q
                ->where('display_type', 'product')
                ->whereIn('product_id', (array) $filters['product_id']));
        }

        if (! empty($filters['category_id'])) {
            $query->whereHas('lines.product', fn (Builder $q) => $q->whereIn('category_id', (array) $filters['category_id']));
        }

        if (! empty($filters[$partnerFilter])) {
            $query->whereIn($query->qualifyColumn('partner_id'), (array) $filters[$partnerFilter]);
        }

        if (! empty($filters['payment_state'])) {
            $query->whereIn($query->qualifyColumn('payment_state'), (array) $filters['payment_state']);
        }

        return $query;
    }

    protected function customerInvoices(?Carbon $start = null, ?Carbon $end = null): Builder
    {
        $query = Invoice::query();

        return $this->applyFilters($query, 'customer_id', $start, $end)
            ->where($query->qualifyColumn('move_type'), MoveType::OUT_INVOICE);
    }

    protected function vendorBills(?Carbon $start = null, ?Carbon $end = null): Builder
    {
        $query = Invoice::query();

        return $this->applyFilters($query, 'vendor_id', $start, $end)
            ->where($query->qualifyColumn('move_type'), MoveType::IN_INVOICE);
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
