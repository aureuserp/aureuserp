<?php

namespace Webkul\Partner\Filament\Resources\Industries\Tables;

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

class IndustriesTable
{
    public static function configure($table)
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('partners::filament/resources/industry.table.columns.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('description')
                    ->label(__('partners::filament/resources/industry.table.columns.full-name'))
                    ->searchable()
                    ->sortable(),
            ])
            ->recordActions([
                EditAction::make()
                    ->hidden(fn ($record) => $record->trashed())
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('partners::filament/resources/industry.table.actions.edit.notification.title'))
                            ->body(__('partners::filament/resources/industry.table.actions.edit.notification.body')),
                    ),
                RestoreAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('partners::filament/resources/industry.table.actions.restore.notification.title'))
                            ->body(__('partners::filament/resources/industry.table.actions.restore.notification.body')),
                    ),
                DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('partners::filament/resources/industry.table.actions.delete.notification.title'))
                            ->body(__('partners::filament/resources/industry.table.actions.delete.notification.body')),
                    ),
                ForceDeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('partners::filament/resources/industry.table.actions.force-delete.notification.title'))
                            ->body(__('partners::filament/resources/industry.table.actions.force-delete.notification.body')),
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    RestoreBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('partners::filament/resources/industry.table.bulk-actions.restore.notification.title'))
                                ->body(__('partners::filament/resources/industry.table.bulk-actions.restore.notification.body')),
                        ),
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('partners::filament/resources/industry.table.bulk-actions.delete.notification.title'))
                                ->body(__('partners::filament/resources/industry.table.bulk-actions.delete.notification.body')),
                        ),
                    ForceDeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('partners::filament/resources/industry.table.bulk-actions.force-delete.notification.title'))
                                ->body(__('partners::filament/resources/industry.table.bulk-actions.force-delete.notification.body')),
                        ),
                ]),
            ]);
    }
}
