<?php

namespace Webkul\Sale\Filament\Clusters\Configuration\Resources\SaleTeams\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators\IsRelatedToOperator;
use Filament\Tables\Filters\QueryBuilder\Constraints\TextConstraint;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;

class SaleTeamsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->dateTime()
                    ->sortable()
                    ->label(__('sales::filament/clusters/configurations/resources/team.table.columns.id'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('company.name')
                    ->label(__('sales::filament/clusters/configurations/resources/team.table.columns.company'))
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label(__('sales::filament/clusters/configurations/resources/team.table.columns.team-leader'))
                    ->sortable(),
                ColorColumn::make('color')
                    ->label(__('sales::filament/clusters/configurations/resources/team.table.columns.color'))
                    ->searchable(),
                TextColumn::make('createdBy.name')
                    ->label(__('sales::filament/clusters/configurations/resources/team.table.columns.created-by'))
                    ->sortable(),
                TextColumn::make('name')
                    ->label(__('sales::filament/clusters/configurations/resources/team.table.columns.name'))
                    ->searchable(),
                IconColumn::make('is_active')
                    ->label(__('sales::filament/clusters/configurations/resources/team.table.columns.status'))
                    ->boolean(),
                TextColumn::make('invoiced_target')
                    ->numeric()
                    ->label(__('sales::filament/clusters/configurations/resources/team.table.columns.invoiced-target'))
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->label(__('sales::filament/clusters/configurations/resources/team.table.columns.created-at'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->label(__('sales::filament/clusters/configurations/resources/team.table.columns.updated-at'))
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                QueryBuilder::make()
                    ->constraintPickerColumns(2)
                    ->constraints([
                        TextConstraint::make('name')
                            ->label(__('sales::filament/clusters/configurations/resources/team.table.filters.name'))
                            ->icon('heroicon-o-user'),
                        RelationshipConstraint::make('user')
                            ->label(__('sales::filament/clusters/configurations/resources/team.table.filters.team-leader'))
                            ->icon('heroicon-o-user')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->label(__('sales::filament/clusters/configurations/resources/team.table.filters.team-leader'))
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        RelationshipConstraint::make('company')
                            ->label(__('sales::filament/clusters/configurations/resources/team.table.filters.company'))
                            ->icon('heroicon-o-building-office-2')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->label(__('sales::filament/clusters/configurations/resources/team.table.filters.company'))
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        DateConstraint::make('creator_id')
                            ->label(__('sales::filament/clusters/configurations/resources/team.table.filters.created-by')),
                        DateConstraint::make('created_at')
                            ->label(__('sales::filament/clusters/configurations/resources/team.table.filters.created-at')),
                        DateConstraint::make('updated_at')
                            ->label(__('sales::filament/clusters/configurations/resources/team.table.filters.updated-at')),
                    ]),
            ])
            ->groups([
                Group::make('name')
                    ->label(__('sales::filament/clusters/configurations/resources/team.table.groups.name'))
                    ->collapsible(),
                Group::make('company.name')
                    ->label(__('sales::filament/clusters/configurations/resources/team.table.groups.company'))
                    ->collapsible(),
                Group::make('user.name')
                    ->label(__('sales::filament/clusters/configurations/resources/team.table.groups.team-leader'))
                    ->collapsible(),
                Group::make('created_at')
                    ->label(__('sales::filament/clusters/configurations/resources/team.table.groups.created-at'))
                    ->collapsible(),
                Group::make('updated_at')
                    ->label(__('sales::filament/clusters/configurations/resources/team.table.groups.updated-at'))
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
                            ->title(__('sales::filament/clusters/configurations/resources/team.table.actions.delete.notification.title'))
                            ->body(__('sales::filament/clusters/configurations/resources/team.table.actions.delete.notification.title')),
                    ),
                RestoreAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('sales::filament/clusters/configurations/resources/team.table.actions.restore.notification.title'))
                            ->body(__('sales::filament/clusters/configurations/resources/team.table.actions.restore.notification.title')),
                    ),
                ForceDeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('sales::filament/clusters/configurations/resources/team.table.actions.force-delete.notification.title'))
                            ->body(__('sales::filament/clusters/configurations/resources/team.table.actions.force-delete.notification.title')),
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    RestoreBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('sales::filament/clusters/configurations/resources/team.table.bulk-actions.restore.notification.title'))
                                ->body(__('sales::filament/clusters/configurations/resources/team.table.bulk-actions.restore.notification.title')),
                        ),
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('sales::filament/clusters/configurations/resources/team.table.bulk-actions.delete.notification.title'))
                                ->body(__('sales::filament/clusters/configurations/resources/team.table.bulk-actions.delete.notification.title')),
                        ),
                    ForceDeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('sales::filament/clusters/configurations/resources/team.table.bulk-actions.force-delete.notification.title'))
                                ->body(__('sales::filament/clusters/configurations/resources/team.table.bulk-actions.force-delete.notification.title')),
                        ),
                ]),
            ])
            ->reorderable('sort', 'desc');
    }
}
