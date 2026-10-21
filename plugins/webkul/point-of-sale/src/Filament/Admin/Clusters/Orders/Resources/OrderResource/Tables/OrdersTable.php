<?php

namespace Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources\OrderResource\Tables;

use Filament\Actions\EditAction;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group as TableGroup;
use Filament\Tables\Table;
use Webkul\PointOfSale\Enums\OrderState;
use Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources\OrderResource;
use Webkul\PointOfSale\Models\Order;
use Webkul\Support\Models\Company;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->reorderableColumns()
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('name')
                    ->label(__('point-of-sale::filament/admin/clusters/orders/resources/order.table.columns.name'))
                    ->placeholder('—')
                    ->weight(FontWeight::Bold)
                    ->searchable(),
                TextColumn::make('session.name')
                    ->label(__('point-of-sale::filament/admin/clusters/orders/resources/order.table.columns.session'))
                    ->placeholder('—')
                    ->searchable(),
                TextColumn::make('ordered_at')
                    ->label(__('point-of-sale::filament/admin/clusters/orders/resources/order.table.columns.ordered-at'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('config.name')
                    ->label(__('point-of-sale::filament/admin/clusters/orders/resources/order.table.columns.config'))
                    ->placeholder('—'),
                TextColumn::make('reference')
                    ->label(__('point-of-sale::filament/admin/clusters/orders/resources/order.table.columns.reference'))
                    ->placeholder('—')
                    ->searchable(),
                TextColumn::make('partner.name')
                    ->label(__('point-of-sale::filament/admin/clusters/orders/resources/order.table.columns.partner'))
                    ->placeholder('—')
                    ->searchable(),
                TextColumn::make('user.name')
                    ->label(__('point-of-sale::filament/admin/clusters/orders/resources/order.table.columns.user'))
                    ->placeholder('—'),
                TextColumn::make('amount_total')
                    ->label(__('point-of-sale::filament/admin/clusters/orders/resources/order.table.columns.amount-total'))
                    ->money(fn (Order $record): ?string => $record->currency?->name)
                    ->alignEnd()
                    ->summarize(Sum::make()->money(fn (): ?string => Company::first()?->currency?->name))
                    ->sortable(),
                TextColumn::make('state')
                    ->label(__('point-of-sale::filament/admin/clusters/orders/resources/order.table.columns.state'))
                    ->badge(),
                TextColumn::make('tracking_number')
                    ->label(__('point-of-sale::filament/admin/clusters/orders/resources/order.table.columns.tracking-number'))
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('is_edited')
                    ->label(__('point-of-sale::filament/admin/clusters/orders/resources/order.table.columns.is-edited'))
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('amount_paid')
                    ->label(__('point-of-sale::filament/admin/clusters/orders/resources/order.table.columns.amount-paid'))
                    ->money(fn (Order $record): ?string => $record->currency?->name)
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('has_failed_operation')
                    ->label(__('point-of-sale::filament/admin/clusters/orders/resources/order.table.columns.has-failed-operation'))
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('is_invoiced')
                    ->label(__('point-of-sale::filament/admin/clusters/orders/resources/order.table.columns.is-invoiced'))
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->groups([
                TableGroup::make('session.name')
                    ->label(__('point-of-sale::filament/admin/clusters/orders/resources/order.table.groups.session')),
                TableGroup::make('config.name')
                    ->label(__('point-of-sale::filament/admin/clusters/orders/resources/order.table.groups.config')),
                TableGroup::make('state')
                    ->label(__('point-of-sale::filament/admin/clusters/orders/resources/order.table.groups.state')),
                TableGroup::make('ordered_at')
                    ->label(__('point-of-sale::filament/admin/clusters/orders/resources/order.table.groups.ordered-at'))
                    ->date(),
            ])
            ->filters([
                SelectFilter::make('state')
                    ->label(__('point-of-sale::filament/admin/clusters/orders/resources/order.table.filters.state'))
                    ->options(OrderState::class)
                    ->native(false),
                SelectFilter::make('session_id')
                    ->label(__('point-of-sale::filament/admin/clusters/orders/resources/order.table.filters.session'))
                    ->relationship('session', 'name')
                    ->searchable()
                    ->preload()
                    ->native(false),
                SelectFilter::make('config_id')
                    ->label(__('point-of-sale::filament/admin/clusters/orders/resources/order.table.filters.config'))
                    ->relationship('config', 'name')
                    ->searchable()
                    ->preload()
                    ->native(false),
            ])
            ->recordTitleAttribute('name')
            ->recordUrl(fn (Order $record): string => OrderResource::getUrl('view', ['record' => $record]))
            ->recordActions([
                EditAction::make()
                    ->url(fn (Order $record): string => OrderResource::getUrl('edit', ['record' => $record])),
            ]);
    }
}
