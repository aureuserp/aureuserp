<?php

namespace Webkul\Crm\Filament\Clusters\Sales\Resources\ActivityResource\Tables;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Crm\Enums\ActivityDueStatus;
use Webkul\Crm\Filament\Clusters\Sales\Resources\ActivityResource\Actions\MarkActivityAsDoneAction;
use Webkul\Crm\Filament\Clusters\Sales\Resources\ActivityResource\Actions\RescheduleActivityAction;
use Webkul\Crm\Filament\Support\LeadResourceResolver;
use Webkul\Crm\Models\Activity;

class ActivitiesTable
{
    public static function configure(Table $table, bool $withinRecord = false): Table
    {
        return $table
            ->columns([
                TextColumn::make('date_deadline')
                    ->label(__('crm::filament/clusters/sales/resources/activity.table.columns.due-date'))
                    ->date()
                    ->badge()
                    ->color(fn (Activity $record): string => $record->getDueStatus()->getColor())
                    ->icon(fn (Activity $record): string => $record->getDueStatus()->getIcon())
                    ->description(fn (Activity $record): string => $record->getDueStatus()->getLabel())
                    ->sortable(),
                TextColumn::make('lead.name')
                    ->hidden($withinRecord)
                    ->label(__('crm::filament/clusters/sales/resources/activity.table.columns.opportunity'))
                    ->weight('medium')
                    ->searchable()
                    ->url(fn (Activity $record): ?string => LeadResourceResolver::viewUrl($record->lead)),
                TextColumn::make('lead.partner.name')
                    ->hidden($withinRecord)
                    ->label(__('crm::filament/clusters/sales/resources/activity.table.columns.customer'))
                    ->placeholder('—')
                    ->toggleable(),
                TextColumn::make('activityType.name')
                    ->label(__('crm::filament/clusters/sales/resources/activity.table.columns.activity-type'))
                    ->badge()
                    ->color('gray')
                    ->placeholder('—'),
                TextColumn::make('summary')
                    ->label(__('crm::filament/clusters/sales/resources/activity.table.columns.summary'))
                    ->limit(50)
                    ->searchable()
                    ->placeholder('—'),
                TextColumn::make('assignedTo.name')
                    ->label(__('crm::filament/clusters/sales/resources/activity.table.columns.assigned-to'))
                    ->placeholder('—'),
                TextColumn::make('lead.stage.name')
                    ->hidden($withinRecord)
                    ->label(__('crm::filament/clusters/sales/resources/activity.table.columns.stage'))
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label(__('crm::filament/clusters/sales/resources/activity.table.columns.created-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('date_deadline')
            ->groups([
                Group::make('activityType.name')
                    ->label(__('crm::filament/clusters/sales/resources/activity.table.groups.activity-type'))
                    ->collapsible(),
                Group::make('assignedTo.name')
                    ->label(__('crm::filament/clusters/sales/resources/activity.table.groups.assigned-to'))
                    ->collapsible(),
                Group::make('date_deadline')
                    ->label(__('crm::filament/clusters/sales/resources/activity.table.groups.due-date'))
                    ->date()
                    ->collapsible(),
            ])
            ->filters([
                SelectFilter::make('due_status')
                    ->label(__('crm::filament/clusters/sales/resources/activity.table.filters.due-status'))
                    ->options(ActivityDueStatus::class)
                    ->query(fn (Builder $query, array $data): Builder => filled($data['value'] ?? null) ? $query->dueStatus($data['value']) : $query),
                SelectFilter::make('activity_type_id')
                    ->label(__('crm::filament/clusters/sales/resources/activity.table.filters.activity-type'))
                    ->relationship('activityType', 'name')
                    ->multiple()
                    ->preload(),
                SelectFilter::make('assigned_to')
                    ->label(__('crm::filament/clusters/sales/resources/activity.table.filters.assigned-to'))
                    ->relationship('assignedTo', 'name')
                    ->multiple()
                    ->preload(),
            ])
            ->recordUrl(fn (Activity $record): ?string => $withinRecord ? null : LeadResourceResolver::viewUrl($record->lead))
            ->recordActions([
                MarkActivityAsDoneAction::make()
                    ->iconButton()
                    ->tooltip(__('crm::filament/clusters/sales/resources/activity.actions.done.label')),
                ActionGroup::make([
                    Action::make('openOpportunity')
                        ->hidden($withinRecord)
                        ->label(__('crm::filament/clusters/sales/resources/activity.actions.open.label'))
                        ->icon('heroicon-o-arrow-top-right-on-square')
                        ->url(fn (Activity $record): ?string => LeadResourceResolver::viewUrl($record->lead)),
                    RescheduleActivityAction::make(),
                    DeleteAction::make()
                        ->label(__('crm::filament/clusters/sales/resources/activity.actions.cancel.label'))
                        ->icon('heroicon-o-x-circle')
                        ->modalHeading(__('crm::filament/clusters/sales/resources/activity.actions.cancel.modal.heading'))
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('crm::filament/clusters/sales/resources/activity.actions.cancel.notification.title'))
                                ->body(__('crm::filament/clusters/sales/resources/activity.actions.cancel.notification.body')),
                        ),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label(__('crm::filament/clusters/sales/resources/activity.actions.cancel.bulk-label'))
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('crm::filament/clusters/sales/resources/activity.actions.cancel.notification.title'))
                                ->body(__('crm::filament/clusters/sales/resources/activity.actions.cancel.notification.body')),
                        ),
                ]),
            ])
            ->emptyStateHeading(__('crm::filament/clusters/sales/resources/activity.table.empty-state.heading'))
            ->emptyStateDescription(__('crm::filament/clusters/sales/resources/activity.table.empty-state.description'))
            ->emptyStateIcon('heroicon-o-clock');
    }
}
