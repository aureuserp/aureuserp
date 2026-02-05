<?php

namespace Webkul\Purchase\Filament\Admin\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Purchase\Models\OrderLine;

class TopPurchasedProductsWidget extends BaseWidget
{
    use InteractsWithPageFilters;

    protected static ?string $pollingInterval = '15s';

    protected static bool $isLazy = false;

    protected static ?string $heading = 'Top Purchased Products';

    public function getTableRecordKey($record): string
    {
        return (string) $record->product_id;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getFilteredQuery())
            ->columns([
                Tables\Columns\TextColumn::make('product.name')
                    ->label('Product'),

                Tables\Columns\TextColumn::make('total_quantity')
                    ->label('Total Quantity'),

                Tables\Columns\TextColumn::make('total_value')
                    ->label('Total Value')
                    ->money('INR'),
            ])
            ->paginated(false);
    }

    protected function getFilteredQuery(): Builder
    {
        $query = OrderLine::query()
            ->with('product')
            ->select('product_id')
            ->selectRaw('SUM(product_qty) as total_quantity')
            ->selectRaw('SUM(price_total) as total_value')
            ->groupBy('product_id')
            ->orderByDesc('total_quantity')
            ->limit(10);

        if (! empty($this->filters['start_date'])) {
            $query->whereHas('order', function ($q) {
                $q->whereDate('ordered_at', '>=', $this->filters['start_date']);
            });
        }

        if (! empty($this->filters['end_date'])) {
            $query->whereHas('order', function ($q) {
                $q->whereDate('ordered_at', '<=', $this->filters['end_date']);
            });
        }

        return $query;
    }
}
