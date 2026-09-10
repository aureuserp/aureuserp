<?php

namespace Webkul\Account\Filament\Resources\JournalResource\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Webkul\Account\Models\Journal;

class JournalsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->label(__('accounts::filament/resources/journal.table.columns.name')),
                TextColumn::make('type')
                    ->searchable()
                    ->sortable()
                    ->label(__('accounts::filament/resources/journal.table.columns.type')),
                TextColumn::make('code')
                    ->searchable()
                    ->sortable()
                    ->label(__('accounts::filament/resources/journal.table.columns.code')),
                TextColumn::make('currency.name')
                    ->searchable()
                    ->sortable()
                    ->label(__('accounts::filament/resources/journal.table.columns.currency')),
                TextColumn::make('creator.name')
                    ->searchable()
                    ->sortable()
                    ->label(__('accounts::filament/resources/journal.table.columns.created-by')),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make()
                    ->action(function (Journal $record, DeleteAction $action) {
                        try {
                            $record->delete();

                            $action->success();
                        } catch (QueryException $e) {
                            $action->failure();
                        }
                    })
                    ->failureNotification(
                        Notification::make()
                            ->danger()
                            ->title(__('accounts::filament/resources/journal.table.actions.delete.notification.error.title'))
                            ->body(__('accounts::filament/resources/journal.table.actions.delete.notification.error.body'))
                    )
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('accounts::filament/resources/journal.table.actions.delete.notification.success.title'))
                            ->body(__('accounts::filament/resources/journal.table.actions.delete.notification.success.body'))
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->action(function (Collection $records, DeleteBulkAction $action) {
                            try {
                                $records->each(fn (Model $record) => $record->delete());

                                $action->success();
                            } catch (QueryException $e) {
                                $action->failure();
                            }
                        })
                        ->failureNotification(
                            Notification::make()
                                ->danger()
                                ->title(__('accounts::filament/resources/journal.table.bulk-actions.delete.notification.error.title'))
                                ->body(__('accounts::filament/resources/journal.table.bulk-actions.delete.notification.error.body'))
                        )
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('accounts::filament/resources/journal.table.bulk-actions.delete.notification.success.title'))
                                ->body(__('accounts::filament/resources/journal.table.bulk-actions.delete.notification.success.body'))
                        ),
                ]),
            ]);
    }
}
