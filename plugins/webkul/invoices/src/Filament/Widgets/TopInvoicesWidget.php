<?php

namespace Webkul\Invoice\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Account\Enums\PaymentState;
use Webkul\Invoice\Models\Invoice;

class TopInvoicesWidget extends BaseWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = 'Top Invoices';

    protected static bool $isLazy = false;

    public function getColumnSpan(): int|string
    {
        return 'full';
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getFilteredQuery())
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Number'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Invoice Date')
                    ->date('M d, Y'),

                Tables\Columns\TextColumn::make('invoiceUser.name')
                    ->label('Salesperson'),

                Tables\Columns\TextColumn::make('amount_total')
                    ->label('Total')
                    ->money('USD')
                    ->alignRight(),

                Tables\Columns\TextColumn::make('payment_state')
                    ->label('Payment State')
                    ->color(fn (PaymentState $state) => $state->getColor())
                    ->icon(fn (PaymentState $state) => $state->getIcon())
                    ->formatStateUsing(fn (PaymentState $state) => $state->getLabel())
                    ->badge(),
            ])
            ->defaultSort('amount_total', 'desc')
            ->paginated(false);
    }

    protected function getFilteredQuery(): Builder
    {
        $query = Invoice::query();

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

        return $query->with('invoiceUser')
            ->orderByDesc('amount_total')
            ->limit(10);
    }
}
