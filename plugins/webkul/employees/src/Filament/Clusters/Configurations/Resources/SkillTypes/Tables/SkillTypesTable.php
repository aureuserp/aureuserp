<?php

namespace Webkul\Employee\Filament\Clusters\Configurations\Resources\SkillTypes\Tables;

use Filament\Actions\ActionGroup;
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
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators\IsRelatedToOperator;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Webkul\Employee\Filament\Clusters\Configurations\Resources\SkillTypes\SkillTypeResource;

class SkillTypesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label(__('employees::filament/clusters/configurations/resources/skill-type.table.columns.id'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('name')
                    ->label(__('employees::filament/clusters/configurations/resources/skill-type.table.columns.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('color')
                    ->label(__('employees::filament/clusters/configurations/resources/skill-type.table.columns.color'))
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->formatStateUsing(fn (Model $record) => '<span class="flex h-5 w-5 rounded-full" style="background: rgb(var(--'.$record->color.'-500))"></span>')
                    ->html()
                    ->sortable(),
                TextColumn::make('skills.name')
                    ->label(__('employees::filament/clusters/configurations/resources/skill-type.table.columns.skills'))
                    ->badge()
                    ->color(fn (Model $record) => $record->color)
                    ->searchable(),
                TextColumn::make('skillLevels.name')
                    ->label(__('employees::filament/clusters/configurations/resources/skill-type.table.columns.levels'))
                    ->badge()
                    ->color('gray')
                    ->searchable(),
                IconColumn::make('is_active')
                    ->sortable()
                    ->label(__('employees::filament/clusters/configurations/resources/skill-type.table.columns.status'))
                    ->sortable()
                    ->boolean(),
                TextColumn::make('createdBy.name')
                    ->label(__('employees::filament/clusters/configurations/resources/skill-type.table.columns.created-by'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label(__('employees::filament/clusters/configurations/resources/skill-type.table.columns.created-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('employees::filament/clusters/configurations/resources/skill-type.table.columns.updated-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->columnToggleFormColumns(2)
            ->filters([
                TernaryFilter::make('is_active')
                    ->label(__('employees::filament/clusters/configurations/resources/skill-type.table.filters.status')),
                QueryBuilder::make()
                    ->constraintPickerColumns(2)
                    ->constraints([
                        RelationshipConstraint::make('skillLevels')
                            ->label(__('employees::filament/clusters/configurations/resources/skill-type.table.filters.skill-levels'))
                            ->icon('heroicon-o-bolt')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        RelationshipConstraint::make('skills')
                            ->label(__('employees::filament/clusters/configurations/resources/skill-type.table.filters.skills'))
                            ->icon('heroicon-o-bolt')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        RelationshipConstraint::make('createdBy')
                            ->label(__('employees::filament/clusters/configurations/resources/skill-type.table.filters.created-by'))
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
                            ->label(__('employees::filament/clusters/configurations/resources/skill-type.table.filters.created-at')),
                        DateConstraint::make('updated_at')
                            ->label(__('employees::filament/clusters/configurations/resources/skill-type.table.filters.updated-at')),
                    ]),
            ])
            ->filtersFormColumns(2)
            ->groups([
                Group::make('name')
                    ->label(__('employees::filament/clusters/configurations/resources/skill-type.table.groups.name'))
                    ->collapsible(),
                Group::make('color')
                    ->label(__('employees::filament/clusters/configurations/resources/skill-type.table.groups.color'))
                    ->collapsible(),
                Group::make('createdBy.name')
                    ->label(__('employees::filament/clusters/configurations/resources/skill-type.table.groups.created-by'))
                    ->collapsible(),
                Group::make('is_active')
                    ->label(__('employees::filament/clusters/configurations/resources/skill-type.table.groups.status'))
                    ->collapsible(),
                Group::make('created_at')
                    ->label(__('employees::filament/clusters/configurations/resources/skill-type.table.groups.created-at'))
                    ->collapsible(),
                Group::make('updated_at')
                    ->label(__('employees::filament/clusters/configurations/resources/skill-type.table.groups.updated-at'))
                    ->date()
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
                                ->title(__('employees::filament/clusters/configurations/resources/skill-type.table.actions.delete.notification.title'))
                                ->body(__('employees::filament/clusters/configurations/resources/skill-type.table.actions.delete.notification.body')),
                        ),
                    RestoreAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('employees::filament/clusters/configurations/resources/skill-type.table.actions.restore.notification.title'))
                                ->body(__('employees::filament/clusters/configurations/resources/skill-type.table.actions.restore.notification.body')),
                        ),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    RestoreBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('employees::filament/clusters/configurations/resources/skill-type.table.bulk-actions.restore.notification.title'))
                                ->body(__('employees::filament/clusters/configurations/resources/skill-type.table.bulk-actions.restore.notification.body')),
                        ),
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('employees::filament/clusters/configurations/resources/skill-type.table.bulk-actions.delete.notification.title'))
                                ->body(__('employees::filament/clusters/configurations/resources/skill-type.table.bulk-actions.delete.notification.body')),
                        ),
                    ForceDeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('employees::filament/clusters/configurations/resources/skill-type.table.bulk-actions.force-delete.notification.title'))
                                ->body(__('employees::filament/clusters/configurations/resources/skill-type.table.bulk-actions.force-delete.notification.body')),
                        ),
                ]),
            ])
            ->emptyStateActions([
                CreateAction::make()
                    ->icon('heroicon-o-plus-circle')
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('employees::filament/clusters/configurations/resources/skill-type.table.empty-state-actions.create.notification.title'))
                            ->body(__('employees::filament/clusters/configurations/resources/skill-type.table.empty-state-actions.create.notification.body')),
                    )
                    ->after(function ($record) {
                        return redirect(
                            SkillTypeResource::getUrl('edit', ['record' => $record])
                        );
                    }),
            ]);
    }
}
