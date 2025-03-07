<?php

namespace Webkul\Recruitment\Filament\Clusters\Configurations\Resources;

use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators\IsRelatedToOperator;
use Filament\Tables\Table;
use Webkul\Employee\Filament\Clusters\Configurations\Resources\ActivityPlanResource as BaseActivityPlanResource;
use Webkul\Recruitment\Filament\Clusters\Configurations;
use Webkul\Recruitment\Filament\Clusters\Configurations\Resources\ActivityPlanResource\Pages;
use Webkul\Recruitment\Models\ActivityPlan;

class ActivityPlanResource extends BaseActivityPlanResource
{
    protected static ?string $model = ActivityPlan::class;

    protected static ?string $cluster = Configurations::class;

    public static function getNavigationGroup(): string
    {
        return __('recruitments::filament/clusters/configurations/resources/activity-plan.navigation.group');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('recruitments::filament/clusters/configurations/resources/activity-plan.table.columns.name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('department.name')
                    ->numeric()
                    ->label(__('recruitments::filament/clusters/configurations/resources/activity-plan.table.columns.department'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('department.manager.name')
                    ->numeric()
                    ->label(__('recruitments::filament/clusters/configurations/resources/activity-plan.table.columns.manager'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('company.name')
                    ->numeric()
                    ->label(__('recruitments::filament/clusters/configurations/resources/activity-plan.table.columns.company'))
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label(__('recruitments::filament/clusters/configurations/resources/activity-plan.table.columns.status'))
                    ->sortable()
                    ->boolean(),
                Tables\Columns\TextColumn::make('createdBy.name')
                    ->label(__('recruitments::filament/clusters/configurations/resources/activity-plan.table.columns.created-by'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('recruitments::filament/clusters/configurations/resources/activity-plan.table.columns.created-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('recruitments::filament/clusters/configurations/resources/activity-plan.table.columns.updated-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label(__('recruitments::filament/clusters/configurations/resources/activity-plan.table.filters.is-active')),
                Tables\Filters\QueryBuilder::make()
                    ->constraintPickerColumns(2)
                    ->constraints([
                        Tables\Filters\QueryBuilder\Constraints\TextConstraint::make('name')
                            ->label(__('recruitments::filament/clusters/configurations/resources/activity-plan.table.filters.name'))
                            ->icon('heroicon-o-briefcase'),
                        Tables\Filters\QueryBuilder\Constraints\TextConstraint::make('plugin')
                            ->label(__('recruitments::filament/clusters/configurations/resources/activity-plan.table.filters.plugin'))
                            ->icon('heroicon-o-briefcase'),
                        Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint::make('activityTypes')
                            ->label(__('recruitments::filament/clusters/configurations/resources/activity-plan.table.filters.activity-types'))
                            ->icon('heroicon-o-briefcase')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->label(__('recruitments::filament/clusters/configurations/resources/activity-plan.table.filters.activity-types'))
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint::make('company')
                            ->label(__('recruitments::filament/clusters/configurations/resources/activity-plan.table.filters.company'))
                            ->icon('heroicon-o-building-office-2')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->label(__('recruitments::filament/clusters/configurations/resources/activity-plan.table.filters.company'))
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint::make('department')
                            ->label(__('recruitments::filament/clusters/configurations/resources/activity-plan.table.filters.department'))
                            ->icon('heroicon-o-building-office-2')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->label(__('recruitments::filament/clusters/configurations/resources/activity-plan.table.filters.department'))
                                    ->multiple()
                                    ->preload(),
                            ),
                        Tables\Filters\QueryBuilder\Constraints\DateConstraint::make('created_at')
                            ->label(__('recruitments::filament/clusters/configurations/resources/activity-plan.table.filters.created-at')),
                        Tables\Filters\QueryBuilder\Constraints\DateConstraint::make('updated_at')
                            ->label(__('recruitments::filament/clusters/configurations/resources/activity-plan.table.filters.updated-at')),
                    ]),
            ])
            ->groups([
                Tables\Grouping\Group::make('name')
                    ->label(__('recruitments::filament/clusters/configurations/resources/activity-plan.table.groups.name'))
                    ->collapsible(),
                Tables\Grouping\Group::make('createdBy.name')
                    ->label(__('recruitments::filament/clusters/configurations/resources/activity-plan.table.groups.created-by'))
                    ->collapsible(),
                Tables\Grouping\Group::make('is_active')
                    ->label(__('recruitments::filament/clusters/configurations/resources/activity-plan.table.groups.status'))
                    ->collapsible(),
                Tables\Grouping\Group::make('created_at')
                    ->label(__('recruitments::filament/clusters/configurations/resources/activity-plan.table.groups.created-at'))
                    ->collapsible(),
                Tables\Grouping\Group::make('updated_at')
                    ->label(__('recruitments::filament/clusters/configurations/resources/activity-plan.table.groups.updated-at'))
                    ->date()
                    ->collapsible(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->hidden(fn($record) => $record->trashed()),
                Tables\Actions\EditAction::make()
                    ->hidden(fn($record) => $record->trashed()),
                Tables\Actions\RestoreAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('recruitments::filament/clusters/configurations/resources/activity-plan.table.actions.restore.notification.title'))
                            ->body(__('recruitments::filament/clusters/configurations/resources/activity-plan.table.actions.restore.notification.body')),
                    ),
                Tables\Actions\DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('recruitments::filament/clusters/configurations/resources/activity-plan.table.actions.delete.notification.title'))
                            ->body(__('recruitments::filament/clusters/configurations/resources/activity-plan.table.actions.delete.notification.body')),
                    ),
                Tables\Actions\ForceDeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('recruitments::filament/clusters/configurations/resources/activity-plan.table.actions.force-delete.notification.title'))
                            ->body(__('recruitments::filament/clusters/configurations/resources/activity-plan.table.actions.force-delete.notification.body')),
                    ),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\RestoreBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('recruitments::filament/clusters/configurations/resources/activity-plan.table.bulk-actions.restore.notification.title'))
                                ->body(__('recruitments::filament/clusters/configurations/resources/activity-plan.table.bulk-actions.restore.notification.body')),
                        ),
                    Tables\Actions\DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('recruitments::filament/clusters/configurations/resources/activity-plan.table.bulk-actions.delete.notification.title'))
                                ->body(__('recruitments::filament/clusters/configurations/resources/activity-plan.table.bulk-actions.delete.notification.body')),
                        ),
                    Tables\Actions\ForceDeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('recruitments::filament/clusters/configurations/resources/activity-plan.table.bulk-actions.force-delete.notification.title'))
                                ->body(__('recruitments::filament/clusters/configurations/resources/activity-plan.table.bulk-actions.force-delete.notification.body')),
                        ),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->icon('heroicon-o-plus-circle'),
            ])
            ->modifyQueryUsing(function ($query) {
                $query->where('plugin', 'recruitments');
            });
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListActivityPlans::route('/'),
            'edit'   => Pages\EditActivityPlan::route('/{record}/edit'),
            'view'   => Pages\ViewActivityPlan::route('/{record}'),
        ];
    }
}
