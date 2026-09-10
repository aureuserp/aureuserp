<?php

namespace Webkul\Maintenance\Filament\Resources\EquipmentResource\Tables;

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
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group as TableGroup;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Webkul\Maintenance\Models\Equipment;

class EquipmentTable
{
    public static function configure(Table $table, array $customColumns = [], array $customFilters = []): Table
    {
        return $table
            ->columns(array_merge([
                TextColumn::make('name')
                    ->label(__('maintenance::filament/resources/equipment.table.columns.name'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('owner.name')
                    ->label(__('maintenance::filament/resources/equipment.table.columns.owner'))
                    ->placeholder('—')
                    ->sortable(),

                TextColumn::make('serial_no')
                    ->label(__('maintenance::filament/resources/equipment.table.columns.serial-no'))
                    ->placeholder('—')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('technician.name')
                    ->label(__('maintenance::filament/resources/equipment.table.columns.technician'))
                    ->placeholder('—')
                    ->sortable(),

                TextColumn::make('category.name')
                    ->label(__('maintenance::filament/resources/equipment.table.columns.category'))
                    ->placeholder('—')
                    ->sortable(),

                TextColumn::make('company.name')
                    ->label(__('maintenance::filament/resources/equipment.table.columns.company'))
                    ->placeholder('—')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label(__('maintenance::filament/resources/equipment.table.columns.created-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ], $customColumns))
            ->filters(array_merge([
                SelectFilter::make('category_id')
                    ->label(__('maintenance::filament/resources/equipment.table.filters.category'))
                    ->relationship('category', 'name')
                    ->native(false)
                    ->searchable()
                    ->preload(),

                SelectFilter::make('maintenance_team_id')
                    ->label(__('maintenance::filament/resources/equipment.table.filters.team'))
                    ->relationship('team', 'name')
                    ->native(false)
                    ->searchable()
                    ->preload(),

                SelectFilter::make('technician_user_id')
                    ->label(__('maintenance::filament/resources/equipment.table.filters.technician'))
                    ->relationship('technician', 'name')
                    ->native(false)
                    ->searchable()
                    ->preload(),
            ], $customFilters))
            ->groups([
                TableGroup::make('technician.name')
                    ->label(__('maintenance::filament/resources/equipment.table.groups.technician')),
                TableGroup::make('category.name')
                    ->label(__('maintenance::filament/resources/equipment.table.groups.category')),
                TableGroup::make('owner.name')
                    ->label(__('maintenance::filament/resources/equipment.table.groups.owner')),
                TableGroup::make('partner.name')
                    ->label(__('maintenance::filament/resources/equipment.table.groups.vendor')),
            ])
            ->recordActions([
                ViewAction::make()
                    ->hidden(fn ($record) => $record->trashed()),
                EditAction::make()
                    ->hidden(fn ($record) => $record->trashed())
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('maintenance::filament/resources/equipment.table.actions.edit.notification.title'))
                            ->body(__('maintenance::filament/resources/equipment.table.actions.edit.notification.body')),
                    ),
                RestoreAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('maintenance::filament/resources/equipment.table.actions.restore.notification.title'))
                            ->body(__('maintenance::filament/resources/equipment.table.actions.restore.notification.body')),
                    ),
                DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('maintenance::filament/resources/equipment.table.actions.delete.notification.title'))
                            ->body(__('maintenance::filament/resources/equipment.table.actions.delete.notification.body')),
                    ),
                ForceDeleteAction::make()
                    ->action(function (Equipment $record): void {
                        try {
                            $record->forceDelete();

                            Notification::make()
                                ->success()
                                ->title(__('maintenance::filament/resources/equipment.table.actions.force-delete.notification.success.title'))
                                ->body(__('maintenance::filament/resources/equipment.table.actions.force-delete.notification.success.body'))
                                ->send();
                        } catch (QueryException) {
                            Notification::make()
                                ->danger()
                                ->title(__('maintenance::filament/resources/equipment.table.actions.force-delete.notification.error.title'))
                                ->body(__('maintenance::filament/resources/equipment.table.actions.force-delete.notification.error.body'))
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
                                ->title(__('maintenance::filament/resources/equipment.table.bulk-actions.restore.notification.title'))
                                ->body(__('maintenance::filament/resources/equipment.table.bulk-actions.restore.notification.body')),
                        ),
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('maintenance::filament/resources/equipment.table.bulk-actions.delete.notification.title'))
                                ->body(__('maintenance::filament/resources/equipment.table.bulk-actions.delete.notification.body')),
                        ),
                    ForceDeleteBulkAction::make()
                        ->action(function (Collection $records): void {
                            try {
                                $records->each(fn (Model $record) => $record->forceDelete());

                                Notification::make()
                                    ->success()
                                    ->title(__('maintenance::filament/resources/equipment.table.actions.force-delete.notification.success.title'))
                                    ->body(__('maintenance::filament/resources/equipment.table.actions.force-delete.notification.success.body'))
                                    ->send();
                            } catch (QueryException) {
                                Notification::make()
                                    ->danger()
                                    ->title(__('maintenance::filament/resources/equipment.table.actions.force-delete.notification.error.title'))
                                    ->body(__('maintenance::filament/resources/equipment.table.actions.force-delete.notification.error.body'))
                                    ->send();
                            }
                        }),
                ]),
            ])
            ->emptyStateActions([
                CreateAction::make()
                    ->icon('heroicon-o-plus-circle')
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('maintenance::filament/resources/equipment.table.empty-state.create.notification.title'))
                            ->body(__('maintenance::filament/resources/equipment.table.empty-state.create.notification.body')),
                    ),
            ]);
    }
}
