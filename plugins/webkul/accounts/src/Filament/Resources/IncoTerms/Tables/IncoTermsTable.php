<?php

namespace Webkul\Account\Filament\Resources\IncoTerms\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class IncotermsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label(__('accounts::filament/resources/incoterm.table.columns.code'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->label(__('accounts::filament/resources/incoterm.table.columns.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('createdBy.name')
                    ->label(__('accounts::filament/resources/incoterm.table.columns.created-by'))
                    ->searchable()
                    ->sortable(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('accounts::filament/resources/incoterm.table.actions.edit.notification.title'))
                            ->body(__('accounts::filament/resources/incoterm.table.actions.edit.notification.body'))
                    ),
                DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->title(__('accounts::filament/resources/incoterm.table.actions.delete.notification.title'))
                            ->body(__('accounts::filament/resources/incoterm.table.actions.delete.notification.body'))
                    ),
                RestoreAction::make()
                    ->successNotification(
                        Notification::make()
                            ->title(__('accounts::filament/resources/incoterm.table.actions.restore.notification.title'))
                            ->body(__('accounts::filament/resources/incoterm.table.actions.restore.notification.body'))
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->title(__('accounts::filament/resources/incoterm.table.bulk-actions.delete.notification.title'))
                                ->body(__('accounts::filament/resources/incoterm.table.bulk-actions.delete.notification.body'))
                        ),
                    ForceDeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->title(__('accounts::filament/resources/incoterm.table.bulk-actions.force-delete.notification.title'))
                                ->body(__('accounts::filament/resources/incoterm.table.bulk-actions.force-delete.notification.body'))
                        ),
                    RestoreBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->title(__('accounts::filament/resources/incoterm.table.bulk-actions.restore.notification.title'))
                                ->body(__('accounts::filament/resources/incoterm.table.bulk-actions.restore.notification.body'))
                        ),
                ]),
            ]);
    }
}
