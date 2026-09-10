<?php

namespace Webkul\Maintenance\Filament\Clusters\Configurations\Resources\TeamResource\Tables;

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
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Webkul\Maintenance\Models\Team;

class TeamsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('maintenance::filament/clusters/configurations/resources/team.table.columns.name'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('company.name')
                    ->label(__('maintenance::filament/clusters/configurations/resources/team.table.columns.company'))
                    ->sortable(),

                TextColumn::make('users.name')
                    ->label(__('maintenance::filament/clusters/configurations/resources/team.table.columns.users'))
                    ->badge(),

                TextColumn::make('created_at')
                    ->label(__('maintenance::filament/clusters/configurations/resources/team.table.columns.created-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([
                EditAction::make()
                    ->hidden(fn ($record) => $record->trashed())
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('maintenance::filament/clusters/configurations/resources/team.table.actions.edit.notification.title'))
                            ->body(__('maintenance::filament/clusters/configurations/resources/team.table.actions.edit.notification.body')),
                    ),
                RestoreAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('maintenance::filament/clusters/configurations/resources/team.table.actions.restore.notification.title'))
                            ->body(__('maintenance::filament/clusters/configurations/resources/team.table.actions.restore.notification.body')),
                    ),
                DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('maintenance::filament/clusters/configurations/resources/team.table.actions.delete.notification.title'))
                            ->body(__('maintenance::filament/clusters/configurations/resources/team.table.actions.delete.notification.body')),
                    ),
                ForceDeleteAction::make()
                    ->action(function (Team $record): void {
                        try {
                            $record->forceDelete();

                            Notification::make()
                                ->success()
                                ->title(__('maintenance::filament/clusters/configurations/resources/team.table.actions.force-delete.notification.success.title'))
                                ->body(__('maintenance::filament/clusters/configurations/resources/team.table.actions.force-delete.notification.success.body'))
                                ->send();
                        } catch (QueryException) {
                            Notification::make()
                                ->danger()
                                ->title(__('maintenance::filament/clusters/configurations/resources/team.table.actions.force-delete.notification.error.title'))
                                ->body(__('maintenance::filament/clusters/configurations/resources/team.table.actions.force-delete.notification.error.body'))
                                ->send();
                        }
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    RestoreBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('maintenance::filament/clusters/configurations/resources/team.table.bulk-actions.restore.notification.title'))
                                ->body(__('maintenance::filament/clusters/configurations/resources/team.table.bulk-actions.restore.notification.body')),
                        ),
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('maintenance::filament/clusters/configurations/resources/team.table.bulk-actions.delete.notification.title'))
                                ->body(__('maintenance::filament/clusters/configurations/resources/team.table.bulk-actions.delete.notification.body')),
                        ),
                    ForceDeleteBulkAction::make()
                        ->action(function (Collection $records): void {
                            try {
                                $records->each(fn (Model $record) => $record->forceDelete());

                                Notification::make()
                                    ->success()
                                    ->title(__('maintenance::filament/clusters/configurations/resources/team.table.actions.force-delete.notification.success.title'))
                                    ->body(__('maintenance::filament/clusters/configurations/resources/team.table.actions.force-delete.notification.success.body'))
                                    ->send();
                            } catch (QueryException) {
                                Notification::make()
                                    ->danger()
                                    ->title(__('maintenance::filament/clusters/configurations/resources/team.table.actions.force-delete.notification.error.title'))
                                    ->body(__('maintenance::filament/clusters/configurations/resources/team.table.actions.force-delete.notification.error.body'))
                                    ->send();
                            }
                        }),
                ]),
            ]);
    }
}
