<?php

namespace Webkul\TimeOff\Filament\Clusters\Configurations\Resources\LeaveTypes\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators\IsRelatedToOperator;
use Filament\Tables\Filters\QueryBuilder\Constraints\TextConstraint;
use Filament\Tables\Table;
use Webkul\TimeOff\Enums\AllocationValidationType;
use Webkul\TimeOff\Enums\EmployeeRequest;
use Webkul\TimeOff\Enums\RequiresAllocation;

class LeaveTypesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('time-off::filament/clusters/configurations/resources/leave-type.table.columns.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('leave_validation_type')
                    ->label(__('time-off::filament/clusters/configurations/resources/leave-type.table.columns.time-off-approval'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                TextColumn::make('notifiedTimeOffOfficers.name')
                    ->badge()
                    ->label(__('time-off::filament/clusters/configurations/resources/leave-type.table.columns.notified-time-officers'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                TextColumn::make('requires_allocation')
                    ->badge()
                    ->label(__('time-off::filament/clusters/configurations/resources/leave-type.table.columns.requires-allocation'))
                    ->formatStateUsing(fn ($state) => RequiresAllocation::options()[$state])
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                TextColumn::make('allocation_validation_type')
                    ->label(__('time-off::filament/clusters/configurations/resources/leave-type.table.columns.allocation-approval'))
                    ->searchable()
                    ->formatStateUsing(fn ($state) => AllocationValidationType::options()[$state])
                    ->sortable(),
                TextColumn::make('employee_requests')
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label(__('time-off::filament/clusters/configurations/resources/leave-type.table.columns.employee-request'))
                    ->formatStateUsing(fn ($state) => EmployeeRequest::options()[$state])
                    ->searchable()
                    ->sortable(),
                ColorColumn::make('color')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label(__('time-off::filament/clusters/configurations/resources/leave-type.table.columns.color')),
                TextColumn::make('company.name')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
            ])
            ->filters([
                QueryBuilder::make()
                    ->constraintPickerColumns(2)
                    ->constraints([
                        TextConstraint::make('name')
                            ->label(__('time-off::filament/clusters/configurations/resources/leave-type.table.filters.name'))
                            ->icon('heroicon-o-building-office-2'),
                        TextConstraint::make('leave_validation_type')
                            ->label(__('time-off::filament/clusters/configurations/resources/leave-type.table.filters.time-off-approval'))
                            ->icon('heroicon-o-check-circle'),
                        TextConstraint::make('requires_allocation')
                            ->label(__('time-off::filament/clusters/configurations/resources/leave-type.table.filters.requires-allocation'))
                            ->icon('heroicon-o-calculator'),
                        TextConstraint::make('employee_requests')
                            ->label(__('time-off::filament/clusters/configurations/resources/leave-type.table.filters.employee-request'))
                            ->icon('heroicon-o-user-group'),
                        TextConstraint::make('time_type')
                            ->label(__('time-off::filament/clusters/configurations/resources/leave-type.table.filters.time-type'))
                            ->icon('heroicon-o-clock'),
                        TextConstraint::make('request_unit')
                            ->label(__('time-off::filament/clusters/configurations/resources/leave-type.table.filters.request-unit'))
                            ->icon('heroicon-o-clock'),
                        RelationshipConstraint::make('created_by')
                            ->label(__('time-off::filament/clusters/configurations/resources/leave-type.table.filters.created-by'))
                            ->icon('heroicon-o-user')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        RelationshipConstraint::make('company')
                            ->label(__('time-off::filament/clusters/configurations/resources/leave-type.table.filters.company-name'))
                            ->icon('heroicon-o-building-office-2')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        DateConstraint::make('created_at')
                            ->label(__('time-off::filament/clusters/configurations/resources/leave-type.table.filters.created-at')),
                        DateConstraint::make('updated_at')
                            ->label(__('time-off::filament/clusters/configurations/resources/leave-type.table.filters.updated-at')),
                    ]),
            ])
            ->filtersFormColumns(2)
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('time-off::filament/clusters/configurations/resources/leave-type.table.actions.delete.notification.title'))
                            ->body(__('time-off::filament/clusters/configurations/resources/leave-type.table.actions.delete.notification.body'))
                    ),
                RestoreAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('time-off::filament/clusters/configurations/resources/leave-type.table.actions.restore.notification.title'))
                            ->body(__('time-off::filament/clusters/configurations/resources/leave-type.table.actions.restore.notification.body'))
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('time-off::filament/clusters/configurations/resources/leave-type.table.bulk-actions.delete.notification.title'))
                                ->body(__('time-off::filament/clusters/configurations/resources/leave-type.table.bulk-actions.delete.notification.body'))
                        ),
                    ForceDeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('time-off::filament/clusters/configurations/resources/leave-type.table.bulk-actions.force-delete.notification.title'))
                                ->body(__('time-off::filament/clusters/configurations/resources/leave-type.table.bulk-actions.force-delete.notification.body'))
                        ),
                    RestoreBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('time-off::filament/clusters/configurations/resources/leave-type.table.bulk-actions.restore.notification.title'))
                                ->body(__('time-off::filament/clusters/configurations/resources/leave-type.table.bulk-actions.restore.notification.body'))
                        ),
                ]),
            ]);
    }
}
