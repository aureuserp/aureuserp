<?php

namespace Webkul\Inventory\Filament\Clusters\Configurations\Resources\PackageTypes\tables;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PackageTypesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('inventories::filament/clusters/configurations/resources/package-type.table.columns.name'))
                    ->searchable(),
                TextColumn::make('height')
                    ->label(__('inventories::filament/clusters/configurations/resources/package-type.table.columns.height'))
                    ->sortable(),
                TextColumn::make('width')
                    ->label(__('inventories::filament/clusters/configurations/resources/package-type.table.columns.width'))
                    ->sortable(),
                TextColumn::make('length')
                    ->label(__('inventories::filament/clusters/configurations/resources/package-type.table.columns.length'))
                    ->sortable(),
                TextColumn::make('barcode')
                    ->label(__('inventories::filament/clusters/configurations/resources/package-type.table.columns.barcode'))
                    ->placeholder('—')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label(__('inventories::filament/clusters/configurations/resources/package-type.table.columns.created-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('inventories::filament/clusters/configurations/resources/package-type.table.columns.updated-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('inventories::filament/clusters/configurations/resources/package-type.table.actions.delete.notification.title'))
                            ->body(__('inventories::filament/clusters/configurations/resources/package-type.table.actions.delete.notification.body')),
                    ),
            ])
            ->toolbarActions([
                DeleteBulkAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('inventories::filament/clusters/configurations/resources/package-type.table.bulk-actions.delete.notification.title'))
                            ->body(__('inventories::filament/clusters/configurations/resources/package-type.table.bulk-actions.delete.notification.body')),
                    ),
            ])
            ->emptyStateActions([
                CreateAction::make()
                    ->icon('heroicon-o-plus-circle'),
            ]);
    }
}
