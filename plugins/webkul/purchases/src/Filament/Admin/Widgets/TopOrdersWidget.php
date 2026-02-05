<?php

namespace Webkul\Purchase\Filament\Admin\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Webkul\Purchase\Models\PurchaseOrder;

class TopOrdersWidget extends BaseWidget
{
    use InteractsWithPageFilters;

    protected static ?string $pollingInterval = '15s';

    protected static bool $isLazy = false;

    protected static ?string $heading = 'Top Orders';

    public function getColumnSpan(): int|string
    {
        return 'full';
    }

    public function getTableRecordKey($record): string
    {
        return (string) $record->id;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getFilteredQuery())
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Order Number'),

                Tables\Columns\TextColumn::make('partner.name')
                    ->label('Vendor'),

                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Total Amount')
                    ->money(fn (PurchaseOrder $record) => $record->currency?->name),

                Tables\Columns\TextColumn::make('ordered_at')
                    ->label('Ordered At')
                    ->date(),
            ])
            ->paginated(false);
    }

    protected function getFilteredQuery(): Builder
    {
        $query = PurchaseOrder::with('partner');

        if (! empty($this->filters['start_date'])) {
            $query->whereDate('ordered_at', '>=', Carbon::parse($this->filters['start_date']));
        }

        if (! empty($this->filters['end_date'])) {
            $query->whereDate('ordered_at', '<=', Carbon::parse($this->filters['end_date']));
        }

        return $query->orderByDesc('total_amount')->limit(10);
    }
}
