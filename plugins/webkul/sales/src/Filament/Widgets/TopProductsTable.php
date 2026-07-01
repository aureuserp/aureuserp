<?php

namespace Webkul\Sale\Filament\Widgets;

use Filament\Tables;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Sale\Filament\Widgets\Concerns\HasSaleDashboardFilters;
use Webkul\Sale\Models\OrderLine;

class TopProductsTable extends TableWidget
{
    use HasSaleDashboardFilters;

    protected static ?int $sort = 6;

    public function getHeading(): ?string
    {
        return __('sales::filament/widgets/sales-dashboard.top-products.heading');
    }

    protected function isTablePaginationEnabled(): bool
    {
        return false;
    }

    protected function getTableQuery(): Builder
    {
        return OrderLine::query()
            ->whereHas('order', fn (Builder $query) => $this->applyConfirmedScope($query))
            ->join('products_products', 'products_products.id', '=', 'sales_order_lines.product_id')
            ->groupBy('products_products.id', 'products_products.name')
            ->select('products_products.id as id', 'products_products.name as name')
            ->selectRaw('SUM(sales_order_lines.product_uom_qty) as quantity')
            ->selectRaw('SUM(sales_order_lines.price_total) as revenue')
            ->orderByDesc('revenue')
            ->limit(10);
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('name')
                ->label(__('sales::filament/widgets/sales-dashboard.top-products.columns.name')),
            Tables\Columns\TextColumn::make('quantity')
                ->label(__('sales::filament/widgets/sales-dashboard.top-products.columns.quantity'))
                ->numeric(),
            Tables\Columns\TextColumn::make('revenue')
                ->label(__('sales::filament/widgets/sales-dashboard.top-products.columns.revenue'))
                ->formatStateUsing(fn ($state) => money($state ?? 0)),
        ];
    }
}
