<?php

namespace Webkul\Invoice\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Enums\PaymentState;
use Webkul\Invoice\Models\Invoice;

class TopBillsWidget extends BaseWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = 'Top Bills';

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
                    ->label('Bill Number'),

                Tables\Columns\TextColumn::make('partner.name')
                    ->label('Vendor'),

                Tables\Columns\TextColumn::make('invoice_date')
                    ->label('Bill Date')
                    ->date('M d, Y'),

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
        $query = Invoice::query()->where('move_type', MoveType::IN_INVOICE);

        if (! empty($this->filters['start_date'])) {
            $query->whereDate('invoice_date', '>=', $this->filters['start_date']);
        }

        if (! empty($this->filters['end_date'])) {
            $query->whereDate('invoice_date', '<=', $this->filters['end_date']);
        }

        if (! empty($this->filters['salesperson_id'])) {
            $query->whereIn('invoice_user_id', (array) $this->filters['salesperson_id']);
        }

        if (! empty($this->filters['product_id'])) {
            $query->whereHas('lines', function ($lineQuery) {
                $lineQuery->where('display_type', 'product')
                    ->whereIn('product_id', (array) $this->filters['product_id']);
            });
        }

        if (! empty($this->filters['category_id'])) {
            $query->whereHas('lines.product', function ($productQuery) {
                $productQuery->whereIn('category_id', (array) $this->filters['category_id']);
            });
        }

        if (! empty($this->filters['vendor_id'])) {
            $query->whereIn('partner_id', (array) $this->filters['vendor_id']);
        }

        if (! empty($this->filters['payment_state'])) {
            $query->whereIn('payment_state', (array) $this->filters['payment_state']);
        }

        return $query
            ->with('partner')
            ->orderByDesc('amount_total')
            ->limit(10);
    }
}
