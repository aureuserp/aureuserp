<?php

namespace Webkul\Sale\Filament\Clusters\Configuration\Resources\ActivityPlans\Tables;

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
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators\IsRelatedToOperator;
use Filament\Tables\Filters\QueryBuilder\Constraints\TextConstraint;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class ActivityPlansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('sales::filament/clusters/configurations/resources/activity-plan.table.columns.name'))
                    ->searchable(),
                TextColumn::make('department.name')
                    ->label(__('sales::filament/clusters/configurations/resources/activity-plan.table.columns.department'))
                    ->sortable(),
                TextColumn::make('department.manager.name')
                    ->label(__('sales::filament/clusters/configurations/resources/activity-plan.table.columns.manager'))
                    ->sortable(),
                TextColumn::make('company.name')
                    ->label(__('sales::filament/clusters/configurations/resources/activity-plan.table.columns.company'))
                    ->sortable(),
                IconColumn::make('is_active')
                    ->label(__('sales::filament/clusters/configurations/resources/activity-plan.table.columns.status'))
                    ->sortable()
                    ->boolean(),
                TextColumn::make('createdBy.name')
                    ->label(__('sales::filament/clusters/configurations/resources/activity-plan.table.columns.created-by'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label(__('sales::filament/clusters/configurations/resources/activity-plan.table.columns.created-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('sales::filament/clusters/configurations/resources/activity-plan.table.columns.updated-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label(__('sales::filament/clusters/configurations/resources/activity-plan.table.filters.is-active')),
                QueryBuilder::make()
                    ->constraintPickerColumns(2)
                    ->constraints([
                        TextConstraint::make('name')
                            ->label(__('sales::filament/clusters/configurations/resources/activity-plan.table.filters.name'))
                            ->icon('heroicon-o-briefcase'),
                        TextConstraint::make('plugin')
                            ->label(__('sales::filament/clusters/configurations/resources/activity-plan.table.filters.plugin'))
                            ->icon('heroicon-o-briefcase'),
                        RelationshipConstraint::make('activityTypes')
                            ->label(__('sales::filament/clusters/configurations/resources/activity-plan.table.filters.activity-types'))
                            ->icon('heroicon-o-briefcase')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->label(__('sales::filament/clusters/configurations/resources/activity-plan.table.filters.activity-types'))
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        RelationshipConstraint::make('company')
                            ->label(__('sales::filament/clusters/configurations/resources/activity-plan.table.filters.company'))
                            ->icon('heroicon-o-building-office-2')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->label(__('sales::filament/clusters/configurations/resources/activity-plan.table.filters.company'))
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        RelationshipConstraint::make('department')
                            ->label(__('sales::filament/clusters/configurations/resources/activity-plan.table.filters.department'))
                            ->icon('heroicon-o-building-office-2')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->label(__('sales::filament/clusters/configurations/resources/activity-plan.table.filters.department'))
                                    ->multiple()
                                    ->preload(),
                            ),
                        DateConstraint::make('created_at')
                            ->label(__('sales::filament/clusters/configurations/resources/activity-plan.table.filters.created-at')),
                        DateConstraint::make('updated_at')
                            ->label(__('sales::filament/clusters/configurations/resources/activity-plan.table.filters.updated-at')),
                    ]),
            ])
            ->groups([
                Group::make('name')
                    ->label(__('sales::filament/clusters/configurations/resources/activity-plan.table.groups.name'))
                    ->collapsible(),
                Group::make('createdBy.name')
                    ->label(__('sales::filament/clusters/configurations/resources/activity-plan.table.groups.created-by'))
                    ->collapsible(),
                Group::make('is_active')
                    ->label(__('sales::filament/clusters/configurations/resources/activity-plan.table.groups.status'))
                    ->collapsible(),
                Group::make('created_at')
                    ->label(__('sales::filament/clusters/configurations/resources/activity-plan.table.groups.created-at'))
                    ->collapsible(),
                Group::make('updated_at')
                    ->label(__('sales::filament/clusters/configurations/resources/activity-plan.table.groups.updated-at'))
                    ->date()
                    ->collapsible(),
            ])
            ->recordActions([
                ViewAction::make()
                    ->hidden(fn ($record) => $record->trashed()),
                EditAction::make()
                    ->hidden(fn ($record) => $record->trashed()),
                RestoreAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('sales::filament/clusters/configurations/resources/activity-plan.table.actions.restore.notification.title'))
                            ->body(__('sales::filament/clusters/configurations/resources/activity-plan.table.actions.restore.notification.body')),
                    ),
                DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('sales::filament/clusters/configurations/resources/activity-plan.table.actions.delete.notification.title'))
                            ->body(__('sales::filament/clusters/configurations/resources/activity-plan.table.actions.delete.notification.body')),
                    ),
                ForceDeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('sales::filament/clusters/configurations/resources/activity-plan.table.actions.force-delete.notification.title'))
                            ->body(__('sales::filament/clusters/configurations/resources/activity-plan.table.actions.force-delete.notification.body')),
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    RestoreBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('sales::filament/clusters/configurations/resources/activity-plan.table.bulk-actions.restore.notification.title'))
                                ->body(__('sales::filament/clusters/configurations/resources/activity-plan.table.bulk-actions.restore.notification.body')),
                        ),
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('sales::filament/clusters/configurations/resources/activity-plan.table.bulk-actions.delete.notification.title'))
                                ->body(__('sales::filament/clusters/configurations/resources/activity-plan.table.bulk-actions.delete.notification.body')),
                        ),
                    ForceDeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('sales::filament/clusters/configurations/resources/activity-plan.table.bulk-actions.force-delete.notification.title'))
                                ->body(__('sales::filament/clusters/configurations/resources/activity-plan.table.bulk-actions.force-delete.notification.body')),
                        ),
                ]),
            ])
            ->emptyStateActions([
                CreateAction::make()
                    ->icon('heroicon-o-plus-circle')
                    ->mutateDataUsing(function (array $data): array {
                        $user = Auth::user();

                        $data['plugin'] = 'sales';

                        $data['creator_id'] = $user->id;

                        $data['company_id'] ??= $user->defaultCompany?->id;

                        return $data;
                    })
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('sales::filament/clusters/configurations/resources/activity-plan.table.empty-state.create.notification.title'))
                            ->body(__('sales::filament/clusters/configurations/resources/activity-plan.table.empty-state.create.notification.body')),
                    ),
            ])
            ->modifyQueryUsing(function ($query) {
                $query->where('plugin', 'sales');
            });
    }
}
