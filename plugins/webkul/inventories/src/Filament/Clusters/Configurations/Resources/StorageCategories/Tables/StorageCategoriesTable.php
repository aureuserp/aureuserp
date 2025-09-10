<?php

namespace Webkul\Inventory\Filament\Clusters\Configurations\Resources\StorageCategories\Tables;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;

class StorageCategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('inventories::filament/clusters/configurations/resources/storage-category.table.columns.name'))
                    ->searchable(),
                TextColumn::make('allow_new_products')
                    ->label(__('inventories::filament/clusters/configurations/resources/storage-category.table.columns.allow-new-products'))
                    ->sortable(),
                TextColumn::make('max_weight')
                    ->label(__('inventories::filament/clusters/configurations/resources/storage-category.table.columns.max-weight'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('company.name')
                    ->label(__('inventories::filament/clusters/configurations/resources/storage-category.table.columns.company'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(__('inventories::filament/clusters/configurations/resources/storage-category.table.columns.created-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('inventories::filament/clusters/configurations/resources/storage-category.table.columns.updated-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->groups([
                Group::make('allow_new_products')
                    ->label(__('inventories::filament/clusters/configurations/resources/storage-category.table.groups.allow-new-products'))
                    ->collapsible(),
                Group::make('created_at')
                    ->label(__('inventories::filament/clusters/configurations/resources/storage-category.table.groups.created-at'))
                    ->collapsible(),
                Group::make('updated_at')
                    ->label(__('inventories::filament/clusters/configurations/resources/storage-category.table.groups.updated-at'))
                    ->date()
                    ->collapsible(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('inventories::filament/clusters/configurations/resources/storage-category.table.actions.delete.notification.title'))
                            ->body(__('inventories::filament/clusters/configurations/resources/storage-category.table.actions.delete.notification.body')),
                    ),
            ])
            ->toolbarActions([
                DeleteBulkAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('inventories::filament/clusters/configurations/resources/storage-category.table.bulk-actions.delete.notification.title'))
                            ->body(__('inventories::filament/clusters/configurations/resources/storage-category.table.bulk-actions.delete.notification.body')),
                    ),
            ])
            ->emptyStateActions([
                CreateAction::make()
                    ->icon('heroicon-o-plus-circle'),
            ]);
    }
}
