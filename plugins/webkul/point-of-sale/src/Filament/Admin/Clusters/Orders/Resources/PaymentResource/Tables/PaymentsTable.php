<?php

namespace Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources\PaymentResource\Tables;

use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group as TableGroup;
use Filament\Tables\Table;
use Webkul\PointOfSale\Models\Payment;

class PaymentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->reorderableColumns()
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('order.name')
                    ->label(__('point-of-sale::filament/admin/clusters/orders/resources/payment.table.columns.order'))
                    ->placeholder('—')
                    ->searchable(),
                TextColumn::make('session.name')
                    ->label(__('point-of-sale::filament/admin/clusters/orders/resources/payment.table.columns.session'))
                    ->placeholder('—')
                    ->searchable(),
                TextColumn::make('paymentMethod.name')
                    ->label(__('point-of-sale::filament/admin/clusters/orders/resources/payment.table.columns.payment-method'))
                    ->searchable(),
                TextColumn::make('amount')
                    ->label(__('point-of-sale::filament/admin/clusters/orders/resources/payment.table.columns.amount'))
                    ->money(fn (Payment $record): ?string => $record->order?->currency?->name)
                    ->sortable(),
                TextColumn::make('partner.name')
                    ->label(__('point-of-sale::filament/admin/clusters/orders/resources/payment.table.columns.partner'))
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('is_change')
                    ->label(__('point-of-sale::filament/admin/clusters/orders/resources/payment.table.columns.is-change'))
                    ->boolean(),
                TextColumn::make('terminal_status')
                    ->label(__('point-of-sale::filament/admin/clusters/orders/resources/payment.table.columns.terminal-status'))
                    ->badge()
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('paid_at')
                    ->label(__('point-of-sale::filament/admin/clusters/orders/resources/payment.table.columns.paid-at'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->groups([
                TableGroup::make('paymentMethod.name')
                    ->label(__('point-of-sale::filament/admin/clusters/orders/resources/payment.table.groups.payment-method')),
                TableGroup::make('session.name')
                    ->label(__('point-of-sale::filament/admin/clusters/orders/resources/payment.table.groups.session')),
            ])
            ->filters([
                SelectFilter::make('payment_method_id')
                    ->label(__('point-of-sale::filament/admin/clusters/orders/resources/payment.table.filters.payment-method'))
                    ->relationship('paymentMethod', 'name')
                    ->searchable()
                    ->preload()
                    ->native(false),
                SelectFilter::make('session_id')
                    ->label(__('point-of-sale::filament/admin/clusters/orders/resources/payment.table.filters.session'))
                    ->relationship('session', 'name')
                    ->searchable()
                    ->preload()
                    ->native(false),
            ]);
    }
}
