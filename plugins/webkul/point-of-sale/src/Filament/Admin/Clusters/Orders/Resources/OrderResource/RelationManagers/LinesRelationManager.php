<?php

namespace Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources\OrderResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Webkul\PointOfSale\Models\OrderLine;

class LinesRelationManager extends RelationManager
{
    protected static string $relationship = 'lines';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('point-of-sale::filament/admin/clusters/orders/resources/order/relation-managers/lines.title');
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort')
            ->columns([
                TextColumn::make('full_product_name')
                    ->label(__('point-of-sale::filament/admin/clusters/orders/resources/order/relation-managers/lines.table.columns.product'))
                    ->searchable(),
                TextColumn::make('qty')
                    ->label(__('point-of-sale::filament/admin/clusters/orders/resources/order/relation-managers/lines.table.columns.qty'))
                    ->numeric(decimalPlaces: 4),
                TextColumn::make('price_unit')
                    ->label(__('point-of-sale::filament/admin/clusters/orders/resources/order/relation-managers/lines.table.columns.price-unit'))
                    ->money(fn (OrderLine $record): ?string => $record->order?->currency?->name),
                TextColumn::make('discount')
                    ->label(__('point-of-sale::filament/admin/clusters/orders/resources/order/relation-managers/lines.table.columns.discount'))
                    ->numeric(decimalPlaces: 2),
                TextColumn::make('price_subtotal')
                    ->label(__('point-of-sale::filament/admin/clusters/orders/resources/order/relation-managers/lines.table.columns.price-subtotal'))
                    ->money(fn (OrderLine $record): ?string => $record->order?->currency?->name),
                TextColumn::make('price_tax')
                    ->label(__('point-of-sale::filament/admin/clusters/orders/resources/order/relation-managers/lines.table.columns.price-tax'))
                    ->money(fn (OrderLine $record): ?string => $record->order?->currency?->name)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('total_cost')
                    ->label(__('point-of-sale::filament/admin/clusters/orders/resources/order/relation-managers/lines.table.columns.total-cost'))
                    ->money(fn (OrderLine $record): ?string => $record->order?->currency?->name)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('refunded_qty')
                    ->label(__('point-of-sale::filament/admin/clusters/orders/resources/order/relation-managers/lines.table.columns.refunded-qty'))
                    ->numeric(decimalPlaces: 4)
                    ->toggleable(isToggledHiddenByDefault: true),
            ]);
    }
}
