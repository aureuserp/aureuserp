<?php

namespace Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources\OrderResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Webkul\PointOfSale\Models\Payment;

class PaymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'payments';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('point-of-sale::filament/admin/clusters/orders/resources/order/relation-managers/payments.title');
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('id')
            ->columns([
                TextColumn::make('paymentMethod.name')
                    ->label(__('point-of-sale::filament/admin/clusters/orders/resources/order/relation-managers/payments.table.columns.payment-method'))
                    ->searchable(),
                TextColumn::make('amount')
                    ->label(__('point-of-sale::filament/admin/clusters/orders/resources/order/relation-managers/payments.table.columns.amount'))
                    ->money(fn (Payment $record): ?string => $record->order?->currency?->name),
                IconColumn::make('is_change')
                    ->label(__('point-of-sale::filament/admin/clusters/orders/resources/order/relation-managers/payments.table.columns.is-change'))
                    ->boolean(),
                TextColumn::make('terminal_status')
                    ->label(__('point-of-sale::filament/admin/clusters/orders/resources/order/relation-managers/payments.table.columns.terminal-status'))
                    ->badge()
                    ->placeholder('—'),
                TextColumn::make('transaction_reference')
                    ->label(__('point-of-sale::filament/admin/clusters/orders/resources/order/relation-managers/payments.table.columns.transaction-reference'))
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('paid_at')
                    ->label(__('point-of-sale::filament/admin/clusters/orders/resources/order/relation-managers/payments.table.columns.paid-at'))
                    ->dateTime()
                    ->placeholder('—'),
            ]);
    }
}
