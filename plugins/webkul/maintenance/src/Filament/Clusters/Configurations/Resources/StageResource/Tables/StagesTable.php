<?php

namespace Webkul\Maintenance\Filament\Clusters\Configurations\Resources\StageResource\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;

class StagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('maintenance::filament/clusters/configurations/resources/stage.table.columns.name'))
                    ->searchable()
                    ->sortable(),

                IconColumn::make('done')
                    ->label(__('maintenance::filament/clusters/configurations/resources/stage.table.columns.done'))
                    ->boolean()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label(__('maintenance::filament/clusters/configurations/resources/stage.table.columns.created-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->groups([
                Group::make('done')
                    ->label(__('maintenance::filament/clusters/configurations/resources/stage.table.groups.done')),
                Group::make('created_at')
                    ->label(__('maintenance::filament/clusters/configurations/resources/stage.table.groups.created-at'))
                    ->date(),
            ])
            ->reorderable('sort')
            ->defaultSort('sort')
            ->recordActions([
                EditAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('maintenance::filament/clusters/configurations/resources/stage.table.actions.edit.notification.title'))
                            ->body(__('maintenance::filament/clusters/configurations/resources/stage.table.actions.edit.notification.body')),
                    ),
                DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('maintenance::filament/clusters/configurations/resources/stage.table.actions.delete.notification.title'))
                            ->body(__('maintenance::filament/clusters/configurations/resources/stage.table.actions.delete.notification.body')),
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('maintenance::filament/clusters/configurations/resources/stage.table.bulk-actions.delete.notification.title'))
                                ->body(__('maintenance::filament/clusters/configurations/resources/stage.table.bulk-actions.delete.notification.body')),
                        ),
                ]),
            ]);
    }
}
