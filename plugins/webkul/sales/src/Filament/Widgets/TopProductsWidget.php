<?php

namespace Webkul\Sale\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Webkul\Sale\Models\OrderLine;

class TopProductsWidget extends BaseWidget
{
    protected function getHeading(): ?string
    {
        return __('sales::filament/widgets/top-products.heading');
    }

    public function table(Table $table): Table
    {
        $query = OrderLine::query()
            ->selectRaw('
                    sales_order_lines.product_id, 
                    SUM(sales_order_lines.product_uom_qty) AS total_qty, 
                    SUM(sales_order_lines.price_total * sales_orders.currency_rate) AS total_revenue
                ')
            ->join('sales_orders', 'sales_order_lines.order_id', '=', 'sales_orders.id')
            ->where('sales_orders.state', 'confirm') // only confirmed quotations
            ->groupBy('sales_order_lines.product_id')
            ->orderByDesc('total_revenue')
            ->with('product')
            ->limit(10);

        return $table
            ->query($query)
            ->defaultPaginationPageOption(5)
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Product'),
                Tables\Columns\TextColumn::make('total_qty')->label('Quantity'),
                Tables\Columns\TextColumn::make('total_revenue')->label('Revenue')->money('USD', true),
            ]);
    }
}
