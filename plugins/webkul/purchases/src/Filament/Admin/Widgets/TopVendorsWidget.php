<?php

namespace Webkul\Purchase\Filament\Admin\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Webkul\Purchase\Models\PurchaseOrder;
use Webkul\Support\Models\Currency;

class TopVendorsWidget extends BaseWidget
{
    use InteractsWithPageFilters;

    protected static ?string $pollingInterval = '15s';

    protected static bool $isLazy = false;

    protected static ?string $heading = 'Top Vendors';

    public function getTableRecordKey($record): string
    {
        return (string) $record->partner_id;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getFilteredQuery())
            ->columns([
                Tables\Columns\TextColumn::make('vendor_name')
                    ->label('Vendor'),

                Tables\Columns\TextColumn::make('orders_count')
                    ->label('Orders'),

                Tables\Columns\TextColumn::make('total_purchased')
                    ->label('Total Purchased')
                    ->money($this->getActiveCurrency(), true),
            ])
            ->paginated(false);
    }

    protected function getFilteredQuery(): Builder
    {
        $query = PurchaseOrder::query()
            ->selectRaw('partners_partners.id as partner_id, partners_partners.name as vendor_name, COUNT(purchases_orders.id) as orders_count, SUM(purchases_orders.total_amount) as total_purchased')
            ->join('partners_partners', 'purchases_orders.partner_id', '=', 'partners_partners.id');

        if (! empty($this->filters['start_date'])) {
            $query->whereDate('purchases_orders.ordered_at', '>=', Carbon::parse($this->filters['start_date']));
        }

        if (! empty($this->filters['end_date'])) {
            $query->whereDate('purchases_orders.ordered_at', '<=', Carbon::parse($this->filters['end_date']));
        }

        if (! empty($this->filters['country_id'])) {
            $query->whereIn('partners_partners.country_id', (array) $this->filters['country_id']);
        }

        if (! empty($this->filters['product_id'])) {
            $query->whereExists(function ($subQuery) {
                $subQuery->selectRaw('1')
                    ->from('purchases_order_lines')
                    ->whereColumn('purchases_order_lines.order_id', 'purchases_orders.id')
                    ->whereIn('purchases_order_lines.product_id', (array) $this->filters['product_id']);
            });
        }

        if (! empty($this->filters['partner_id'])) {
            $query->whereIn('partners_partners.id', (array) $this->filters['partner_id']);
        }

        if (! empty($this->filters['category_id'])) {
            $query->whereExists(function ($subQuery) {
                $subQuery->selectRaw('1')
                    ->from('purchases_order_lines')
                    ->join('products_products', 'purchases_order_lines.product_id', '=', 'products_products.id')
                    ->whereColumn('purchases_order_lines.order_id', 'purchases_orders.id')
                    ->whereIn('products_products.category_id', (array) $this->filters['category_id']);
            });
        }

        if (! empty($this->filters['buyer_id'])) {
            $query->whereIn('purchases_orders.user_id', (array) $this->filters['buyer_id']);
        }

        if (! empty($this->filters['state'])) {
            $query->whereIn('purchases_orders.state', (array) $this->filters['state']);
        }

        $query->where('partners_partners.supplier_rank', '>', 0);

        return $query
            ->groupBy('partners_partners.id', 'partners_partners.name')
            ->orderByDesc('total_purchased')
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
