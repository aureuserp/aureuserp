<?php

namespace Webkul\Sale\Filament\Widgets;

use Filament\Tables;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Sale\Filament\Widgets\Concerns\HasSaleDashboardFilters;

class TopSalesPersonsTable extends TableWidget
{
    use HasSaleDashboardFilters;

    protected static ?int $sort = 10;

    public function getHeading(): ?string
    {
        return __('sales::filament/widgets/sales-dashboard.top-sales-persons.heading');
    }

    protected function isTablePaginationEnabled(): bool
    {
        return false;
    }

    protected function getTableQuery(): Builder
    {
        return $this->saleOrders()
            ->join('users', 'users.id', '=', 'sales_orders.user_id')
            ->groupBy('users.id', 'users.name')
            ->select('users.id as id', 'users.name as name')
            ->selectRaw('COUNT(*) as orders_count')
            ->selectRaw('SUM(sales_orders.amount_total) as revenue')
            ->orderByDesc('revenue')
            ->limit(10);
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('name')
                ->label(__('sales::filament/widgets/sales-dashboard.top-sales-persons.columns.name')),
            Tables\Columns\TextColumn::make('orders_count')
                ->label(__('sales::filament/widgets/sales-dashboard.top-sales-persons.columns.orders'))
                ->numeric(),
            Tables\Columns\TextColumn::make('revenue')
                ->label(__('sales::filament/widgets/sales-dashboard.top-sales-persons.columns.revenue'))
                ->formatStateUsing(fn ($state) => money($state ?? 0)),
        ];
    }
}
