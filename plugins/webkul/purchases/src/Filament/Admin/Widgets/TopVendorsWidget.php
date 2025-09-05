<?php

namespace Webkul\Purchase\Filament\Admin\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Webkul\Purchase\Models\PurchaseOrder;

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
                    ->money('INR'),
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

        return $query
            ->groupBy('partners_partners.id', 'partners_partners.name')
            ->orderByDesc('total_purchased')
            ->limit(10);
    }
}
