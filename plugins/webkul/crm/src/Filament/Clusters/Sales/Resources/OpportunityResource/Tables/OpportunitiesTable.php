<?php

namespace Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Webkul\Chatter\Filament\Actions\ActivityTableAction;
use Webkul\Crm\Enums\OpportunityPriority;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource\Actions\MarkAsLostAction;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource\Actions\MarkAsWonAction;
use Webkul\Crm\Models\Lead;

class OpportunitiesTable
{
    public static function configure(Table $table, bool $withinTeam = false): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('crm::filament/clusters/sales/resources/opportunity.table.columns.name'))
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),
                TextColumn::make('partner.name')
                    ->label(__('crm::filament/clusters/sales/resources/opportunity.table.columns.customer'))
                    ->searchable()
                    ->sortable()
                    ->placeholder('—'),
                TextColumn::make('stage.name')
                    ->label(__('crm::filament/clusters/sales/resources/opportunity.table.columns.stage'))
                    ->badge()
                    ->color(fn (Lead $record): string => $record->trashed() ? 'danger' : ($record->isWon() ? 'success' : 'primary'))
                    ->sortable(),
                TextColumn::make('expected_revenue')
                    ->label(__('crm::filament/clusters/sales/resources/opportunity.table.columns.expected-revenue'))
                    ->money(fn (Lead $record): ?string => $record->company?->currency?->code)
                    ->sortable(),
                TextColumn::make('probability')
                    ->label(__('crm::filament/clusters/sales/resources/opportunity.table.columns.probability'))
                    ->numeric(decimalPlaces: 0)
                    ->suffix('%')
                    ->sortable(),
                TextColumn::make('priority')
                    ->label(__('crm::filament/clusters/sales/resources/opportunity.table.columns.priority'))
                    ->badge()
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label(__('crm::filament/clusters/sales/resources/opportunity.table.columns.salesperson'))
                    ->sortable()
                    ->placeholder('—'),
                TextColumn::make('team.name')
                    ->label(__('crm::filament/clusters/sales/resources/opportunity.table.columns.team'))
                    ->sortable()
                    ->placeholder('—')
                    ->hidden($withinTeam),
                TextColumn::make('expected_closing_date')
                    ->label(__('crm::filament/clusters/sales/resources/opportunity.table.columns.expected-closing'))
                    ->date()
                    ->sortable()
                    ->placeholder('—')
                    ->toggleable(),
                TextColumn::make('tags.name')
                    ->label(__('crm::filament/clusters/sales/resources/opportunity.table.columns.tags'))
                    ->badge()
                    ->toggleable(),
                TextColumn::make('email')
                    ->label(__('crm::filament/clusters/sales/resources/opportunity.table.columns.email'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('phone')
                    ->label(__('crm::filament/clusters/sales/resources/opportunity.table.columns.phone'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('lostReason.name')
                    ->label(__('crm::filament/clusters/sales/resources/opportunity.table.columns.lost-reason'))
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label(__('crm::filament/clusters/sales/resources/opportunity.table.columns.created-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->groups(array_values(array_filter([
                Group::make('stage.name')
                    ->label(__('crm::filament/clusters/sales/resources/opportunity.table.groups.stage'))
                    ->collapsible(),
                $withinTeam ? null : Group::make('team.name')
                    ->label(__('crm::filament/clusters/sales/resources/opportunity.table.groups.team'))
                    ->collapsible(),
                Group::make('user.name')
                    ->label(__('crm::filament/clusters/sales/resources/opportunity.table.groups.salesperson'))
                    ->collapsible(),
                Group::make('priority')
                    ->label(__('crm::filament/clusters/sales/resources/opportunity.table.groups.priority'))
                    ->collapsible(),
                Group::make('expected_closing_date')
                    ->label(__('crm::filament/clusters/sales/resources/opportunity.table.groups.expected-closing'))
                    ->date()
                    ->collapsible(),
            ])))
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('stage_id')
                    ->label(__('crm::filament/clusters/sales/resources/opportunity.table.filters.stage'))
                    ->relationship('stage', 'name')
                    ->multiple()
                    ->preload(),
                SelectFilter::make('team_id')
                    ->label(__('crm::filament/clusters/sales/resources/opportunity.table.filters.team'))
                    ->relationship('team', 'name')
                    ->multiple()
                    ->preload()
                    ->hidden($withinTeam),
                SelectFilter::make('user_id')
                    ->label(__('crm::filament/clusters/sales/resources/opportunity.table.filters.salesperson'))
                    ->relationship('user', 'name')
                    ->multiple()
                    ->preload(),
                SelectFilter::make('tags')
                    ->label(__('crm::filament/clusters/sales/resources/opportunity.table.filters.tags'))
                    ->relationship('tags', 'name')
                    ->multiple()
                    ->preload(),
                SelectFilter::make('priority')
                    ->label(__('crm::filament/clusters/sales/resources/opportunity.table.filters.priority'))
                    ->options(OpportunityPriority::class),
            ])
            ->recordActions([
                ActivityTableAction::make()
                    ->tooltip(__('crm::filament/clusters/sales/resources/opportunity.table.actions.schedule-activity.label'))
                    ->visible(fn (Lead $record): bool => ! $record->trashed() && OpportunityResource::canEdit($record)),
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make()
                        ->hidden(fn (Lead $record): bool => $record->trashed()),
                    MarkAsWonAction::make(),
                    MarkAsLostAction::make(),
                    RestoreAction::make()
                        ->label(__('crm::filament/clusters/sales/resources/opportunity.table.actions.restore.label'))
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('crm::filament/clusters/sales/resources/opportunity.table.actions.restore.notification.title'))
                                ->body(__('crm::filament/clusters/sales/resources/opportunity.table.actions.restore.notification.body')),
                        ),
                    ForceDeleteAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('crm::filament/clusters/sales/resources/opportunity.table.actions.force-delete.notification.title'))
                                ->body(__('crm::filament/clusters/sales/resources/opportunity.table.actions.force-delete.notification.body')),
                        ),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    RestoreBulkAction::make()
                        ->label(__('crm::filament/clusters/sales/resources/opportunity.table.bulk-actions.restore.label'))
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('crm::filament/clusters/sales/resources/opportunity.table.bulk-actions.restore.notification.title'))
                                ->body(__('crm::filament/clusters/sales/resources/opportunity.table.bulk-actions.restore.notification.body')),
                        ),
                    ForceDeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('crm::filament/clusters/sales/resources/opportunity.table.bulk-actions.force-delete.notification.title'))
                                ->body(__('crm::filament/clusters/sales/resources/opportunity.table.bulk-actions.force-delete.notification.body')),
                        ),
                ]),
            ]);
    }
}
