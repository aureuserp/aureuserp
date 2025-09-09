<?php

namespace Webkul\Sale\Filament\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\DB;
use Webkul\Sale\Enums\OrderState;
use Webkul\Sale\Models\OrderLine;
use Webkul\Support\Models\Currency;

class TopProductsWidget extends BaseWidget
{
    use HasWidgetShield, InteractsWithPageFilters;

    protected int|string|array $columnSpan = 'full';

    protected function getHeading(): ?string
    {
        return __('sales::filament/widgets/top-products.heading');
    }

    public function table(Table $table): Table
    {
        $filters = $this->filters;

        $query = OrderLine::query()
            ->where('state', OrderState::SALE)
            ->when(
                ! empty($filters['start_date']),
                fn ($q) => $q->whereDate('create_date', '>=', $filters['start_date'])
            )
            ->when(
                ! empty($filters['end_date']),
                fn ($q) => $q->whereDate('create_date', '<=', $filters['end_date'])
            )
            ->when(
                ! empty($filters['salesperson_id']),
                fn ($q) => $q->where('salesman_id', $filters['salesperson_id'])
            )
            ->select(
                'product_id',
                DB::raw('SUM(product_uom_qty) as total_qty'),
                DB::raw('SUM(price_total) as total_revenue')
            )
            ->with('product') // eager load product
            ->groupBy('product_id')
            ->orderByDesc('total_qty'); // show most sold first

        return $table
            ->query($query)
            ->defaultPaginationPageOption(5)
            ->columns([
                Tables\Columns\TextColumn::make('product.name')
                    ->label(__('sales::filament/widgets/top-products.tables.product'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('total_qty')
                    ->label(__('sales::filament/widgets/top-products.tables.quantity-sold'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_revenue')
                    ->label(__('sales::filament/widgets/top-products.tables.revenue'))
                    ->money($this->getActiveCurrency(), true)
                    ->sortable(),
            ]);
    }

    protected function getActiveCurrency()
    {
        return Currency::where('active', true)->value('name') ?? null;
    }
}
