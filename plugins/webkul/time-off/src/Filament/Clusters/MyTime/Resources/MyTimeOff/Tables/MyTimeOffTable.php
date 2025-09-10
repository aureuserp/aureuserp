<?php

namespace Webkul\TimeOff\Filament\Clusters\MyTime\Resources\MyTimeOff\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Webkul\TimeOff\Enums\State;

class MyTimeOffTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('employee.name')
                    ->label(__('time-off::filament/clusters/my-time/resources/my-time-off.table.columns.employee-name'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('holidayStatus.name')
                    ->label(__('time-off::filament/clusters/my-time/resources/my-time-off.table.columns.time-off-type'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('private_name')
                    ->label(__('time-off::filament/clusters/my-time/resources/my-time-off.table.columns.description'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('date_from')
                    ->label(__('time-off::filament/clusters/my-time/resources/my-time-off.table.columns.date-from'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('date_to')
                    ->label(__('time-off::filament/clusters/my-time/resources/my-time-off.table.columns.date-to'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('duration_display')
                    ->label(__('time-off::filament/clusters/my-time/resources/my-time-off.table.columns.duration'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('state')
                    ->label(__('time-off::filament/clusters/my-time/resources/my-time-off.table.columns.status'))
                    ->formatStateUsing(fn (State $state) => State::options()[$state->value])
                    ->sortable()
                    ->badge()
                    ->searchable(),
            ])
            ->groups([
                Tables\Grouping\Group::make('employee.name')
                    ->label(__('Employee Name'))
                    ->label(__('time-off::filament/clusters/my-time/resources/my-time-off.table.groups.employee-name'))
                    ->collapsible(),
                Tables\Grouping\Group::make('holidayStatus.name')
                    ->label(__('time-off::filament/clusters/my-time/resources/my-time-off.table.groups.time-off-type'))
                    ->collapsible(),
                Tables\Grouping\Group::make('state')
                    ->label(__('time-off::filament/clusters/my-time/resources/my-time-off.table.groups.status'))
                    ->collapsible(),
                Tables\Grouping\Group::make('date_from')
                    ->label(__('time-off::filament/clusters/my-time/resources/my-time-off.table.groups.start-date'))
                    ->collapsible(),
                Tables\Grouping\Group::make('date_to')
                    ->label(__('time-off::filament/clusters/my-time/resources/my-time-off.table.groups.start-to'))
                    ->collapsible(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('time-off::filament/clusters/my-time/resources/my-time-off.table.actions.delete.notification.title'))
                            ->body(__('time-off::filament/clusters/my-time/resources/my-time-off.table.actions.delete.notification.body'))
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
                            ->title(__('time-off::filament/clusters/my-time/resources/my-time-off.table.actions.approve.notification.title'))
                            ->body(__('time-off::filament/clusters/my-time/resources/my-time-off.table.actions.approve.notification.body'))
                            ->send();
                    })
                    ->label(function ($record) {
                        if ($record->state === State::VALIDATE_ONE->value) {
                            return __('time-off::filament/clusters/my-time/resources/my-time-off.table.actions.approve.title.validate');
                        } else {
                            return __('time-off::filament/clusters/my-time/resources/my-time-off.table.actions.approve.title.approve');
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
                            ->title(__('time-off::filament/clusters/my-time/resources/my-time-off.table.actions.refused.notification.title'))
                            ->body(__('time-off::filament/clusters/my-time/resources/my-time-off.table.actions.refused.notification.body'))
                            ->send();
                    })
                    ->label(__('time-off::filament/clusters/my-time/resources/my-time-off.table.actions.refused.title')),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('time-off::filament/clusters/my-time/resources/my-time-off.table.bulk-actions.delete.notification.title'))
                                ->body(__('time-off::filament/clusters/my-time/resources/my-time-off.table.bulk-actions.delete.notification.body'))
                        ),
                ]),
            ])
            ->modifyQueryUsing(function ($query) {
                $query->where('employee_id', Auth::user()?->employee?->id);
            });
    }
}
