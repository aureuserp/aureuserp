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
use Webkul\Sale\Models\OrderLine;
use Webkul\Support\Models\Currency;

class TopSalesCountryWidget extends BaseWidget
{
    use HasWidgetShield, InteractsWithPageFilters;

    protected static ?string $pollingInterval = '15s';

    protected function getHeading(): ?string
    {
        return __('sales::filament/pages/sales-dashboard.widgets.top-sales-countries.heading');
    }

    public function table(Table $table): Table
    {
        $query = $this->applyFilters($this->baseQuery());

        return $table
            ->query($query)
            ->defaultPaginationPageOption(5)
            ->columns($this->getTableColumns());
    }

    protected function baseQuery(): Builder
    {
        $query = OrderLine::query()
            ->whereHas('order', fn ($q) => $q->where('state', OrderState::SALE))
            ->join('sales_orders', 'sales_orders.id', '=', 'sales_order_lines.order_id')
            ->join('partners_partners', 'partners_partners.id', '=', 'sales_orders.partner_id')
            ->join('countries', 'countries.id', '=', 'partners_partners.country_id')
            ->select(
                'partners_partners.country_id',
                'countries.name as country_name',
                DB::raw('SUM(sales_order_lines.product_uom_qty) as total_products'),
                DB::raw('SUM(sales_order_lines.price_total) as total_revenue')
            )
            ->groupBy('partners_partners.country_id', 'countries.name')
            ->orderByDesc('total_revenue');

        return $query;
    }

    protected function applyFilters(Builder $query): Builder
    {
        $filters = $this->filters ?? [];

        $query->when(! empty($filters['start_date']), function ($query) use ($filters) {
            $query->whereHas('order', fn ($q) => $q->whereDate('date_order', '>=', $filters['start_date']));
        });

        $query->when(! empty($filters['end_date']), function ($query) use ($filters) {
            $query->whereHas('order', fn ($q) => $q->whereDate('date_order', '<=', $filters['end_date']));
        });

        $query->when(! empty($filters['salesperson_id']), function ($query) use ($filters) {
            $query->whereHas('order', fn ($q) => $q->whereIn('user_id', (array) $filters['salesperson_id']));
        });

        $query->when(! empty($filters['customer_id']), function ($query) use ($filters) {
            $query->whereHas('order', fn ($q) => $q->whereIn('partner_id', (array) $filters['customer_id']));
        });

        $query->when(! empty($filters['salesperson_id']), function ($query) use ($filters) {
            $query->whereHas('order', fn ($q) => $q->whereIn('user_id', (array) $filters['salesperson_id']));
        });

        $query->when(! empty($filters['customer_id']), function ($query) use ($filters) {
            $query->whereHas('order', fn ($q) => $q->whereIn('partner_id', (array) $filters['customer_id']));
        });

        $query->when(! empty($filters['salesteam_id']), function ($query) use ($filters) {
            $query->whereHas('order', fn ($q) => $q->whereIn('team_id', (array) $filters['salesteam_id']));
        });

        return $query;
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('country_name')
                ->label(__('sales::filament/pages/sales-dashboard.widgets.top-sales-countries.column.country'))
                ->sortable(),
            Tables\Columns\TextColumn::make('total_products')
                ->label(__('sales::filament/pages/sales-dashboard.widgets.top-sales-countries.column.total_orders'))
                ->sortable(),

            Tables\Columns\TextColumn::make('total_revenue')
                ->label(__('sales::filament/pages/sales-dashboard.widgets.top-sales-countries.column.total_revenue'))
                ->money($this->getActiveCurrency(), true)
                ->sortable(),
        ];
    }

    protected function getActiveCurrency(): ?string
    {
        return Currency::where('active', true)->value('name') ?? 'USD';
    }

    public function getTableRecordKey($record): string
    {
        return (string) $record->country_id;
    }
}
