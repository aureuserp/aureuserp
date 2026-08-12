<?php

namespace Webkul\Support\Filament\Resources\UOMCategoryResource\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;

class UOMCategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('support::filament/resources/uom-category.table.columns.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('uoms.name')
                    ->label(__('support::filament/resources/uom-category.table.columns.uoms'))
                    ->badge()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(__('support::filament/resources/uom-category.table.columns.created-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('support::filament/resources/uom-category.table.columns.updated-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->groups([
                Group::make('created_at')
                    ->label(__('support::filament/resources/uom-category.table.groups.created-at'))
                    ->date(),
            ])
            ->recordActions([
                EditAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('support::filament/resources/uom-category.table.actions.edit.notification.title'))
                            ->body(__('support::filament/resources/uom-category.table.actions.edit.notification.body')),
                    ),
                DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('support::filament/resources/uom-category.table.actions.delete.notification.title'))
                            ->body(__('support::filament/resources/uom-category.table.actions.delete.notification.body')),
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('support::filament/resources/uom-category.table.bulk-actions.delete.notification.title'))
                                ->body(__('support::filament/resources/uom-category.table.bulk-actions.delete.notification.body')),
                        ),
                ]),
            ]);
    }
}
