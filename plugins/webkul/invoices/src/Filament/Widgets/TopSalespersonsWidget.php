<?php

namespace Webkul\Invoice\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Invoice\Models\Invoice;

class TopSalespersonsWidget extends BaseWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = 'Top Salespersons';

    protected static bool $isLazy = false;

    public function getTableRecordKey($record): string
    {
        return (string) $record->invoice_user_id;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getFilteredQuery())
            ->columns([
                Tables\Columns\TextColumn::make('invoiceUser.name')
                    ->label('Salesperson'),

                Tables\Columns\TextColumn::make('invoices_count')
                    ->label('Invoices'),

                Tables\Columns\TextColumn::make('total_billed')
                    ->label('Total Billed')
                    ->money('USD'),
            ])
            ->paginated(false)
            ->defaultSort('total_billed', 'desc');
    }

    protected function getFilteredQuery(): Builder
    {
        $query = Invoice::query()
            ->whereNotNull('invoice_user_id')
            ->selectRaw('invoice_user_id, SUM(amount_total) as total_billed, COUNT(*) as invoices_count')
            ->with('invoiceUser')
            ->groupBy('invoice_user_id');

        if (! empty($this->filters['start_date'])) {
            $query->whereDate('created_at', '>=', $this->filters['start_date']);
        }

        if (! empty($this->filters['end_date'])) {
            $query->whereDate('created_at', '<=', $this->filters['end_date']);
        }

        if (! empty($this->filters['salesperson_id'])) {
            $query->where('invoice_user_id', $this->filters['salesperson_id']);
        }

        if (! empty($this->filters['product_id'])) {
            $query->whereHas('lines', function ($q) {
                $q->where('display_type', 'product')
                    ->where('product_id', $this->filters['product_id']);
            });
        }

        return $query->orderByDesc('total_billed')->limit(10);
    }
}
