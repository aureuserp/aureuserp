<?php

namespace Webkul\Recruitment\Filament\Clusters\Configurations\Resources\Stages\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Filters\QueryBuilder\Constraints\BooleanConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators\IsRelatedToOperator;
use Filament\Tables\Table;

class StagesTable
{
    public static function configure(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('id')
                ->label(__('recruitments::filament/clusters/configurations/resources/stage.table.columns.id'))
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
            TextColumn::make('name')
                ->label(__('recruitments::filament/clusters/configurations/resources/stage.table.columns.name'))
                ->sortable()
                ->searchable(),
            TextColumn::make('jobs.name')
                ->placeholder('-')
                ->badge()
                ->label(__('recruitments::filament/clusters/configurations/resources/stage.table.columns.job-positions')),
            IconColumn::make('is_default')
                ->boolean()
                ->label(__('recruitments::filament/clusters/configurations/resources/stage.table.columns.default-stage')),
            IconColumn::make('fold')
                ->boolean()
                ->label(__('recruitments::filament/clusters/configurations/resources/stage.table.columns.folded')),
            IconColumn::make('hired_stage')
                ->boolean()
                ->label(__('recruitments::filament/clusters/configurations/resources/stage.table.columns.hired-stage')),
            TextColumn::make('createdBy.name')
                ->label(__('recruitments::filament/clusters/configurations/resources/stage.table.columns.created-by'))
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true)
                ->searchable(),
            TextColumn::make('created_at')
                ->label(__('recruitments::filament/clusters/configurations/resources/stage.table.columns.created-at'))
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true)
                ->searchable(),
            TextColumn::make('updated_at')
                ->label(__('recruitments::filament/clusters/configurations/resources/stage.table.columns.updated-at'))
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true)
                ->searchable(),
        ])
            ->filters([
                QueryBuilder::make()
                    ->constraintPickerColumns(2)
                    ->constraints([
                        RelationshipConstraint::make('name')
                            ->label(__('recruitments::filament/clusters/configurations/resources/stage.table.filters.name'))
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        RelationshipConstraint::make('jobs')
                            ->label(__('recruitments::filament/clusters/configurations/resources/stage.table.filters.job-position'))
                            ->multiple()
                            ->icon('heroicon-o-briefcase')
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        BooleanConstraint::make('fold')
                            ->label(__('recruitments::filament/clusters/configurations/resources/stage.table.filters.folded'))
                            ->icon('heroicon-o-briefcase'),
                        RelationshipConstraint::make('legend_normal')
                            ->label(__('recruitments::filament/clusters/configurations/resources/stage.table.filters.gray-label')),
                        RelationshipConstraint::make('legend_blocked')
                            ->label(__('recruitments::filament/clusters/configurations/resources/stage.table.filters.red-label')),
                        RelationshipConstraint::make('legend_done')
                            ->label(__('recruitments::filament/clusters/configurations/resources/stage.table.filters.green-label')),
                        RelationshipConstraint::make('createdBy')
                            ->label(__('recruitments::filament/clusters/configurations/resources/stage.table.filters.created-by'))
                            ->multiple()
                            ->icon('heroicon-o-user')
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        DateConstraint::make('created_at')
                            ->label(__('recruitments::filament/clusters/configurations/resources/stage.table.filters.created-at')),
                        DateConstraint::make('updated_at')
                            ->label(__('recruitments::filament/clusters/configurations/resources/stage.table.filters.updated-at')),
                    ]),
            ])
            ->filtersFormColumns(2)
            ->groups([
                Tables\Grouping\Group::make('name')
                    ->label(__('recruitments::filament/clusters/configurations/resources/stage.table.groups.stage-name'))
                    ->collapsible(),
                Tables\Grouping\Group::make('fold')
                    ->label(__('recruitments::filament/clusters/configurations/resources/stage.table.groups.folded'))
                    ->collapsible(),
                Tables\Grouping\Group::make('legend_normal')
                    ->label(__('recruitments::filament/clusters/configurations/resources/stage.table.groups.gray-label'))
                    ->collapsible(),
                Tables\Grouping\Group::make('legend_blocked')
                    ->label(__('recruitments::filament/clusters/configurations/resources/stage.table.groups.red-label'))
                    ->collapsible(),
                Tables\Grouping\Group::make('legend_done')
                    ->label(__('recruitments::filament/clusters/configurations/resources/stage.table.groups.green-label'))
                    ->collapsible(),
                Tables\Grouping\Group::make('createdBy.name')
                    ->label(__('recruitments::filament/clusters/configurations/resources/stage.table.groups.created-by'))
                    ->collapsible(),
                Tables\Grouping\Group::make('created_at')
                    ->label(__('recruitments::filament/clusters/configurations/resources/stage.table.groups.created-at'))
                    ->collapsible(),
                Tables\Grouping\Group::make('updated_at')
                    ->label(__('recruitments::filament/clusters/configurations/resources/stage.table.groups.updated-at'))
                    ->date()
                    ->collapsible(),
            ])
            ->emptyStateActions([
                CreateAction::make()
                    ->label(__('recruitments::filament/clusters/configurations/resources/stage.table.empty-state-actions.create.label'))
                    ->icon('heroicon-o-plus-circle'),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('recruitments::filament/clusters/configurations/resources/stage.table.actions.delete.notification.title'))
                            ->body(__('recruitments::filament/clusters/configurations/resources/stage.table.actions.delete.notification.body'))
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('recruitments::filament/clusters/configurations/resources/stage.table.bulk-actions.delete.notification.title'))
                                ->body(__('recruitments::filament/clusters/configurations/resources/stage.table.bulk-actions.delete.notification.body'))
                        ),
                ]),
            ])
            ->reorderable('sort', 'Desc');
    }
}
