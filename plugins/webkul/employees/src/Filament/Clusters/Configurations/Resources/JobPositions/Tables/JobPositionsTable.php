<?php

namespace Webkul\Employee\Filament\Clusters\Configurations\Resources\JobPositions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
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
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators\IsRelatedToOperator;
use Filament\Tables\Filters\QueryBuilder\Constraints\TextConstraint;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class JobPositionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label(__('employees::filament/clusters/configurations/resources/job-position.table.columns.id'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('name')
                    ->label(__('employees::filament/clusters/configurations/resources/job-position.table.columns.job-position'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('department.name')
                    ->label(__('employees::filament/clusters/configurations/resources/job-position.table.columns.department'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('company.name')
                    ->label(__('employees::filament/clusters/configurations/resources/job-position.table.columns.company'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('expected_employees')
                    ->label(__('employees::filament/clusters/configurations/resources/job-position.table.columns.expected-employees'))
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('no_of_employee')
                    ->label(__('employees::filament/clusters/configurations/resources/job-position.table.columns.current-employees'))
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('is_active')
                    ->sortable()
                    ->label(__('employees::filament/clusters/configurations/resources/job-position.table.columns.status'))
                    ->boolean(),
                TextColumn::make('createdBy.name')
                    ->label(__('employees::filament/clusters/configurations/resources/job-position.table.columns.created-by'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label(__('employees::filament/clusters/configurations/resources/job-position.table.columns.created-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('employees::filament/clusters/configurations/resources/job-position.table.columns.updated-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->columnToggleFormColumns(2)
            ->filters([
                SelectFilter::make('department')
                    ->relationship('department', 'name')
                    ->label(__('employees::filament/clusters/configurations/resources/job-position.table.filters.department')),
                SelectFilter::make('employmentType')
                    ->relationship('employmentType', 'name')
                    ->label(__('employees::filament/clusters/configurations/resources/job-position.table.filters.employment-type')),
                SelectFilter::make('company')
                    ->relationship('company', 'name')
                    ->label(__('employees::filament/clusters/configurations/resources/job-position.table.filters.company')),
                TernaryFilter::make('is_active')
                    ->label(__('employees::filament/clusters/configurations/resources/job-position.table.filters.status')),
                QueryBuilder::make()
                    ->constraintPickerColumns(2)
                    ->constraints([
                        TextConstraint::make('name')
                            ->label(__('employees::filament/clusters/configurations/resources/job-position.table.filters.job-position'))
                            ->icon('heroicon-o-building-office-2'),
                        RelationshipConstraint::make('company')
                            ->label(__('employees::filament/clusters/configurations/resources/job-position.table.filters.company'))
                            ->icon('heroicon-o-building-office')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        RelationshipConstraint::make('department')
                            ->label(__('employees::filament/clusters/configurations/resources/job-position.table.filters.department'))
                            ->icon('heroicon-o-building-office-2')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        RelationshipConstraint::make('employmentType')
                            ->label(__('employees::filament/clusters/configurations/resources/job-position.table.filters.employment-type'))
                            ->icon('heroicon-o-user')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        RelationshipConstraint::make('creator')
                            ->label(__('employees::filament/clusters/configurations/resources/job-position.table.filters.created-by'))
                            ->icon('heroicon-o-user')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        DateConstraint::make('created_at')
                            ->label(__('employees::filament/clusters/configurations/resources/job-position.table.filters.created-at')),
                        DateConstraint::make('updated_at')
                            ->label(__('employees::filament/clusters/configurations/resources/job-position.table.filters.updated-at')),
                    ]),
            ])
            ->filtersFormColumns(2)
            ->groups([
                Tables\Grouping\Group::make('name')
                    ->label(__('employees::filament/clusters/configurations/resources/job-position.table.groups.job-position'))
                    ->collapsible(),
                Tables\Grouping\Group::make('company.name')
                    ->label(__('employees::filament/clusters/configurations/resources/job-position.table.groups.company'))
                    ->collapsible(),
                Tables\Grouping\Group::make('department.name')
                    ->label(__('employees::filament/clusters/configurations/resources/job-position.table.groups.department'))
                    ->collapsible(),
                Tables\Grouping\Group::make('employmentType.name')
                    ->label(__('employees::filament/clusters/configurations/resources/job-position.table.groups.employment-type'))
                    ->collapsible(),
                Tables\Grouping\Group::make('createdBy.name')
                    ->label(__('employees::filament/clusters/configurations/resources/job-position.table.groups.created-by'))
                    ->collapsible(),
                Tables\Grouping\Group::make('created_at')
                    ->label(__('employees::filament/clusters/configurations/resources/job-position.table.groups.created-at'))
                    ->collapsible(),
                Tables\Grouping\Group::make('updated_at')
                    ->label(__('employees::filament/clusters/configurations/resources/job-position.table.groups.updated-at'))
                    ->date()
                    ->collapsible(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('employees::filament/clusters/configurations/resources/job-position.table.actions.delete.notification.title'))
                            ->body(__('employees::filament/clusters/configurations/resources/job-position.table.actions.delete.notification.body'))
                    ),
                RestoreAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('employees::filament/clusters/configurations/resources/job-position.table.actions.restore.notification.title'))
                            ->body(__('employees::filament/clusters/configurations/resources/job-position.table.actions.restore.notification.body'))
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('employees::filament/clusters/configurations/resources/job-position.table.bulk-actions.delete.notification.title'))
                                ->body(__('employees::filament/clusters/configurations/resources/job-position.table.bulk-actions.delete.notification.body'))
                        ),
                    ForceDeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('employees::filament/clusters/configurations/resources/job-position.table.bulk-actions.force-delete.notification.title'))
                                ->body(__('employees::filament/clusters/configurations/resources/job-position.table.bulk-actions.force-delete.notification.body'))
                        ),
                    RestoreBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('employees::filament/clusters/configurations/resources/job-position.table.bulk-actions.restore.notification.title'))
                                ->body(__('employees::filament/clusters/configurations/resources/job-position.table.bulk-actions.restore.notification.body'))
                        ),
                ]),
            ])
            ->emptyStateActions([
                CreateAction::make()
                    ->icon('heroicon-o-plus-circle')
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('employees::filament/clusters/configurations/resources/job-position.table.empty-state-actions.create.notification.title'))
                            ->body(__('employees::filament/clusters/configurations/resources/job-position.table.empty-state-actions.create.notification.body'))
                    ),
            ])
            ->reorderable('sort')
            ->defaultSort('sort', 'desc');
    }
}
