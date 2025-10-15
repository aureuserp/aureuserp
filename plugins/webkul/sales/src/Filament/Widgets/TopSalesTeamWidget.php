<?php

namespace Webkul\Sale\Filament\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Webkul\Sale\Enums\OrderState;
use Webkul\Sale\Models\Order;
use Webkul\Support\Models\Currency;

class TopSalesTeamWidget extends BaseWidget
{
    use HasWidgetShield,InteractsWithPageFilters;

    protected static ?string $pollingInterval = '15s';

    public function getHeading(): ?string
    {
        return __('sales::filament/pages/sales-dashboard.widgets.top-sales-teams.heading');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->applyFilters($this->baseQuery()))
            ->defaultPaginationPageOption(5)
            ->columns($this->getTableColumns());
    }

    protected function baseQuery(): Builder
    {
        return Order::query()
            ->where('sales_orders.state', OrderState::SALE)
            ->whereHas('orderLines')
            ->whereHas('team')
            ->with('team')
            ->select(
                'team_id',
                DB::raw('COUNT(DISTINCT sales_orders.id) as total_orders'),
                DB::raw('SUM(sales_order_lines.price_total) as total_revenue')
            )
            ->join('sales_order_lines', 'sales_orders.id', '=', 'sales_order_lines.order_id')
            ->groupBy('team_id')
            ->orderByDesc('total_revenue');
    }

    protected function applyFilters(Builder $query): Builder
    {
        $filters = $this->filters;

        if (! empty($filters['start_date'])) {
            $query->whereDate('date_order', '>=', $filters['start_date']);
        }
        if (! empty($filters['end_date'])) {
            $query->whereDate('date_order', '<=', $filters['end_date']);
        }

        if (! empty($filters['salesperson_id'])) {
            $query->whereIn('user_id', (array) $filters['salesperson_id']);
        }

        if (! empty($filters['country_id'])) {
            $query->whereHas('partner', fn ($q) => $q->whereIn('country_id', (array) $filters['country_id']));
        }

        if (! empty($filters['product_id'])) {
            $query->whereHas('orderLines', fn ($q) => $q->whereIn('product_id', (array) $filters['product_id']));
        }

        if (! empty($filters['category_id'])) {
            $query->whereHas('orderLines.product.category', fn ($q) => $q->whereIn('category_id', (array) $filters['category_id']));
        }

        if (! empty($filters['salesteam_id'])) {
            $query->whereIn('team_id', (array) $filters['salesteam_id']);
        }

        return $query;
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('team.name')
                ->label(__('sales::filament/pages/sales-dashboard.widgets.top-sales-teams.column.sales_team'))
                ->sortable()
                ->default('—'),

            Tables\Columns\TextColumn::make('total_orders')
                ->label(__('sales::filament/pages/sales-dashboard.widgets.top-sales-teams.column.total_orders'))
                ->sortable(),

            Tables\Columns\TextColumn::make('total_revenue')
                ->label(__('sales::filament/pages/sales-dashboard.widgets.top-sales-teams.column.total_revenue'))
                ->money($this->getActiveCurrency(), true)
                ->sortable(),
        ];
    }

    protected function getActiveCurrency(): ?string
    {
        return Currency::where('active', true)->value('name');
    }

    public function getTableRecordKey($record): string
    {
        return (string) $record->team_id;
    }
}
