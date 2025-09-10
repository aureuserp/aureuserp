<?php

namespace Webkul\TimeOff\Filament\Clusters\Management\Resources\Allocations\Tables;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Webkul\TimeOff\Enums\AllocationType;
use Webkul\TimeOff\Enums\State;

class AllocationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('employee.name')
                    ->label(__('time-off::filament/clusters/management/resources/allocation.table.columns.employee-name'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('holidayStatus.name')
                    ->label(__('time-off::filament/clusters/management/resources/allocation.table.columns.time-off-type'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('number_of_days')
                    ->label(__('time-off::filament/clusters/management/resources/allocation.table.columns.amount'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('allocation_type')
                    ->formatStateUsing(fn ($state) => AllocationType::options()[$state])
                    ->label(__('time-off::filament/clusters/management/resources/allocation.table.columns.allocation-type'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('state')
                    ->formatStateUsing(fn ($state) => State::options()[$state])
                    ->label(__('time-off::filament/clusters/management/resources/allocation.table.columns.status'))
                    ->badge()
                    ->sortable()
                    ->searchable(),
            ])
            ->groups([
                Tables\Grouping\Group::make('employee.name')
                    ->label(__('time-off::filament/clusters/management/resources/allocation.table.groups.employee-name'))
                    ->collapsible(),
                Tables\Grouping\Group::make('holidayStatus.name')
                    ->label(__('time-off::filament/clusters/management/resources/allocation.table.groups.time-off-type'))
                    ->collapsible(),
                Tables\Grouping\Group::make('allocation_type')
                    ->label(__('time-off::filament/clusters/management/resources/allocation.table.groups.allocation-type'))
                    ->collapsible(),
                Tables\Grouping\Group::make('state')
                    ->label(__('time-off::filament/clusters/management/resources/allocation.table.groups.status'))
                    ->collapsible(),
                Tables\Grouping\Group::make('date_from')
                    ->label(__('time-off::filament/clusters/management/resources/allocation.table.groups.start-date'))
                    ->collapsible(),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('time-off::filament/clusters/management/resources/allocation.table.actions.delete.notification.title'))
                                ->body(__('time-off::filament/clusters/management/resources/allocation.table.actions.delete.notification.body'))
                        ),
                    Action::make('approve')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->hidden(fn ($record) => $record->state === State::VALIDATE_TWO->value)
                        ->action(function ($record) {
                            if ($record->state === State::VALIDATE_ONE->value) {
                                $record->update(['state' => State::VALIDATE_TWO->value]);
                            } else {
                                $record->update(['state' => State::VALIDATE_TWO->value]);
                            }

                            Notification::make()
                                ->success()
                                ->title(__('time-off::filament/clusters/management/resources/allocation.table.actions.approve.notification.title'))
                                ->body(__('time-off::filament/clusters/management/resources/allocation.table.actions.approve.notification.body'))
                                ->send();
                        })
                        ->label(function ($record) {
                            if ($record->state === State::VALIDATE_ONE->value) {
                                return __('time-off::filament/clusters/management/resources/allocation.table.actions.approve.title.validate');
                            } else {
                                return __('time-off::filament/clusters/management/resources/allocation.table.actions.approve.title.approve');
                            }
                        }),
                    Action::make('refuse')
                        ->icon('heroicon-o-x-circle')
                        ->hidden(fn ($record) => $record->state === State::REFUSE->value)
                        ->color('danger')
                        ->action(function ($record) {
                            $record->update(['state' => State::REFUSE->value]);

                            Notification::make()
                                ->success()
                                ->title(__('time-off::filament/clusters/management/resources/allocation.table.actions.refused.notification.title'))
                                ->body(__('time-off::filament/clusters/management/resources/allocation.table.actions.refused.notification.body'))
                                ->send();
                        })
                        ->label(__('time-off::filament/clusters/management/resources/allocation.table.actions.refused.title')),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('time-off::filament/clusters/management/resources/allocation.table.bulk-actions.delete.notification.title'))
                                ->body(__('time-off::filament/clusters/management/resources/allocation.table.bulk-actions.delete.notification.body'))
                        ),
                ]),
            ]);
    }
}
