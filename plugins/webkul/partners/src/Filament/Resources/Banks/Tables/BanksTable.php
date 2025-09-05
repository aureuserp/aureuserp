<?php

namespace Webkul\Partner\Filament\Resources\Banks\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;

class BanksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('partners::filament/resources/bank.table.columns.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('code')
                    ->label(__('partners::filament/resources/bank.table.columns.code'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('country.name')
                    ->label(__('partners::filament/resources/bank.table.columns.country'))
                    ->numeric()
                    ->sortable()
                    ->sortable(),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->groups([
                Group::make('country.name')
                    ->label(__('partners::filament/resources/bank.table.groups.country')),
                Group::make('created_at')
                    ->label(__('partners::filament/resources/bank.table.groups.created-at'))
                    ->date(),
            ])
            ->recordActions([
                EditAction::make()
                    ->hidden(fn ($record) => $record->trashed())
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('partners::filament/resources/bank.table.actions.edit.notification.title'))
                            ->body(__('partners::filament/resources/bank.table.actions.edit.notification.body')),
                    ),
                RestoreAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('partners::filament/resources/bank.table.actions.restore.notification.title'))
                            ->body(__('partners::filament/resources/bank.table.actions.restore.notification.body')),
                    ),
                DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('partners::filament/resources/bank.table.actions.delete.notification.title'))
                            ->body(__('partners::filament/resources/bank.table.actions.delete.notification.body')),
                    ),
                ForceDeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('partners::filament/resources/bank.table.actions.force-delete.notification.title'))
                            ->body(__('partners::filament/resources/bank.table.actions.force-delete.notification.body')),
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    RestoreBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('partners::filament/resources/bank.table.bulk-actions.restore.notification.title'))
                                ->body(__('partners::filament/resources/bank.table.bulk-actions.restore.notification.body')),
                        ),
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('partners::filament/resources/bank.table.bulk-actions.delete.notification.title'))
                                ->body(__('partners::filament/resources/bank.table.bulk-actions.delete.notification.body')),
                        ),
                    ForceDeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('partners::filament/resources/bank.table.bulk-actions.force-delete.notification.title'))
                                ->body(__('partners::filament/resources/bank.table.bulk-actions.force-delete.notification.body')),
                        ),
                ]),
            ]);
    }
}
