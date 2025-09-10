<?php

namespace Webkul\Security\Filament\Resources\Teams\Tables;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TeamsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('security::filament/resources/team.table.columns.name'))
                    ->searchable()
                    ->limit(50)
                    ->sortable(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('security::filament/resources/team.table.actions.edit.notification.title'))
                            ->body(__('security::filament/resources/team.table.actions.edit.notification.body'))
                    ),
                DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('security::filament/resources/team.table.actions.delete.notification.title'))
                            ->body(__('security::filament/resources/team.table.actions.delete.notification.body'))
                    ),
            ])
            ->emptyStateActions([
                CreateAction::make()
                    ->icon('heroicon-o-plus-circle')
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('security::filament/resources/team.table.empty-state-actions.create.notification.title'))
                            ->body(__('security::filament/resources/team.table.empty-state-actions.create.notification.body'))
                    ),
            ]);
    }
}
