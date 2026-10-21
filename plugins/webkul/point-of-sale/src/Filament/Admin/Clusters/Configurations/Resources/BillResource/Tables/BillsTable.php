<?php

namespace Webkul\PointOfSale\Filament\Admin\Clusters\Configurations\Resources\BillResource\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BillsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->reorderableColumns()
            ->defaultSort('value')
            ->columns([
                TextColumn::make('name')
                    ->label(__('point-of-sale::filament/admin/clusters/configurations/resources/bill.table.columns.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('value')
                    ->label(__('point-of-sale::filament/admin/clusters/configurations/resources/bill.table.columns.value'))
                    ->numeric(decimalPlaces: 4)
                    ->sortable(),
                IconColumn::make('is_for_all_configs')
                    ->label(__('point-of-sale::filament/admin/clusters/configurations/resources/bill.table.columns.is-for-all-configs'))
                    ->boolean(),
                TextColumn::make('configs.name')
                    ->label(__('point-of-sale::filament/admin/clusters/configurations/resources/bill.table.columns.configs'))
                    ->badge()
                    ->placeholder('—'),
            ])
            ->recordTitleAttribute('name')
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
