<?php

namespace Webkul\Sale\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Webkul\Sale\Enums\OrderState;
use Webkul\Sale\Models\OrderLine;
use Webkul\Support\Models\Currency;

class TopProductsWidget extends BaseWidget
{
    use InteractsWithPageFilters;

    protected int|string|array $columnSpan = 'full';

    protected function getHeading(): ?string
    {
        return __('sales::filament/widgets/top-products.heading');
    }

    /**
     * 🔹 Build and return the table with query + columns.
     */
    public function table(Table $table): Table
    {
        return $table
            ->query($this->applyFilters($this->baseQuery()))
            ->defaultPaginationPageOption(5)
            ->columns($this->getTableColumns());
    }

    /**
     * 🔹 Base query before filters.
     */
    protected function baseQuery(): Builder
    {
        return OrderLine::query()
            ->where('state', OrderState::SALE)
            ->select(
                'product_id',
                DB::raw('SUM(product_uom_qty) as total_qty'),
                DB::raw('SUM(price_total) as total_revenue')
            )
            ->with('product')
            ->groupBy('product_id')
            ->orderByDesc('total_qty');
    }

    /**
     * 🔹 Apply page filters (date & salesperson).
     */
    protected function applyFilters(Builder $query): Builder
    {
        $filters = $this->filters;

        if (! empty($filters['start_date'])) {
            $query->whereDate('create_date', '>=', $filters['start_date']);
        }

        if (! empty($filters['end_date'])) {
            $query->whereDate('create_date', '<=', $filters['end_date']);
        }

        if (! empty($filters['salesperson_id'])) {
            $query->where('salesman_id', $filters['salesperson_id']);
        }

        return $query;
    }

    /**
     * 🔹 Table columns definition.
     */
    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('product.name')
                ->label(__('sales::filament/widgets/top-products.tables.product'))
                ->sortable()
                ->searchable(),

            Tables\Columns\TextColumn::make('total_qty')
                ->label(__('sales::filament/widgets/top-products.tables.quantity-sold'))
                ->sortable(),

            Tables\Columns\TextColumn::make('total_revenue')
                ->label(__('sales::filament/widgets/top-products.tables.revenue'))
                ->money($this->getActiveCurrency(), true)
                ->sortable(),
        ];
    }

    /**
     * 🔹 Get active currency for money formatting.
     */
    protected function getActiveCurrency(): ?string
    {
        return Currency::where('active', true)->value('name');
    }
}
