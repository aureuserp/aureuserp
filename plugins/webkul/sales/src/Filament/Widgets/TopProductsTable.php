<?php

namespace Webkul\Sale\Filament\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Sale\Filament\Clusters\Products\Resources\ProductResource;
use Webkul\Sale\Filament\Widgets\Concerns\HasSaleDashboardFilters;
use Webkul\Sale\Models\OrderLine;

class TopProductsTable extends TableWidget
{
    use HasSaleDashboardFilters, HasWidgetShield;

    protected static ?int $sort = 6;

    protected static bool $isLazy = false;

    public function table(Table $table): Table
    {
        return $table
            ->defaultKeySort(false)
            ->recordUrl(fn ($record): ?string => $record->id && ProductResource::canAccess()
                ? ProductResource::getUrl('view', ['record' => $record->id])
                : null);
    }

    protected function getTableHeading(): string|Htmlable|null
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
            ->leftJoin('products_products', 'products_products.id', '=', 'sales_order_lines.product_id')
            ->groupBy('products_products.id', 'products_products.name')
            ->selectRaw('COALESCE(products_products.id, 0) as id')
            ->addSelect('products_products.name as name')
            ->selectRaw('SUM(sales_order_lines.product_uom_qty) as quantity')
            ->selectRaw('SUM(sales_order_lines.price_total) as revenue')
            ->havingRaw('SUM(sales_order_lines.price_total) > 0')
            ->orderByDesc('revenue')
            ->limit(5);
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('name')
                ->label(__('sales::filament/widgets/sales-dashboard.top-products.columns.name'))
                ->placeholder(__('sales::filament/widgets/sales-dashboard.unknown')),
            Tables\Columns\TextColumn::make('quantity')
                ->label(__('sales::filament/widgets/sales-dashboard.top-products.columns.quantity'))
                ->numeric(),
            Tables\Columns\TextColumn::make('revenue')
                ->label(__('sales::filament/widgets/sales-dashboard.top-products.columns.revenue'))
                ->formatStateUsing(fn ($state) => money($state ?? 0, current_company()?->currency?->name)),
        ];
    }
}
