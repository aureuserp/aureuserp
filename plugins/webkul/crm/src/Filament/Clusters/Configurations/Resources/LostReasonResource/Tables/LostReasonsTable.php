<?php

namespace Webkul\Crm\Filament\Clusters\Configurations\Resources\LostReasonResource\Tables;

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

class LostReasonsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('crm::filament/clusters/configurations/resources/lost-reason.table.columns.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('creator.name')
                    ->label(__('crm::filament/clusters/configurations/resources/lost-reason.table.columns.created-by'))
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label(__('crm::filament/clusters/configurations/resources/lost-reason.table.columns.created-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('crm::filament/clusters/configurations/resources/lost-reason.table.columns.updated-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('name')
            ->recordActions([
                EditAction::make()
                    ->hidden(fn ($record): bool => $record->trashed())
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('crm::filament/clusters/configurations/resources/lost-reason.table.actions.edit.notification.title'))
                            ->body(__('crm::filament/clusters/configurations/resources/lost-reason.table.actions.edit.notification.body')),
                    ),
                RestoreAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('crm::filament/clusters/configurations/resources/lost-reason.table.actions.restore.notification.title'))
                            ->body(__('crm::filament/clusters/configurations/resources/lost-reason.table.actions.restore.notification.body')),
                    ),
                DeleteAction::make()
                    ->label(__('crm::filament/clusters/configurations/resources/lost-reason.table.actions.delete.label'))
                    ->icon('heroicon-o-archive-box')
                    ->modalHeading(__('crm::filament/clusters/configurations/resources/lost-reason.table.actions.delete.modal.heading'))
                    ->modalDescription(__('crm::filament/clusters/configurations/resources/lost-reason.table.actions.delete.modal.description'))
                    ->modalSubmitActionLabel(__('crm::filament/clusters/configurations/resources/lost-reason.table.actions.delete.label'))
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('crm::filament/clusters/configurations/resources/lost-reason.table.actions.delete.notification.title'))
                            ->body(__('crm::filament/clusters/configurations/resources/lost-reason.table.actions.delete.notification.body')),
                    ),
                ForceDeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('crm::filament/clusters/configurations/resources/lost-reason.table.actions.force-delete.notification.title'))
                            ->body(__('crm::filament/clusters/configurations/resources/lost-reason.table.actions.force-delete.notification.body')),
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    RestoreBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('crm::filament/clusters/configurations/resources/lost-reason.table.bulk-actions.restore.notification.title'))
                                ->body(__('crm::filament/clusters/configurations/resources/lost-reason.table.bulk-actions.restore.notification.body')),
                        ),
                    DeleteBulkAction::make()
                        ->label(__('crm::filament/clusters/configurations/resources/lost-reason.table.bulk-actions.delete.label'))
                        ->icon('heroicon-o-archive-box')
                        ->modalHeading(__('crm::filament/clusters/configurations/resources/lost-reason.table.bulk-actions.delete.modal.heading'))
                        ->modalDescription(__('crm::filament/clusters/configurations/resources/lost-reason.table.bulk-actions.delete.modal.description'))
                        ->modalSubmitActionLabel(__('crm::filament/clusters/configurations/resources/lost-reason.table.bulk-actions.delete.label'))
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('crm::filament/clusters/configurations/resources/lost-reason.table.bulk-actions.delete.notification.title'))
                                ->body(__('crm::filament/clusters/configurations/resources/lost-reason.table.bulk-actions.delete.notification.body')),
                        ),
                    ForceDeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('crm::filament/clusters/configurations/resources/lost-reason.table.bulk-actions.force-delete.notification.title'))
                                ->body(__('crm::filament/clusters/configurations/resources/lost-reason.table.bulk-actions.force-delete.notification.body')),
                        ),
                ]),
            ]);
    }
}
