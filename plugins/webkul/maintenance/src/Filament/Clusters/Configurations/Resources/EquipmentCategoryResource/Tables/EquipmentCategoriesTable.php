<?php

namespace Webkul\Maintenance\Filament\Clusters\Configurations\Resources\EquipmentCategoryResource\Tables;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;

class EquipmentCategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('maintenance::filament/clusters/configurations/resources/equipment-category.table.columns.name'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('technician.name')
                    ->label(__('maintenance::filament/clusters/configurations/resources/equipment-category.table.columns.technician'))
                    ->sortable(),

                TextColumn::make('company.name')
                    ->label(__('maintenance::filament/clusters/configurations/resources/equipment-category.table.columns.company'))
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label(__('maintenance::filament/clusters/configurations/resources/equipment-category.table.columns.created-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->groups([
                Group::make('technician.name')
                    ->label(__('maintenance::filament/clusters/configurations/resources/equipment-category.table.groups.technician')),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('maintenance::filament/clusters/configurations/resources/equipment-category.table.actions.edit.notification.title'))
                            ->body(__('maintenance::filament/clusters/configurations/resources/equipment-category.table.actions.edit.notification.body')),
                    ),
                DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('maintenance::filament/clusters/configurations/resources/equipment-category.table.actions.delete.notification.title'))
                            ->body(__('maintenance::filament/clusters/configurations/resources/equipment-category.table.actions.delete.notification.body')),
                    ),
            ])
            ->toolbarActions([
                DeleteBulkAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('maintenance::filament/clusters/configurations/resources/equipment-category.table.bulk-actions.delete.notification.title'))
                            ->body(__('maintenance::filament/clusters/configurations/resources/equipment-category.table.bulk-actions.delete.notification.body')),
                    ),
            ])
            ->emptyStateActions([
                CreateAction::make()
                    ->icon('heroicon-o-plus-circle')
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('maintenance::filament/clusters/configurations/resources/equipment-category.table.empty-state.create.notification.title'))
                            ->body(__('maintenance::filament/clusters/configurations/resources/equipment-category.table.empty-state.create.notification.body')),
                    ),
            ]);
    }
}
