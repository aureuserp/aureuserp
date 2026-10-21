<?php

namespace Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources\PrinterResource\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Webkul\PointOfSale\Enums\PrinterType;
use Webkul\PointOfSale\Models\Printer;

class PrintersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->reorderableColumns()
            ->defaultSort('sort')
            ->columns([
                TextColumn::make('name')
                    ->label(__('point-of-sale::filament/admin/clusters/orders/resources/printer.table.columns.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('printer_type')
                    ->label(__('point-of-sale::filament/admin/clusters/orders/resources/printer.table.columns.printer-type'))
                    ->badge(),
                TextColumn::make('proxy_ip')
                    ->label(__('point-of-sale::filament/admin/clusters/orders/resources/printer.table.columns.proxy-ip'))
                    ->placeholder('—'),
                TextColumn::make('categories.name')
                    ->label(__('point-of-sale::filament/admin/clusters/orders/resources/printer.table.columns.categories'))
                    ->badge()
                    ->placeholder('—'),
                TextColumn::make('company.name')
                    ->label(__('point-of-sale::filament/admin/clusters/orders/resources/printer.table.columns.company'))
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('printer_type')
                    ->label(__('point-of-sale::filament/admin/clusters/orders/resources/printer.table.filters.printer-type'))
                    ->options(PrinterType::class)
                    ->native(false),
                TrashedFilter::make(),
            ])
            ->recordTitleAttribute('name')
            ->recordActions([
                EditAction::make()
                    ->hidden(fn (Printer $record): bool => $record->trashed()),
                RestoreAction::make(),
                DeleteAction::make(),
                ForceDeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
