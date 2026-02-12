<?php

namespace Webkul\Sale\Filament\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
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
    use HasWidgetShield,InteractsWithPageFilters;

    protected static ?string $pollingInterval = '15s';

    protected function getHeading(): ?string
    {
        return __('sales::filament/pages/sales-dashboard.widgets.top-products.heading');
    }

    public function table(Table $table): Table
    {
        $query = $this->applyFilters($this->baseQuery());

        return $table
            ->query($query)
            ->defaultPaginationPageOption(5)
            ->columns([
                Tables\Columns\TextColumn::make('product_id')
                    ->label(__('sales::filament/pages/sales-dashboard.widgets.top-products.column.product'))
                    ->formatStateUsing(fn ($state, $record) => $record->product?->name ?? '—')
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_qty')
                    ->label(__('sales::filament/pages/sales-dashboard.widgets.top-products.column.total_orders'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_revenue')
                    ->label(__('sales::filament/pages/sales-dashboard.widgets.top-products.column.total_revenue'))
                    ->money($this->getActiveCurrency(), true)
                    ->sortable(),
            ]);
    }

    /**
     * 🔹 Group by product and sum qty & revenue across all orders
     */
    protected function baseQuery(): Builder
    {
        return OrderLine::query()
            ->whereHas('order', fn ($q) => $q->where('state', OrderState::SALE))
            ->whereHas('product')
            ->with('product')
            ->select(
                'product_id',
                DB::raw('SUM(product_uom_qty) as total_qty'),
                DB::raw('SUM(price_total) as total_revenue')
            )
            ->groupBy('product_id')
            ->orderByDesc('total_revenue');
    }

    protected function applyFilters(Builder $query): Builder
    {
        $filters = $this->filters;

        $query->when(! empty($filters['start_date']), function ($query) use ($filters) {
            $query->whereHas('order', fn ($q) => $q->whereDate('date_order', '>=', $filters['start_date']));
        });

        $query->when(! empty($filters['end_date']), function ($query) use ($filters) {
            $query->whereHas('order', fn ($q) => $q->whereDate('date_order', '<=', $filters['end_date']));
        });

        $query->when(! empty($filters['salesperson_id']), function ($query) use ($filters) {
            $query->whereHas('order', fn ($q) => $q->whereIn('user_id', (array) $filters['salesperson_id']));
        });

        $query->when(! empty($filters['country_id']), function ($query) use ($filters) {
            $query->whereHas('order.partner', fn ($q) => $q->whereIn('country_id', (array) $filters['country_id']));
        });

        $query->when(! empty($filters['product_id']), function ($query) use ($filters) {
            $query->whereIn('product_id', (array) $filters['product_id']);
        });

        $query->when(! empty($filters['category_id']), function ($query) use ($filters) {
            $query->whereHas('product.category', fn ($q) => $q->whereIn('category_id', (array) $filters['category_id']));
        });

        $query->when(! empty($filters['customer_id']), function ($query) use ($filters) {
            $query->whereHas('order', fn ($q) => $q->whereIn('partner_id', (array) $filters['customer_id']));
        });

        $query->when(! empty($filters['salesteam_id']), function ($query) use ($filters) {
            $query->whereHas('order', fn ($q) => $q->whereIn('team_id', (array) $filters['salesteam_id']));
        });

        return $query;
    }

    protected function getActiveCurrency(): ?string
    {
        return Currency::where('active', true)->value('name');
    }

    public function getTableRecordKey($record): string
    {
        return (string) $record->product_id;
    }
}
