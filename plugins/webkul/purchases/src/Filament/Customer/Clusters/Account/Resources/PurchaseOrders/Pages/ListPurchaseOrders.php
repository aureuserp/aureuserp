<?php

namespace Webkul\Purchase\Filament\Customer\Clusters\Account\Resources\PurchaseOrders\Pages;

use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Purchase\Enums\OrderState;
use Webkul\Purchase\Filament\Customer\Clusters\Account\Resources\PurchaseOrders\PurchaseOrderResource;

class ListPurchaseOrders extends ListRecords
{
    protected static string $resource = PurchaseOrderResource::class;

    public function table(Table $table): Table
    {
        return PurchaseOrderResource::table($table)
            ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('state', [OrderState::PURCHASE, OrderState::DONE, OrderState::CANCELED]));
    }
}
