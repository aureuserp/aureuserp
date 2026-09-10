<?php

namespace Webkul\Manufacturing\Filament\Clusters\Configurations\Resources\WorkCenterResource\Tables;

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
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Webkul\Manufacturing\Enums\WorkCenterWorkingState;
use Webkul\Manufacturing\Models\WorkCenter;

class WorkCentersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->reorderableColumns()
            ->columns([
                TextColumn::make('name')
                    ->label(__('manufacturing::filament/clusters/configurations/resources/work-center.table.columns.name'))
                    ->searchable(),
                TextColumn::make('code')
                    ->label(__('manufacturing::filament/clusters/configurations/resources/work-center.table.columns.code'))
                    ->searchable(),
                TextColumn::make('company.name')
                    ->label(__('manufacturing::filament/clusters/configurations/resources/work-center.table.columns.company'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('calendar.name')
                    ->label(__('manufacturing::filament/clusters/configurations/resources/work-center.table.columns.calendar'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('working_state')
                    ->label(__('manufacturing::filament/clusters/configurations/resources/work-center.table.columns.working-state'))
                    ->badge(),
                TextColumn::make('default_capacity')
                    ->label(__('manufacturing::filament/clusters/configurations/resources/work-center.table.columns.default-capacity'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('time_efficiency')
                    ->label(__('manufacturing::filament/clusters/configurations/resources/work-center.table.columns.time-efficiency'))
                    ->suffix('%')
                    ->sortable(),
                TextColumn::make('costs_per_hour')
                    ->label(__('manufacturing::filament/clusters/configurations/resources/work-center.table.columns.costs-per-hour'))
                    ->numeric(decimalPlaces: 4)
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->label(__('manufacturing::filament/clusters/configurations/resources/work-center.table.columns.deleted-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label(__('manufacturing::filament/clusters/configurations/resources/work-center.table.columns.created-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('manufacturing::filament/clusters/configurations/resources/work-center.table.columns.updated-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('company_id')
                    ->label(__('manufacturing::filament/clusters/configurations/resources/work-center.table.filters.company'))
                    ->relationship('company', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('working_state')
                    ->label(__('manufacturing::filament/clusters/configurations/resources/work-center.table.filters.working-state'))
                    ->options(WorkCenterWorkingState::options()),
            ])
            ->groups([
                Tables\Grouping\Group::make('company.name')
                    ->label(__('manufacturing::filament/clusters/configurations/resources/work-center.table.groups.company'))
                    ->collapsible(),
            ])
            ->recordActions([
                ViewAction::make()
                    ->hidden(fn (WorkCenter $record): bool => $record->trashed()),
                EditAction::make()
                    ->hidden(fn (WorkCenter $record): bool => $record->trashed()),
                RestoreAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('manufacturing::filament/clusters/configurations/resources/work-center.table.actions.restore.notification.title'))
                            ->body(__('manufacturing::filament/clusters/configurations/resources/work-center.table.actions.restore.notification.body')),
                    ),
                DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('manufacturing::filament/clusters/configurations/resources/work-center.table.actions.delete.notification.title'))
                            ->body(__('manufacturing::filament/clusters/configurations/resources/work-center.table.actions.delete.notification.body')),
                    ),
                ForceDeleteAction::make()
                    ->action(function (WorkCenter $record, ForceDeleteAction $action): void {
                        try {
                            $record->forceDelete();
                        } catch (QueryException) {
                            Notification::make()
                                ->danger()
                                ->title(__('manufacturing::filament/clusters/configurations/resources/work-center.table.actions.force-delete.notification.error.title'))
                                ->body(__('manufacturing::filament/clusters/configurations/resources/work-center.table.actions.force-delete.notification.error.body'))
                                ->send();

                            $action->cancel();
                        }
                    })
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('manufacturing::filament/clusters/configurations/resources/work-center.table.actions.force-delete.notification.success.title'))
                            ->body(__('manufacturing::filament/clusters/configurations/resources/work-center.table.actions.force-delete.notification.success.body')),
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    RestoreBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('manufacturing::filament/clusters/configurations/resources/work-center.table.bulk-actions.restore.notification.title'))
                                ->body(__('manufacturing::filament/clusters/configurations/resources/work-center.table.bulk-actions.restore.notification.body')),
                        ),
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('manufacturing::filament/clusters/configurations/resources/work-center.table.bulk-actions.delete.notification.title'))
                                ->body(__('manufacturing::filament/clusters/configurations/resources/work-center.table.bulk-actions.delete.notification.body')),
                        ),
                    ForceDeleteBulkAction::make()
                        ->action(function (Collection $records, ForceDeleteBulkAction $action): void {
                            try {
                                $records->each(fn (Model $record): ?bool => $record->forceDelete());
                            } catch (QueryException) {
                                Notification::make()
                                    ->danger()
                                    ->title(__('manufacturing::filament/clusters/configurations/resources/work-center.table.bulk-actions.force-delete.notification.error.title'))
                                    ->body(__('manufacturing::filament/clusters/configurations/resources/work-center.table.bulk-actions.force-delete.notification.error.body'))
                                    ->send();

                                $action->cancel();
                            }
                        })
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('manufacturing::filament/clusters/configurations/resources/work-center.table.bulk-actions.force-delete.notification.success.title'))
                                ->body(__('manufacturing::filament/clusters/configurations/resources/work-center.table.bulk-actions.force-delete.notification.success.body')),
                        ),
                ]),
            ])
            ->emptyStateActions([
                CreateAction::make()
                    ->icon('heroicon-o-plus-circle'),
            ])
            ->reorderable('sort', direction: 'desc')
            ->defaultSort('sort', 'desc');
    }
}
