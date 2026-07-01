<?php

namespace Webkul\Sale\Filament\Widgets;

use Filament\Tables;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Sale\Filament\Widgets\Concerns\HasSaleDashboardFilters;
use Webkul\Sale\Models\OrderLine;

class TopCategoriesTable extends TableWidget
{
    use HasSaleDashboardFilters;

    protected static ?int $sort = 8;

    public function getHeading(): ?string
    {
        return __('sales::filament/widgets/sales-dashboard.top-categories.heading');
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
            ->join('products_categories', 'products_categories.id', '=', 'products_products.category_id')
            ->groupBy('products_categories.id', 'products_categories.name')
            ->select('products_categories.id as id', 'products_categories.name as name')
            ->selectRaw('SUM(sales_order_lines.price_total) as revenue')
            ->orderByDesc('revenue')
            ->limit(10);
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('name')
                ->label(__('sales::filament/widgets/sales-dashboard.top-categories.columns.name')),
            Tables\Columns\TextColumn::make('revenue')
                ->label(__('sales::filament/widgets/sales-dashboard.top-categories.columns.revenue'))
                ->formatStateUsing(fn ($state) => money($state ?? 0)),
        ];
    }
}
