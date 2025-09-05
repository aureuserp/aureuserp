<?php

namespace Webkul\Partner\Filament\Resources\Titles\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;

class TitlesTable
{
    public static function configure($table)
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('partners::filament/resources/title.table.columns.name'))
                    ->searchable(),
                TextColumn::make('short_name')
                    ->label(__('partners::filament/resources/title.table.columns.short-name'))
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([
                EditAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('partners::filament/resources/title.table.actions.edit.notification.title'))
                            ->body(__('partners::filament/resources/title.table.actions.edit.notification.body')),
                    ),
                DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('partners::filament/resources/title.table.actions.delete.notification.title'))
                            ->body(__('partners::filament/resources/title.table.actions.delete.notification.body')),
                    ),
            ])
            ->toolbarActions([
                DeleteBulkAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('partners::filament/resources/title.table.bulk-actions.delete.notification.title'))
                            ->body(__('partners::filament/resources/title.table.bulk-actions.delete.notification.body')),
                    ),
            ]);
    }
}
