<?php

namespace Webkul\Purchase\Filament\Admin\Widgets;

use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Purchase\Filament\Admin\Widgets\Concerns\HasPurchaseDashboardFilters;
use Webkul\Purchase\Models\PurchaseOrder;

class TopOrdersWidget extends BaseWidget
{
    use HasPurchaseDashboardFilters, HasWidgetShield;

    protected static ?int $sort = 4;

    protected static bool $isLazy = true;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table->defaultKeySort(false);
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
            Tables\Columns\TextColumn::make('state')
                ->label(__('purchases::filament/admin/widgets/purchase-dashboard.status'))
                ->badge()
                ->formatStateUsing(fn ($state) => $state instanceof BackedEnum ? $state->getLabel() : $state)
                ->color(fn ($state): string => match ($state instanceof BackedEnum ? $state->value : $state) {
                    'purchase' => 'info',
                    'done'     => 'success',
                    'sent'     => 'warning',
                    'canceled' => 'danger',
                    default    => 'gray',
                }),
            Tables\Columns\TextColumn::make('ordered_at')
                ->label(__('purchases::filament/admin/widgets/purchase-dashboard.top-orders.columns.ordered-at'))
                ->date(),
            Tables\Columns\TextColumn::make('total_amount')
                ->label(__('purchases::filament/admin/widgets/purchase-dashboard.top-orders.columns.amount'))
                ->money(fn (PurchaseOrder $record) => $record->currency?->name ?? $this->dashboardCurrency())
                ->alignEnd(),
        ];
    }
}
