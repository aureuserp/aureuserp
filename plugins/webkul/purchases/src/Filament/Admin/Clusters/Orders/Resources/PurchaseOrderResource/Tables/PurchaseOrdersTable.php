<?php

namespace Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\PurchaseOrderResource\Tables;

use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Purchase\Enums\OrderState;

class PurchaseOrdersTable
{
    /**
     * Receives the already-resolved parent table. The caller must build it with
     * parent::table() so late static binding stays on the calling resource --
     * OrderResource::table() resolves custom columns and permissions from static::class.
     */
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('state', [OrderState::PURCHASE, OrderState::DONE]));
    }
}
