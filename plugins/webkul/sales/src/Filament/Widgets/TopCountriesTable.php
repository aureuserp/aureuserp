<?php

namespace Webkul\Sale\Filament\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Sale\Filament\Widgets\Concerns\HasSaleDashboardFilters;

class TopCountriesTable extends TableWidget
{
    use HasSaleDashboardFilters, HasWidgetShield;

    protected static ?int $sort = 5;

    protected static bool $isLazy = false;

    public function table(Table $table): Table
    {
        return $table->defaultKeySort(false);
    }

    protected function getTableHeading(): string|Htmlable|null
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
            ->leftJoin('partners_partners', 'partners_partners.id', '=', 'sales_orders.partner_id')
            ->leftJoin('countries', 'countries.id', '=', 'partners_partners.country_id')
            ->groupBy('countries.id', 'countries.name')
            ->selectRaw('COALESCE(countries.id, 0) as id')
            ->addSelect('countries.name as name')
            ->selectRaw('COUNT(*) as orders_count')
            ->selectRaw('SUM(sales_orders.amount_total) as revenue')
            ->havingRaw('SUM(sales_orders.amount_total) > 0')
            ->orderByDesc('revenue')
            ->limit(5);
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('name')
                ->label(__('sales::filament/widgets/sales-dashboard.top-countries.columns.name'))
                ->placeholder(__('sales::filament/widgets/sales-dashboard.unknown')),
            Tables\Columns\TextColumn::make('orders_count')
                ->label(__('sales::filament/widgets/sales-dashboard.top-countries.columns.orders'))
                ->numeric(),
            Tables\Columns\TextColumn::make('revenue')
                ->label(__('sales::filament/widgets/sales-dashboard.top-countries.columns.revenue'))
                ->formatStateUsing(fn ($state) => money($state ?? 0, current_company()?->currency?->name)),
        ];
    }
}
