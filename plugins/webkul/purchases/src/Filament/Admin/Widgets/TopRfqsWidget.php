<?php

namespace Webkul\Purchase\Filament\Admin\Widgets;

use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Purchase\Enums\OrderState;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\PurchaseOrderResource;
use Webkul\Purchase\Filament\Admin\Widgets\Concerns\HasPurchaseDashboardFilters;
use Webkul\Purchase\Models\PurchaseOrder;

class TopRfqsWidget extends BaseWidget
{
    use HasPurchaseDashboardFilters;

    protected static ?int $sort = 5;

    protected static bool $isLazy = true;

    public function table(Table $table): Table
    {
        return $table
            ->defaultKeySort(false)
            ->recordClasses('dashboard-table-row')
            ->recordUrl(fn ($record): ?string => PurchaseOrderResource::canAccess()
                ? PurchaseOrderResource::getUrl('view', ['record' => $record->id])
                : null);
    }

    protected function getTableHeading(): string|Htmlable|null
    {
        return __('purchases::filament/admin/widgets/purchase-dashboard.top-rfqs.heading');
    }

    protected function isTablePaginationEnabled(): bool
    {
        return false;
    }

    protected function getTableQuery(): Builder
    {
        $query = $this->purchaseOrders();

        return $query
            ->with(['partner', 'currency'])
            ->whereIn($query->qualifyColumn('state'), [OrderState::DRAFT->value, OrderState::SENT->value])
            ->where('total_amount', '>', 0)
            ->orderByDesc('total_amount')
            ->limit(5);
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('name')
                ->label(__('purchases::filament/admin/widgets/purchase-dashboard.top-rfqs.columns.reference'))
                ->weight(FontWeight::Bold)
                ->color('primary')
                ->description(fn ($record): string => $record->partner?->name ?: '—'),
            Tables\Columns\TextColumn::make('state')
                ->label(__('purchases::filament/admin/widgets/purchase-dashboard.top-rfqs.columns.status'))
                ->badge(),
            Tables\Columns\TextColumn::make('total_amount')
                ->label(__('purchases::filament/admin/widgets/purchase-dashboard.top-rfqs.columns.amount'))
                ->money(fn (PurchaseOrder $record) => $record->currency?->name ?? $this->dashboardCurrency())
                ->weight(FontWeight::Bold)
                ->alignEnd(),
        ];
    }
}
