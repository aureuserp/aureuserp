<?php

namespace Webkul\Maintenance\Filament\Clusters\Maintenance\Resources\MaintenanceRequestResource\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Grouping\Group as TableGroup;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Webkul\Chatter\Filament\Actions\ActivityTableAction;
use Webkul\Maintenance\Models\MaintenanceRequest;

class MaintenanceRequestsTable
{
    public static function configure(Table $table, array $customColumns = []): Table
    {
        return $table
            ->columns(array_merge([
                TextColumn::make('name')
                    ->label(__('maintenance::filament/clusters/maintenance/resources/maintenance-request.table.columns.name'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('creator.name')
                    ->label(__('maintenance::filament/clusters/maintenance/resources/maintenance-request.table.columns.creator'))
                    ->placeholder('—')
                    ->sortable(),

                TextColumn::make('user.name')
                    ->label(__('maintenance::filament/clusters/maintenance/resources/maintenance-request.table.columns.technician'))
                    ->placeholder('—')
                    ->sortable(),

                TextColumn::make('category.name')
                    ->label(__('maintenance::filament/clusters/maintenance/resources/maintenance-request.table.columns.category'))
                    ->placeholder('—')
                    ->sortable(),

                TextColumn::make('stage.name')
                    ->label(__('maintenance::filament/clusters/maintenance/resources/maintenance-request.table.columns.stage'))
                    ->placeholder('—')
                    ->sortable(),

                TextColumn::make('company.name')
                    ->label(__('maintenance::filament/clusters/maintenance/resources/maintenance-request.table.columns.company'))
                    ->placeholder('—')
                    ->sortable(),
            ], $customColumns))
            ->groups([
                TableGroup::make('stage.name')
                    ->label(__('maintenance::filament/clusters/maintenance/resources/maintenance-request.table.groups.stage')),
                TableGroup::make('user.name')
                    ->label(__('maintenance::filament/clusters/maintenance/resources/maintenance-request.table.groups.assigned-to')),
                TableGroup::make('category.name')
                    ->label(__('maintenance::filament/clusters/maintenance/resources/maintenance-request.table.groups.category')),
                TableGroup::make('creator.name')
                    ->label(__('maintenance::filament/clusters/maintenance/resources/maintenance-request.table.groups.created-by')),
            ])
            ->recordActions([
                ActivityTableAction::make()
                    ->hidden(fn ($record) => $record->trashed()),
                ActionGroup::make([
                    ViewAction::make()
                        ->hidden(fn ($record) => $record->trashed()),
                    EditAction::make()
                        ->hidden(fn ($record) => $record->trashed()),
                    RestoreAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('maintenance::filament/clusters/maintenance/resources/maintenance-request.table.actions.restore.notification.title'))
                                ->body(__('maintenance::filament/clusters/maintenance/resources/maintenance-request.table.actions.restore.notification.body')),
                        ),
                    DeleteAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('maintenance::filament/clusters/maintenance/resources/maintenance-request.table.actions.delete.notification.title'))
                                ->body(__('maintenance::filament/clusters/maintenance/resources/maintenance-request.table.actions.delete.notification.body')),
                        ),
                    ForceDeleteAction::make()
                        ->action(function (MaintenanceRequest $record): void {
                            try {
                                $record->forceDelete();

                                Notification::make()
                                    ->success()
                                    ->title(__('maintenance::filament/clusters/maintenance/resources/maintenance-request.table.actions.force-delete.notification.success.title'))
                                    ->body(__('maintenance::filament/clusters/maintenance/resources/maintenance-request.table.actions.force-delete.notification.success.body'))
                                    ->send();
                            } catch (QueryException) {
                                Notification::make()
                                    ->danger()
                                    ->title(__('maintenance::filament/clusters/maintenance/resources/maintenance-request.table.actions.force-delete.notification.error.title'))
                                    ->body(__('maintenance::filament/clusters/maintenance/resources/maintenance-request.table.actions.force-delete.notification.error.body'))
                                    ->send();
                            }
                        }),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    RestoreBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('maintenance::filament/clusters/maintenance/resources/maintenance-request.table.bulk-actions.restore.notification.title'))
                                ->body(__('maintenance::filament/clusters/maintenance/resources/maintenance-request.table.bulk-actions.restore.notification.body')),
                        ),
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('maintenance::filament/clusters/maintenance/resources/maintenance-request.table.bulk-actions.delete.notification.title'))
                                ->body(__('maintenance::filament/clusters/maintenance/resources/maintenance-request.table.bulk-actions.delete.notification.body')),
                        ),
                    ForceDeleteBulkAction::make()
                        ->action(function (Collection $records): void {
                            try {
                                $records->each(fn (Model $record) => $record->forceDelete());

                                Notification::make()
                                    ->success()
                                    ->title(__('maintenance::filament/clusters/maintenance/resources/maintenance-request.table.actions.force-delete.notification.success.title'))
                                    ->body(__('maintenance::filament/clusters/maintenance/resources/maintenance-request.table.actions.force-delete.notification.success.body'))
                                    ->send();
                            } catch (QueryException) {
                                Notification::make()
                                    ->danger()
                                    ->title(__('maintenance::filament/clusters/maintenance/resources/maintenance-request.table.actions.force-delete.notification.error.title'))
                                    ->body(__('maintenance::filament/clusters/maintenance/resources/maintenance-request.table.actions.force-delete.notification.error.body'))
                                    ->send();
                            }
                        }),
                ]),
            ])
            ->emptyStateActions([
                CreateAction::make()
                    ->icon('heroicon-o-plus-circle'),
            ]);
    }
}
