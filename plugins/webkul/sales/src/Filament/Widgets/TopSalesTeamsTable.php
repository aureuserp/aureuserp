<?php

namespace Webkul\Sale\Filament\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\TeamResource;
use Webkul\Sale\Filament\Widgets\Concerns\HasSaleDashboardFilters;

class TopSalesTeamsTable extends TableWidget
{
    use HasSaleDashboardFilters, HasWidgetShield;

    protected static ?int $sort = 9;

    protected static bool $isLazy = false;

    public function table(Table $table): Table
    {
        return $table
            ->defaultKeySort(false)
            ->recordUrl(fn ($record): ?string => $record->id && TeamResource::canAccess()
                ? TeamResource::getUrl('view', ['record' => $record->id])
                : null);
    }

    protected function getTableHeading(): string|Htmlable|null
    {
        return __('sales::filament/widgets/sales-dashboard.top-sales-teams.heading');
    }

    protected function isTablePaginationEnabled(): bool
    {
        return false;
    }

    protected function getTableQuery(): Builder
    {
        return $this->saleOrders()
            ->leftJoin('sales_teams', 'sales_teams.id', '=', 'sales_orders.team_id')
            ->groupBy('sales_teams.id', 'sales_teams.name')
            ->selectRaw('COALESCE(sales_teams.id, 0) as id')
            ->addSelect('sales_teams.name as name')
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
                ->label(__('sales::filament/widgets/sales-dashboard.top-sales-teams.columns.name'))
                ->placeholder(__('sales::filament/widgets/sales-dashboard.unknown')),
            Tables\Columns\TextColumn::make('orders_count')
                ->label(__('sales::filament/widgets/sales-dashboard.top-sales-teams.columns.orders'))
                ->numeric(),
            Tables\Columns\TextColumn::make('revenue')
                ->label(__('sales::filament/widgets/sales-dashboard.top-sales-teams.columns.revenue'))
                ->formatStateUsing(fn ($state) => money($state ?? 0, current_company()?->currency?->name)),
        ];
    }
}
