<?php

namespace Webkul\Crm\Filament\Clusters\Configurations\Resources\StageResource\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Webkul\Crm\Filament\Actions\RecordBulkActions;
use Webkul\Crm\Filament\Exports\StageExporter;

class StagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('crm::filament/clusters/configurations/resources/stage.table.columns.name'))
                    ->searchable(),
                TextColumn::make('teams.name')
                    ->label(__('crm::filament/clusters/configurations/resources/stage.table.columns.teams'))
                    ->badge()
                    ->placeholder(__('crm::filament/clusters/configurations/resources/stage.form.fields.all-teams')),
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
                RecordBulkActions::make(exporter: StageExporter::class, copyRelations: ['teams']),
            ]);
    }
}
