<?php

namespace Webkul\Employee\Filament\Clusters\Configurations\Resources\Calendar\Tables;

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
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\NumberConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators\IsRelatedToOperator;
use Filament\Tables\Filters\QueryBuilder\Constraints\TextConstraint;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class CalendarTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label(__('employees::filament/clusters/configurations/resources/calendar.table.columns.id'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('name')
                    ->label('Schedule Name')
                    ->label(__('employees::filament/clusters/configurations/resources/calendar.table.columns.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('timezone')
                    ->label(__('employees::filament/clusters/configurations/resources/calendar.table.columns.timezone'))
                    ->searchable(),
                TextColumn::make('company.name')
                    ->label(__('employees::filament/clusters/configurations/resources/calendar.table.columns.company'))
                    ->searchable()
                    ->sortable(),
                IconColumn::make('flexible_hours')
                    ->sortable()
                    ->label(__('employees::filament/clusters/configurations/resources/calendar.table.columns.flexible-hours'))
                    ->boolean(),
                IconColumn::make('is_active')
                    ->sortable()
                    ->label(__('employees::filament/clusters/configurations/resources/calendar.table.columns.status'))
                    ->boolean(),
                TextColumn::make('hours_per_day')
                    ->label(__('employees::filament/clusters/configurations/resources/calendar.table.columns.daily-hours'))
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('createdBy.name')
                    ->label(__('employees::filament/clusters/configurations/resources/calendar.table.columns.created-by'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label(__('employees::filament/clusters/configurations/resources/calendar.table.columns.created-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('employees::filament/clusters/configurations/resources/calendar.table.columns.updated-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->groups([
                Tables\Grouping\Group::make('name')
                    ->label(__('employees::filament/clusters/configurations/resources/calendar.table.groups.name'))
                    ->collapsible(),
                Tables\Grouping\Group::make('timezone')
                    ->label(__('employees::filament/clusters/configurations/resources/calendar.table.groups.timezone'))
                    ->collapsible(),
                Tables\Grouping\Group::make('flexible_hours')
                    ->label(__('employees::filament/clusters/configurations/resources/calendar.table.groups.flexible-hours'))
                    ->collapsible(),
                Tables\Grouping\Group::make('is_active')
                    ->label(__('employees::filament/clusters/configurations/resources/calendar.table.groups.status'))
                    ->collapsible(),
                Tables\Grouping\Group::make('hours_per_day')
                    ->label(__('employees::filament/clusters/configurations/resources/calendar.table.groups.daily-hours'))
                    ->collapsible(),
                Tables\Grouping\Group::make('created_at')
                    ->label(__('employees::filament/clusters/configurations/resources/calendar.table.groups.created-at'))
                    ->collapsible(),
                Tables\Grouping\Group::make('updated_at')
                    ->label(__('employees::filament/clusters/configurations/resources/calendar.table.groups.updated-at'))
                    ->date()
                    ->collapsible(),
            ])
            ->filtersFormColumns(2)
            ->filters([
                SelectFilter::make('company')
                    ->relationship('company', 'name')
                    ->label(__('employees::filament/clusters/configurations/resources/calendar.table.filters.company')),
                TernaryFilter::make('is_active')
                    ->label(__('employees::filament/clusters/configurations/resources/calendar.table.filters.is-active')),
                TernaryFilter::make('two_weeks_calendar')
                    ->label(__('employees::filament/clusters/configurations/resources/calendar.table.filters.two-week-calendar')),
                TernaryFilter::make('flexible_hours')
                    ->label(__('employees::filament/clusters/configurations/resources/calendar.table.filters.flexible-hours')),
                QueryBuilder::make()
                    ->constraintPickerColumns(2)
                    ->constraints([
                        TextConstraint::make('name')
                            ->label(__('employees::filament/clusters/configurations/resources/calendar.table.filters.name'))
                            ->icon('heroicon-o-user'),
                        NumberConstraint::make('hours_per_day')
                            ->label(__('employees::filament/clusters/configurations/resources/calendar.table.filters.daily-hours'))
                            ->icon('heroicon-o-clock'),
                        NumberConstraint::make('full_time_required_hours')
                            ->label(__('employees::filament/clusters/configurations/resources/calendar.table.filters.full-time-required-hours'))
                            ->icon('heroicon-o-clock'),
                        TextConstraint::make('timezone')
                            ->label(__('employees::filament/clusters/configurations/resources/calendar.table.filters.timezone'))
                            ->icon('heroicon-o-clock'),
                        RelationshipConstraint::make('attendance')
                            ->label(__('employees::filament/clusters/configurations/resources/calendar.table.filters.attendance'))
                            ->icon('heroicon-o-building-office')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->label(__('employees::filament/clusters/configurations/resources/calendar.table.filters.attendance'))
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        RelationshipConstraint::make('company')
                            ->label(__('employees::filament/clusters/configurations/resources/calendar.table.filters.name'))
                            ->icon('heroicon-o-building-office')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        RelationshipConstraint::make('createdBy')
                            ->label('Created By')
                            ->icon('heroicon-o-user')
                            ->label(__('employees::filament/clusters/configurations/resources/calendar.table.filters.created-by'))
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        DateConstraint::make('created_at')
                            ->label(__('employees::filament/clusters/configurations/resources/calendar.table.filters.created-at')),
                        DateConstraint::make('updated_at')
                            ->label(__('employees::filament/clusters/configurations/resources/calendar.table.filters.updated-at')),
                    ]),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    RestoreAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('employees::filament/clusters/configurations/resources/calendar.table.actions.restore.notification.title'))
                                ->body(__('employees::filament/clusters/configurations/resources/calendar.table.actions.restore.notification.body')),
                        ),
                    DeleteAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('employees::filament/clusters/configurations/resources/calendar.table.actions.delete.notification.title'))
                                ->body(__('employees::filament/clusters/configurations/resources/calendar.table.actions.delete.notification.body')),
                        ),
                    ForceDeleteAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('employees::filament/clusters/configurations/resources/calendar.table.actions.force-delete.notification.title'))
                                ->body(__('employees::filament/clusters/configurations/resources/calendar.table.actions.force-delete.notification.body')),
                        ),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    RestoreBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('employees::filament/clusters/configurations/resources/calendar.table.bulk-actions.restore.notification.title'))
                                ->body(__('employees::filament/clusters/configurations/resources/calendar.table.bulk-actions.restore.notification.body')),
                        ),
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('employees::filament/clusters/configurations/resources/calendar.table.bulk-actions.delete.notification.title'))
                                ->body(__('employees::filament/clusters/configurations/resources/calendar.table.bulk-actions.delete.notification.body')),
                        ),
                    ForceDeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('employees::filament/clusters/configurations/resources/calendar.table.bulk-actions.force-delete.notification.title'))
                                ->body(__('employees::filament/clusters/configurations/resources/calendar.table.bulk-actions.force-delete.notification.body')),
                        ),
                ]),
            ])
            ->emptyStateActions([
                CreateAction::make()
                    ->icon('heroicon-o-plus-circle'),
            ]);
    }
}
