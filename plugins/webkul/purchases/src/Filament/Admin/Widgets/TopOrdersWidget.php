<?php

namespace Webkul\Purchase\Filament\Admin\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\PurchaseOrderResource;
use Webkul\Purchase\Filament\Admin\Widgets\Concerns\HasPurchaseDashboardFilters;
use Webkul\Purchase\Models\PurchaseOrder;

class TopOrdersWidget extends BaseWidget
{
    use HasPurchaseDashboardFilters, HasWidgetShield;

    protected static ?int $sort = 4;

    protected static bool $isLazy = true;

    public function table(Table $table): Table
    {
        return $table
            ->defaultKeySort(false)
            ->recordUrl(fn ($record): ?string => PurchaseOrderResource::canAccess()
                ? PurchaseOrderResource::getUrl('view', ['record' => $record->id])
                : null);
    }

    protected function getTableHeading(): string|Htmlable|null
    {
        return __('purchases::filament/admin/widgets/purchase-dashboard.top-orders.heading');
    }

    protected function isTablePaginationEnabled(): bool
    {
        return false;
    }

    protected function getTableQuery(): Builder
    {
        return $this->purchaseOrders()
            ->with(['partner', 'currency'])
            ->where('total_amount', '>', 0)
            ->orderByDesc('total_amount')
            ->limit(10);
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('name')
                ->label(__('purchases::filament/admin/widgets/purchase-dashboard.top-orders.columns.reference')),
            Tables\Columns\TextColumn::make('partner.name')
                ->label(__('purchases::filament/admin/widgets/purchase-dashboard.top-orders.columns.vendor'))
                ->placeholder('-')
                ->wrap(),
            Tables\Columns\TextColumn::make('receipt_status')
                ->label(__('purchases::filament/admin/widgets/purchase-dashboard.receipt-status'))
                ->badge()
                ->placeholder('-'),
            Tables\Columns\TextColumn::make('invoice_status')
                ->label(__('purchases::filament/admin/widgets/purchase-dashboard.top-orders.columns.billing-status'))
                ->badge()
                ->placeholder('-'),
            Tables\Columns\TextColumn::make('total_amount')
                ->label(__('purchases::filament/admin/widgets/purchase-dashboard.top-orders.columns.amount'))
                ->money(fn (PurchaseOrder $record) => $record->currency?->name ?? $this->dashboardCurrency())
                ->alignEnd(),
        ];
    }
}
