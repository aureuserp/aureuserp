<?php

namespace Webkul\Sale\Filament\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Sale\Filament\Widgets\Concerns\HasSaleDashboardFilters;
use Webkul\Security\Filament\Resources\UserResource;

class TopSalesPersonsTable extends TableWidget
{
    use HasSaleDashboardFilters, HasWidgetShield;

    protected static ?int $sort = 10;

    protected static bool $isLazy = false;

    public function table(Table $table): Table
    {
        return $table
            ->defaultKeySort(false)
            ->recordUrl(fn ($record): ?string => $record->id && UserResource::canAccess()
                ? UserResource::getUrl('view', ['record' => $record->id])
                : null);
    }

    protected function getTableHeading(): string|Htmlable|null
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
            ->leftJoin('users', 'users.id', '=', 'sales_orders.user_id')
            ->groupBy('users.id', 'users.name')
            ->selectRaw('COALESCE(users.id, 0) as id')
            ->addSelect('users.name as name')
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
                ->label(__('sales::filament/widgets/sales-dashboard.top-sales-persons.columns.name'))
                ->placeholder(__('sales::filament/widgets/sales-dashboard.unknown')),
            Tables\Columns\TextColumn::make('orders_count')
                ->label(__('sales::filament/widgets/sales-dashboard.top-sales-persons.columns.orders'))
                ->numeric(),
            Tables\Columns\TextColumn::make('revenue')
                ->label(__('sales::filament/widgets/sales-dashboard.top-sales-persons.columns.revenue'))
                ->formatStateUsing(fn ($state) => money($state ?? 0, current_company()?->currency?->name)),
        ];
    }
}
