<?php

namespace Webkul\Sale\Filament\Widgets;

use Filament\Tables;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Sale\Filament\Widgets\Concerns\HasSaleDashboardFilters;

class TopCustomersTable extends TableWidget
{
    use HasSaleDashboardFilters;

    protected static ?int $sort = 7;

    public function getHeading(): ?string
    {
        return __('sales::filament/widgets/sales-dashboard.top-customers.heading');
    }

    protected function isTablePaginationEnabled(): bool
    {
        return false;
    }

    protected function getTableQuery(): Builder
    {
        return $this->saleOrders()
            ->join('partners_partners', 'partners_partners.id', '=', 'sales_orders.partner_id')
            ->groupBy('partners_partners.id', 'partners_partners.name')
            ->select('partners_partners.id as id', 'partners_partners.name as name')
            ->selectRaw('COUNT(*) as orders_count')
            ->selectRaw('SUM(sales_orders.amount_total) as revenue')
            ->orderByDesc('revenue')
            ->limit(10);
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('name')
                ->label(__('sales::filament/widgets/sales-dashboard.top-customers.columns.name')),
            Tables\Columns\TextColumn::make('orders_count')
                ->label(__('sales::filament/widgets/sales-dashboard.top-customers.columns.orders'))
                ->numeric(),
            Tables\Columns\TextColumn::make('revenue')
                ->label(__('sales::filament/widgets/sales-dashboard.top-customers.columns.revenue'))
                ->formatStateUsing(fn ($state) => money($state ?? 0)),
        ];
    }
}
