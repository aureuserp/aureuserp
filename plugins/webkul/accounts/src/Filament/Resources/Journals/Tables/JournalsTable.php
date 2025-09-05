<?php

namespace Webkul\Account\Filament\Resources\Journals\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

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
                    ->formatStateUsing(fn ($state) => JournalType::options()[$state] ?? $state)
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
                TextColumn::make('createdBy.name')
                    ->searchable()
                    ->sortable()
                    ->label(__('accounts::filament/resources/journal.table.columns.created-by')),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->title(__('accounts::filament/resources/journal.table.actions.delete.notification.title'))
                            ->body(__('accounts::filament/resources/journal.table.actions.delete.notification.body'))
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->title(__('accounts::filament/resources/journal.table.bulk-actions.delete.notification.title'))
                                ->body(__('accounts::filament/resources/journal.table.bulk-actions.delete.notification.body'))
                        ),
                ]),
            ]);
    }
}
