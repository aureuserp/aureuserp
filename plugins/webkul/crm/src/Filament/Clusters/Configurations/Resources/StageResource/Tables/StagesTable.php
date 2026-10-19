<?php

namespace Webkul\Crm\Filament\Clusters\Configurations\Resources\StageResource\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class StagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('crm::filament/clusters/configurations/resources/stage.table.columns.name'))
                    ->searchable(),
                IconColumn::make('is_won')
                    ->label(__('crm::filament/clusters/configurations/resources/stage.table.columns.is-won'))
                    ->boolean(),
                TextColumn::make('requirements')
                    ->label(__('crm::filament/clusters/configurations/resources/stage.table.columns.requirements'))
                    ->limit(60)
                    ->placeholder('—')
                    ->toggleable(),
                TextColumn::make('leads_count')
                    ->label(__('crm::filament/clusters/configurations/resources/stage.table.columns.opportunities'))
                    ->counts('leads'),
                TextColumn::make('created_at')
                    ->label(__('crm::filament/clusters/configurations/resources/stage.table.columns.created-at'))
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->reorderable('sort')
            ->defaultSort('sort')
            ->recordActions([
                EditAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('crm::filament/clusters/configurations/resources/stage.table.actions.edit.notification.title'))
                            ->body(__('crm::filament/clusters/configurations/resources/stage.table.actions.edit.notification.body')),
                    ),
                DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('crm::filament/clusters/configurations/resources/stage.table.actions.delete.notification.title'))
                            ->body(__('crm::filament/clusters/configurations/resources/stage.table.actions.delete.notification.body')),
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('crm::filament/clusters/configurations/resources/stage.table.bulk-actions.delete.notification.title'))
                                ->body(__('crm::filament/clusters/configurations/resources/stage.table.bulk-actions.delete.notification.body')),
                        ),
                ]),
            ]);
    }
}
