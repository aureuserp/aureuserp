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
            ->selectRaw('product_id, SUM(price_total) as total_revenue, SUM(product_uom_qty) as total_qty')
            ->groupBy('product_id')
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
