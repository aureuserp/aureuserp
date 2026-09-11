<?php

namespace Webkul\Invoice\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Webkul\Account\Enums\MoveType;
use Webkul\Invoice\Models\Invoice;
use Webkul\Support\Models\Currency;

class TopCustomersWidget extends BaseWidget
{
    use InteractsWithPageFilters;

    protected static ?string $pollingInterval = '15s';

    protected static bool $isLazy = false;

    protected static ?string $heading = 'Top Customers';

    public function getTableRecordKey($record): string
    {
        return (string) $record->partner_id;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getFilteredQuery())
            ->columns([
                Tables\Columns\TextColumn::make('customer_name')
                    ->label('Customer'),

                Tables\Columns\TextColumn::make('invoice_count')
                    ->label('Invoices'),

                Tables\Columns\TextColumn::make('total_billed')
                    ->label('Total Billed')
                    ->money($this->getActiveCurrency(), true),

            ])
            ->paginated(false);
    }

    protected function getFilteredQuery(): Builder
    {
        $query = Invoice::query()
            ->selectRaw('partners_partners.id as partner_id, partners_partners.name as customer_name, COUNT(accounts_account_moves.id) as invoice_count, SUM(accounts_account_moves.amount_total) as total_billed')
            ->join('partners_partners', 'accounts_account_moves.partner_id', '=', 'partners_partners.id');

        if (! empty($this->filters['start_date'])) {
            $query->whereDate('accounts_account_moves.invoice_date', '>=', Carbon::parse($this->filters['start_date']));
        }

        if (! empty($this->filters['end_date'])) {
            $query->whereDate('accounts_account_moves.invoice_date', '<=', Carbon::parse($this->filters['end_date']));
        }

        if (! empty($this->filters['salesperson_id'])) {
            $query->whereIn('accounts_account_moves.invoice_user_id', (array) $this->filters['salesperson_id']);
        }

        if (! empty($this->filters['product_id'])) {
            $query->whereHas('lines', function ($q) {
                $q->where('display_type', 'product')
                    ->whereIn('product_id', (array) $this->filters['product_id']);
            });
        }

        if (! empty($this->filters['category_id'])) {
            $query->whereHas('lines.product', function ($q) {
                $q->whereIn('category_id', (array) $this->filters['category_id']);
            });
        }

        if (! empty($this->filters['customer_id'])) {
            $query->whereIn('accounts_account_moves.partner_id', (array) $this->filters['customer_id']);
        }

        if (! empty($this->filters['payment_state'])) {
            $query->whereIn('accounts_account_moves.payment_state', (array) $this->filters['payment_state']);
        }

        $query->where('accounts_account_moves.move_type', MoveType::OUT_INVOICE);

        $query->where('partners_partners.customer_rank', '>', 0);

        return $query
            ->groupBy('partners_partners.id', 'partners_partners.name')
            ->orderByDesc('total_billed')
            ->limit(10);
    }

    /**
     * 🔹 Get active currency for money formatting.
     */
    protected function getActiveCurrency(): ?string
    {
        return Currency::where('active', true)->value('name') ?? 'USD';
    }
}
