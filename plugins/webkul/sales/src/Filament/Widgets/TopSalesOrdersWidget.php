<?php

namespace Webkul\Sale\Filament\Widgets;

use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables;
use Webkul\Sale\Models\Order;

class TopSalesOrdersWidget extends BaseWidget
{
    protected static ?string $heading = 'Top Sales Orders by Revenue';

    protected function getTableQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return Order::query()
            ->where('state', 'sale')
            ->orderByDesc('amount_total')
            ->limit(10);
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('name')->label('Order'),
            Tables\Columns\TextColumn::make('partner.name')->label('Customer'),
            Tables\Columns\TextColumn::make('amount_total')->label('Revenue')->money('USD', true),
            Tables\Columns\TextColumn::make('date_order')->label('Date')->date(),
        ];
    }
}