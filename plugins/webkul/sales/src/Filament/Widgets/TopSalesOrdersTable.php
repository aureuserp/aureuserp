<?php

namespace Webkul\Sale\Filament\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Sale\Filament\Clusters\Orders\Resources\OrderResource;
use Webkul\Sale\Filament\Widgets\Concerns\HasSaleDashboardFilters;

class TopSalesOrdersTable extends TableWidget
{
    use HasSaleDashboardFilters, HasWidgetShield;

    protected static ?int $sort = 4;

    protected static bool $isLazy = false;

    public function table(Table $table): Table
    {
        return $table
            ->defaultKeySort(false)
            ->recordUrl(fn ($record): ?string => OrderResource::canAccess()
                ? OrderResource::getUrl('view', ['record' => $record->id])
                : null);
    }

    protected function getTableHeading(): string|Htmlable|null
    {
        return __('sales::filament/widgets/sales-dashboard.top-sales-orders.heading');
    }

    protected function isTablePaginationEnabled(): bool
    {
        return false;
    }

    protected function getTableQuery(): Builder
    {
        return $this->saleOrders()
            ->with(['partner', 'currency'])
            ->orderByDesc('amount_total')
            ->limit(5);
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('name')
                ->label(__('sales::filament/widgets/sales-dashboard.top-sales-orders.columns.reference')),
            Tables\Columns\TextColumn::make('partner.name')
                ->label(__('sales::filament/widgets/sales-dashboard.top-sales-orders.columns.customer'))
                ->placeholder('-'),
            Tables\Columns\TextColumn::make('amount_total')
                ->label(__('sales::filament/widgets/sales-dashboard.top-sales-orders.columns.amount'))
                ->money(fn ($record) => $record->currency?->name),
        ];
    }
}
