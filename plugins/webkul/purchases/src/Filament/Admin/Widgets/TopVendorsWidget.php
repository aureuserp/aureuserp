<?php

namespace Webkul\Purchase\Filament\Admin\Widgets;

use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Purchase\Filament\Admin\Widgets\Concerns\HasPurchaseDashboardFilters;

class TopVendorsWidget extends BaseWidget
{
    use HasPurchaseDashboardFilters;

    protected static ?int $sort = 7;

    protected static bool $isLazy = true;

    public function table(Table $table): Table
    {
        return $table
            ->defaultKeySort(false)
            ->recordClasses('dashboard-table-row');
    }

    protected function getTableHeading(): string|Htmlable|null
    {
        return __('purchases::filament/admin/widgets/purchase-dashboard.top-vendors.heading');
    }

    protected function isTablePaginationEnabled(): bool
    {
        return false;
    }

    protected function getTableQuery(): Builder
    {
        return $this->purchaseOrders()
            ->leftJoin('partners_partners', 'partners_partners.id', '=', 'purchases_orders.partner_id')
            ->groupBy('partners_partners.id', 'partners_partners.name')
            ->selectRaw('COALESCE(partners_partners.id, 0) as id')
            ->addSelect('partners_partners.name as name')
            ->selectRaw('COUNT(purchases_orders.id) as orders_count')
            ->selectRaw('SUM(purchases_orders.total_cc_amount) as total_spent')
            ->havingRaw('SUM(purchases_orders.total_cc_amount) > 0')
            ->orderByDesc('total_spent')
            ->limit(5);
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('name')
                ->label(__('purchases::filament/admin/widgets/purchase-dashboard.top-vendors.columns.vendor'))
                ->weight(FontWeight::Bold)
                ->color('primary')
                ->placeholder(__('purchases::filament/admin/widgets/purchase-dashboard.unknown')),
            Tables\Columns\TextColumn::make('orders_count')
                ->label(__('purchases::filament/admin/widgets/purchase-dashboard.top-vendors.columns.orders'))
                ->numeric()
                ->alignEnd(),
            Tables\Columns\TextColumn::make('total_spent')
                ->label(__('purchases::filament/admin/widgets/purchase-dashboard.top-vendors.columns.amount'))
                ->formatStateUsing(fn ($state) => money($state ?? 0, $this->dashboardCurrency()))
                ->weight(FontWeight::Bold)
                ->alignEnd(),
        ];
    }
}
