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

    public function table(Table $table): Table
    {
        $query = $this->applyFilters($this->baseQuery());

        return $table
            ->query($query)
            ->defaultPaginationPageOption(5)
            ->columns([
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
            ]);
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

        $query->when(! empty($filters['start_date']), function ($query) use ($filters) {
            $query->whereDate('date_order', '>=', $filters['start_date']);
        });
        $query->when(! empty($filters['end_date']), function ($query) use ($filters) {
            $query->whereDate('date_order', '<=', $filters['end_date']);
        });

        $query->when(! empty($filters['salesperson_id']), function ($query) use ($filters) {
            $query->whereIn('user_id', (array) $filters['salesperson_id']);
        });

        $query->when(! empty($filters['country_id']), function ($query) use ($filters) {
            $query->whereHas('partner', fn ($q) => $q->whereIn('country_id', (array) $filters['country_id']));
        });

        $query->when(! empty($filters['product_id']), function ($query) use ($filters) {
            $query->whereHas('orderLines', fn ($q) => $q->whereIn('product_id', (array) $filters['product_id']));
        });

        $query->when(! empty($filters['category_id']), function ($query) use ($filters) {
            $query->whereHas('orderLines.product.category', fn ($q) => $q->whereIn('category_id', (array) $filters['category_id']));
        });

        $query->when(! empty($filters['salesteam_id']), function ($query) use ($filters) {
            $query->whereIn('team_id', (array) $filters['salesteam_id']);
        });

        return $query;
    }

    protected function getActiveCurrency(): ?string
    {
        return Currency::where('active', true)->value('name');
    }

    public function getTableRecordKey($record): string
    {
        return (string) $record->team_id;
    }

    public function getHeading(): ?string
    {
        return __('sales::filament/pages/sales-dashboard.widgets.top-sales-teams.heading');
    }
}
