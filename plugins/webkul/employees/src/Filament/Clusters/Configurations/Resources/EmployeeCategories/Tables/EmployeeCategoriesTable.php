<?php

namespace Webkul\Employee\Filament\Clusters\Configurations\Resources\EmployeeCategories\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators\IsRelatedToOperator;
use Filament\Tables\Filters\QueryBuilder\Constraints\TextConstraint;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;

class EmployeeCategoriesTable
{
    public static function configuration(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label(__('employees::filament/clusters/configurations/resources/employee-category.table.columns.id'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('name')
                    ->label(__('employees::filament/clusters/configurations/resources/employee-category.table.columns.name'))
                    ->searchable()
                    ->sortable(),
                ColorColumn::make('color')
                    ->searchable()
                    ->label(__('employees::filament/clusters/configurations/resources/employee-category.table.columns.color'))
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->sortable(),
                TextColumn::make('createdBy.name')
                    ->label(__('employees::filament/clusters/configurations/resources/employee-category.table.columns.created-by'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label(__('employees::filament/clusters/configurations/resources/employee-category.table.columns.created-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('employees::filament/clusters/configurations/resources/employee-category.table.columns.updated-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                QueryBuilder::make()
                    ->constraintPickerColumns(2)
                    ->constraints([
                        TextConstraint::make('name')
                            ->label(__('employees::filament/clusters/configurations/resources/employee-category.table.filters.name'))
                            ->icon('heroicon-o-user'),
                        RelationshipConstraint::make('createdBy')
                            ->label(__('employees::filament/clusters/configurations/resources/employee-category.table.filters.created-by'))
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
                            ->label(__('employees::filament/clusters/configurations/resources/employee-category.table.filters.created-by')),
                        DateConstraint::make('updated_at')
                            ->label(__('employees::filament/clusters/configurations/resources/employee-category.table.filters.updated-by')),
                    ]),
            ])
            ->groups([
                Group::make('name')
                    ->label('Job Position')
                    ->label(__('employees::filament/clusters/configurations/resources/employee-category.table.groups.job-position'))
                    ->collapsible(),
                Group::make('color')
                    ->label(__('employees::filament/clusters/configurations/resources/employee-category.table.groups.color'))
                    ->collapsible(),
                Group::make('createdBy.name')
                    ->label(__('employees::filament/clusters/configurations/resources/employee-category.table.groups.created-by'))
                    ->collapsible(),
                Group::make('created_at')
                    ->label(__('employees::filament/clusters/configurations/resources/employee-category.table.groups.created-at'))
                    ->collapsible(),
                Group::make('updated_at')
                    ->label(__('employees::filament/clusters/configurations/resources/employee-category.table.groups.updated-at'))
                    ->date()
                    ->collapsible(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make()
                    ->mutateDataUsing(function (array $data): array {
                        $data['color'] = $data['color'] ?? fake()->hexColor();

                        return $data;
                    })
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('employees::filament/clusters/configurations/resources/employee-category.table.actions.edit.notification.title'))
                            ->body(__('employees::filament/clusters/configurations/resources/employee-category.table.actions.edit.notification.body'))
                    ),
                DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('employees::filament/clusters/configurations/resources/employee-category.table.actions.delete.notification.title'))
                            ->body(__('employees::filament/clusters/configurations/resources/employee-category.table.actions.delete.notification.body'))
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('employees::filament/clusters/configurations/resources/employee-category.table.bulk-actions.delete.notification.title'))
                                ->body(__('employees::filament/clusters/configurations/resources/employee-category.table.bulk-actions.delete.notification.body'))
                        ),
                ]),
            ])
            ->emptyStateActions([
                CreateAction::make()
                    ->icon('heroicon-o-plus-circle')
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('employees::filament/clusters/configurations/resources/employee-category.table.empty-state-action.create.notification.title'))
                            ->body(__('employees::filament/clusters/configurations/resources/employee-category.table.empty-state-action.create.notification.body'))
                    ),
            ]);
    }
}
