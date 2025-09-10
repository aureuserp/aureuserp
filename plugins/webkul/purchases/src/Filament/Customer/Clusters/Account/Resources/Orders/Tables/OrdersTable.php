<?php

namespace Webkul\Purchase\Filament\Customer\Clusters\Account\Resources\Orders\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Webkul\Purchase\Models\Order;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('purchases::filament/customer/clusters/account/resources/order.table.columns.reference'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('approved_at')
                    ->label(__('purchases::filament/customer/clusters/account/resources/order.table.columns.confirmation-date'))
                    ->sortable()
                    ->placeholder('—'),
                TextColumn::make('invoice_status')
                    ->label(__('purchases::filament/customer/clusters/account/resources/order.table.columns.status'))
                    ->sortable()
                    ->badge()
                    ->toggleable(),
                TextColumn::make('total_amount')
                    ->label(__('purchases::filament/customer/clusters/account/resources/order.table.columns.total-amount'))
                    ->sortable()
                    ->money(fn (Order $record) => $record->currency->code),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->modifyQueryUsing(function (Builder $query) {
                $query->where('partner_id', Auth::guard('customer')->id());
            });
    }
}
