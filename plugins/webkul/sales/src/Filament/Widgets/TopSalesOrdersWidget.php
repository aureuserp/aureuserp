<?php

namespace Webkul\Sale\Filament\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Tables;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Webkul\Sale\Models\Order;
use Webkul\Support\Models\Currency;

class TopSalesOrdersWidget extends BaseWidget
{
    use InteractsWithPageFilters;

    public function getHeading(): ?string
    {
        return __('sales::filament/widgets/top-sales-order.heading');
    }

    /**
     * 🔹 Main table query with applied filters.
     */
    protected function getTableQuery(): Builder
    {
        return $this->applyFilters($this->baseQuery());
    }

    /**
     * 🔹 Base query before applying filters.
     */
    protected function baseQuery(): Builder
    {
        return Order::query()
            ->select(
                'user_id',
                DB::raw('COUNT(*) as total_orders'),
                DB::raw('SUM(amount_total) as total_revenue')
            )
            ->where('state', 'sale')
            ->groupBy('user_id')
            ->orderByDesc('total_revenue')
            ->with('user');
    }

    /**
     * 🔹 Apply dynamic filters.
     */
    protected function applyFilters(Builder $query): Builder
    {
        $filters = $this->filters;

        if (! empty($filters['start_date'])) {
            $query->whereDate('date_order', '>=', $filters['start_date']);
        }

        if (! empty($filters['end_date'])) {
            $query->whereDate('date_order', '<=', $filters['end_date']);
        }

        if (! empty($filters['salesperson_id'])) {
            $query->where('user_id', $filters['salesperson_id']);
        }

        return $query;
    }

    /**
     * 🔹 Table columns definition.
     */
    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('user.name')
                ->label(__('sales::filament/widgets/top-sales-order.sales-person'))
                ->sortable(),

            Tables\Columns\TextColumn::make('total_orders')
                ->label(__('sales::filament/widgets/top-sales-order.total-order'))
                ->sortable(),

            Tables\Columns\TextColumn::make('total_revenue')
                ->label(__('sales::filament/widgets/top-sales-order.revenue'))
                ->money($this->getActiveCurrency(), true)
                ->sortable(),
        ];
    }

    /**
     * 🔹 Get currently active currency.
     */
    protected function getActiveCurrency(): ?string
    {
        return Currency::where('active', true)->value('name');
    }
}
