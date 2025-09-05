<?php

namespace Webkul\Employee\Filament\Clusters\Configurations\Resources\EmploymentTypes\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators\IsRelatedToOperator;
use Filament\Tables\Filters\QueryBuilder\Constraints\TextConstraint;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;

class EmploymentTypesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label(__('employees::filament/clusters/configurations/resources/employment-type.table.columns.id'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('name')
                    ->sortable()
                    ->searchable()
                    ->label(__('employees::filament/clusters/configurations/resources/employment-type.table.columns.name')),
                TextColumn::make('code')
                    ->sortable()
                    ->searchable()
                    ->label(__('employees::filament/clusters/configurations/resources/employment-type.table.columns.code')),
                TextColumn::make('country.name')
                    ->sortable()
                    ->searchable()
                    ->label(__('employees::filament/clusters/configurations/resources/employment-type.table.columns.country')),
                TextColumn::make('createdBy.name')
                    ->label(__('employees::filament/clusters/configurations/resources/employment-type.table.columns.created-by'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label(__('employees::filament/clusters/configurations/resources/employment-type.table.columns.created-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('employees::filament/clusters/configurations/resources/employment-type.table.columns.updated-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                QueryBuilder::make()
                    ->constraintPickerColumns(2)
                    ->constraints([
                        TextConstraint::make('name')
                            ->label(__('employees::filament/clusters/configurations/resources/employment-type.table.filters.name'))
                            ->icon('heroicon-o-user'),
                        RelationshipConstraint::make('country')
                            ->label(__('employees::filament/clusters/configurations/resources/employment-type.table.filters.country'))
                            ->icon('heroicon-o-map')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        RelationshipConstraint::make('createdBy')
                            ->label(__('employees::filament/clusters/configurations/resources/employment-type.table.filters.created-by'))
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
                            ->label(__('employees::filament/clusters/configurations/resources/employment-type.table.filters.created-at')),
                        DateConstraint::make('updated_at')
                            ->label(__('employees::filament/clusters/configurations/resources/employment-type.table.filters.updated-at')),
                    ]),
            ])
            ->groups([
                Group::make('name')
                    ->label(__('employees::filament/clusters/configurations/resources/employment-type.table.groups.name'))
                    ->collapsible(),
                Group::make('code')
                    ->label(__('employees::filament/clusters/configurations/resources/employment-type.table.groups.code'))
                    ->collapsible(),
                Group::make('country.name')
                    ->label(__('employees::filament/clusters/configurations/resources/employment-type.table.groups.country'))
                    ->collapsible(),
                Group::make('createdBy.name')
                    ->label(__('employees::filament/clusters/configurations/resources/employment-type.table.groups.created-by'))
                    ->collapsible(),
                Group::make('created_at')
                    ->label(__('employees::filament/clusters/configurations/resources/employment-type.table.groups.created-at'))
                    ->collapsible(),
                Group::make('updated_at')
                    ->label(__('employees::filament/clusters/configurations/resources/employment-type.table.groups.updated-at'))
                    ->date()
                    ->collapsible(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make()
                    ->mutateDataUsing(function (array $data): array {
                        $data['code'] = $data['code'] ?? $data['name'];

                        return $data;
                    })
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('employees::filament/clusters/configurations/resources/employment-type.table.actions.edit.notification.title'))
                            ->body(__('employees::filament/clusters/configurations/resources/employment-type.table.actions.edit.notification.body'))
                    ),
                DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('employees::filament/clusters/configurations/resources/employment-type.table.actions.delete.notification.title'))
                            ->body(__('employees::filament/clusters/configurations/resources/employment-type.table.actions.delete.notification.body'))
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('employees::filament/clusters/configurations/resources/employment-type.table.bulk-actions.delete.notification.title'))
                                ->body(__('employees::filament/clusters/configurations/resources/employment-type.table.bulk-actions.delete.notification.body'))
                        ),
                ]),
            ])
            ->emptyStateActions([
                CreateAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('employees::filament/clusters/configurations/resources/employment-type.table.empty-state-actions.create.notification.title'))
                            ->body(__('employees::filament/clusters/configurations/resources/employment-type.table.empty-state-actions.create.notification.body'))
                    )
                    ->icon('heroicon-o-plus-circle'),
            ])
            ->reorderable('sort')
            ->defaultSort('sort', 'desc');
    }
}
