<?php

namespace Webkul\Purchase\Filament\Customer\Clusters\Account\Resources\Quotations\Pages;

use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Purchase\Enums\OrderState;
use Webkul\Purchase\Filament\Customer\Clusters\Account\Resources\Quotations\QuotationResource;

class ListQuotations extends ListRecords
{
    protected static string $resource = QuotationResource::class;

    public function table(Table $table): Table
    {
        return QuotationResource::table($table)
            ->modifyQueryUsing(fn (Builder $query) => $query->where('state', OrderState::SENT));
    }
}
