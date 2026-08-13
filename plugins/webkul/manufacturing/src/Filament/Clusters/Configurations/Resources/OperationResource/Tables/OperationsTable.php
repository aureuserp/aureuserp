<?php

namespace Webkul\Manufacturing\Filament\Clusters\Configurations\Resources\OperationResource\Tables;

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
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Webkul\Manufacturing\Enums\OperationTimeMode;
use Webkul\Manufacturing\Enums\OperationWorksheetType;
use Webkul\Manufacturing\Filament\Clusters\Configurations\Resources\OperationResource;
use Webkul\Manufacturing\Models\Operation;

class OperationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->reorderableColumns()
            ->modifyQueryUsing(fn (Builder $query) => $query->with('billOfMaterial'))
            ->columns([
                TextColumn::make('name')
                    ->label(__('manufacturing::filament/clusters/configurations/resources/operation.table.columns.name'))
                    ->searchable(),
                TextColumn::make('bill_of_material_id')
                    ->label(__('manufacturing::filament/clusters/configurations/resources/operation.table.columns.bill-of-material'))
                    ->formatStateUsing(function (mixed $state, Operation $record): string {
                        return OperationResource::getBillOfMaterialLabel($record->billOfMaterial);
                    })
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->whereHas('billOfMaterial', function (Builder $billOfMaterialQuery) use ($search): void {
                            $billOfMaterialQuery
                                ->whereLike('code', "%{$search}%")
                                ->orWhereHas('product', fn (Builder $productQuery) => $productQuery->whereLike('name', "%{$search}%"));
                        });
                    }),
                TextColumn::make('workCenter.name')
                    ->label(__('manufacturing::filament/clusters/configurations/resources/operation.table.columns.work-center'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('time_mode')
                    ->label(__('manufacturing::filament/clusters/configurations/resources/operation.table.columns.time-mode'))
                    ->badge(),
                TextColumn::make('manual_cycle_time')
                    ->label(__('manufacturing::filament/clusters/configurations/resources/operation.table.columns.manual-cycle-time'))
                    ->formatStateUsing(fn (mixed $state): string => format_float_time($state ?? 60, 'minutes'))
                    ->toggleable(),
                TextColumn::make('worksheet_type')
                    ->label(__('manufacturing::filament/clusters/configurations/resources/operation.table.columns.worksheet-type'))
                    ->badge(),
                TextColumn::make('deleted_at')
                    ->label(__('manufacturing::filament/clusters/configurations/resources/operation.table.columns.deleted-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label(__('manufacturing::filament/clusters/configurations/resources/operation.table.columns.created-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('manufacturing::filament/clusters/configurations/resources/operation.table.columns.updated-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('work_center_id')
                    ->label(__('manufacturing::filament/clusters/configurations/resources/operation.table.filters.work-center'))
                    ->relationship('workCenter', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('time_mode')
                    ->label(__('manufacturing::filament/clusters/configurations/resources/operation.table.filters.time-mode'))
                    ->options(OperationTimeMode::options()),
                SelectFilter::make('worksheet_type')
                    ->label(__('manufacturing::filament/clusters/configurations/resources/operation.table.filters.worksheet-type'))
                    ->options(OperationWorksheetType::options()),
            ])
            ->recordActions([
                ViewAction::make()
                    ->hidden(fn (Operation $record): bool => $record->trashed()),
                EditAction::make()
                    ->hidden(fn (Operation $record): bool => $record->trashed())
                    ->modalWidth(Width::SevenExtraLarge),
                RestoreAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('manufacturing::filament/clusters/configurations/resources/operation.table.actions.restore.notification.title'))
                            ->body(__('manufacturing::filament/clusters/configurations/resources/operation.table.actions.restore.notification.body')),
                    ),
                DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('manufacturing::filament/clusters/configurations/resources/operation.table.actions.delete.notification.title'))
                            ->body(__('manufacturing::filament/clusters/configurations/resources/operation.table.actions.delete.notification.body')),
                    ),
                ForceDeleteAction::make()
                    ->action(function (Operation $record, ForceDeleteAction $action): void {
                        try {
                            $record->forceDelete();
                        } catch (QueryException) {
                            Notification::make()
                                ->danger()
                                ->title(__('manufacturing::filament/clusters/configurations/resources/operation.table.actions.force-delete.notification.error.title'))
                                ->body(__('manufacturing::filament/clusters/configurations/resources/operation.table.actions.force-delete.notification.error.body'))
                                ->send();

                            $action->cancel();
                        }
                    })
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('manufacturing::filament/clusters/configurations/resources/operation.table.actions.force-delete.notification.success.title'))
                            ->body(__('manufacturing::filament/clusters/configurations/resources/operation.table.actions.force-delete.notification.success.body')),
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    RestoreBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('manufacturing::filament/clusters/configurations/resources/operation.table.bulk-actions.restore.notification.title'))
                                ->body(__('manufacturing::filament/clusters/configurations/resources/operation.table.bulk-actions.restore.notification.body')),
                        ),
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('manufacturing::filament/clusters/configurations/resources/operation.table.bulk-actions.delete.notification.title'))
                                ->body(__('manufacturing::filament/clusters/configurations/resources/operation.table.bulk-actions.delete.notification.body')),
                        ),
                    ForceDeleteBulkAction::make()
                        ->action(function (Collection $records, ForceDeleteBulkAction $action): void {
                            try {
                                $records->each(fn (Model $record): ?bool => $record->forceDelete());
                            } catch (QueryException) {
                                Notification::make()
                                    ->danger()
                                    ->title(__('manufacturing::filament/clusters/configurations/resources/operation.table.bulk-actions.force-delete.notification.error.title'))
                                    ->body(__('manufacturing::filament/clusters/configurations/resources/operation.table.bulk-actions.force-delete.notification.error.body'))
                                    ->send();

                                $action->cancel();
                            }
                        })
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('manufacturing::filament/clusters/configurations/resources/operation.table.bulk-actions.force-delete.notification.success.title'))
                                ->body(__('manufacturing::filament/clusters/configurations/resources/operation.table.bulk-actions.force-delete.notification.success.body')),
                        ),
                ]),
            ])
            ->emptyStateActions([
                CreateAction::make()
                    ->icon('heroicon-o-plus-circle'),
            ])
            ->reorderable('sort')
            ->defaultSort('sort');
    }
}
