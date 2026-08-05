<?php

namespace Webkul\Sale\Filament\Widgets;

use Filament\Tables;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Sale\Filament\Widgets\Concerns\HasSaleDashboardFilters;

class TopCountriesTable extends TableWidget
{
    use HasSaleDashboardFilters;

    protected static ?int $sort = 5;

    public function getHeading(): ?string
    {
        return __('sales::filament/widgets/sales-dashboard.top-countries.heading');
    }

    protected function isTablePaginationEnabled(): bool
    {
        return false;
    }

    protected function getTableQuery(): Builder
    {
        return $this->saleOrders()
            ->join('partners_partners', 'partners_partners.id', '=', 'sales_orders.partner_id')
            ->join('countries', 'countries.id', '=', 'partners_partners.country_id')
            ->groupBy('countries.id', 'countries.name')
            ->select('countries.id as id', 'countries.name as name')
            ->selectRaw('COUNT(*) as orders_count')
            ->selectRaw('SUM(sales_orders.amount_total) as revenue')
            ->orderByDesc('revenue')
            ->limit(10);
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('name')
                ->label(__('sales::filament/widgets/sales-dashboard.top-countries.columns.name')),
            Tables\Columns\TextColumn::make('orders_count')
                ->label(__('sales::filament/widgets/sales-dashboard.top-countries.columns.orders'))
                ->numeric(),
            Tables\Columns\TextColumn::make('revenue')
                ->label(__('sales::filament/widgets/sales-dashboard.top-countries.columns.revenue'))
                ->formatStateUsing(fn ($state) => money($state ?? 0)),
        ];
    }
}
