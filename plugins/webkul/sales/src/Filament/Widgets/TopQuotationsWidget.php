<?php

namespace Webkul\Sale\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Webkul\Sale\Enums\OrderState;
use Webkul\Sale\Models\Order;

class TopQuotationsWidget extends BaseWidget
{
    protected static ?string $heading = 'Top 10 Quotations by Revenue';

    public function table(Table $table): Table
    {
        $query = Order::where('state', OrderState::SALE)
            ->orderBy('amount_total', 'desc')
            ->limit(5);

        return $table
            ->defaultPaginationPageOption(5)
            ->query($query)
            ->columns([
                Tables\Columns\TextColumn::make('partner.name')
                    ->label(__('sales::filament/widgets/top-quotation.table-columns.customer-name')),
            ]);
    }
}
