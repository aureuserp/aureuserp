<?php

namespace Webkul\Product\Filament\Resources\PriceListResource\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class PriceListsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->reorderable('sort')
            ->defaultSort('sort')
            ->columns([
                TextColumn::make('name')
                    ->label(__('products::filament/resources/price-list.table.columns.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('currency.name')
                    ->label(__('products::filament/resources/price-list.table.columns.currency'))
                    ->sortable(),
                TextColumn::make('company.name')
                    ->label(__('products::filament/resources/price-list.table.columns.company'))
                    ->sortable()
                    ->placeholder('—'),
                TextColumn::make('items_count')
                    ->label(__('products::filament/resources/price-list.table.columns.rules'))
                    ->counts('items'),
                IconColumn::make('is_active')
                    ->label(__('products::filament/resources/price-list.table.columns.status'))
                    ->boolean(),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label(__('products::filament/resources/price-list.table.filters.status')),
                SelectFilter::make('currency_id')
                    ->label(__('products::filament/resources/price-list.table.filters.currency'))
                    ->relationship('currency', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
