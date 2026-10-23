<?php

namespace Webkul\Crm\Filament\Clusters\Leads\Resources\LeadResource\Tables;

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
use Illuminate\Database\Eloquent\Builder;
use Webkul\Chatter\Filament\Actions\ActivityTableAction;
use Webkul\Crm\Enums\OpportunityPriority;
use Webkul\Crm\Filament\Actions\MergeLeadsBulkAction;
use Webkul\Crm\Filament\Clusters\Leads\Resources\LeadResource;
use Webkul\Crm\Filament\Clusters\Leads\Resources\LeadResource\Actions\ConvertToOpportunityAction;
use Webkul\Crm\Filament\Clusters\Leads\Resources\LeadResource\Actions\MassConvertLeadsBulkAction;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource\Actions\MarkAsLostAction;
use Webkul\Crm\Models\Lead;

class LeadsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('crm::filament/clusters/leads/resources/lead.table.columns.name'))
                    ->weight('medium')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('contact_name')
                    ->label(__('crm::filament/clusters/leads/resources/lead.table.columns.contact-name'))
                    ->searchable()
                    ->placeholder('—'),
                TextColumn::make('company_name')
                    ->label(__('crm::filament/clusters/leads/resources/lead.table.columns.company-name'))
                    ->searchable()
                    ->placeholder('—'),
                TextColumn::make('email')
                    ->label(__('crm::filament/clusters/leads/resources/lead.table.columns.email'))
                    ->searchable()
                    ->placeholder('—'),
                TextColumn::make('phone')
                    ->label(__('crm::filament/clusters/leads/resources/lead.table.columns.phone'))
                    ->placeholder('—')
                    ->toggleable(),
                TextColumn::make('city')
                    ->label(__('crm::filament/clusters/leads/resources/lead.table.columns.city'))
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('country.name')
                    ->label(__('crm::filament/clusters/leads/resources/lead.table.columns.country'))
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('user.name')
                    ->label(__('crm::filament/clusters/leads/resources/lead.table.columns.salesperson'))
                    ->placeholder('—')
                    ->sortable(),
                TextColumn::make('team.name')
                    ->label(__('crm::filament/clusters/leads/resources/lead.table.columns.team'))
                    ->placeholder('—')
                    ->toggleable(),
                TextColumn::make('priority')
                    ->label(__('crm::filament/clusters/leads/resources/lead.table.columns.priority'))
                    ->badge()
                    ->sortable(),
                TextColumn::make('tags.name')
                    ->label(__('crm::filament/clusters/leads/resources/lead.table.columns.tags'))
                    ->badge()
                    ->toggleable(),
                TextColumn::make('source.name')
                    ->label(__('crm::filament/clusters/leads/resources/lead.table.columns.source'))
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('lostReason.name')
                    ->label(__('crm::filament/clusters/leads/resources/lead.table.columns.lost-reason'))
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label(__('crm::filament/clusters/leads/resources/lead.table.columns.created-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->groups([
                Group::make('user.name')->label(__('crm::filament/clusters/leads/resources/lead.table.columns.salesperson'))->collapsible(),
                Group::make('team.name')->label(__('crm::filament/clusters/leads/resources/lead.table.columns.team'))->collapsible(),
                Group::make('source.name')->label(__('crm::filament/clusters/leads/resources/lead.table.columns.source'))->collapsible(),
                Group::make('priority')->label(__('crm::filament/clusters/leads/resources/lead.table.columns.priority'))->collapsible(),
            ])
            ->filters([
                SelectFilter::make('user_id')
                    ->label(__('crm::filament/clusters/leads/resources/lead.table.columns.salesperson'))
                    ->relationship('user', 'name')
                    ->multiple()
                    ->preload(),
                SelectFilter::make('team_id')
                    ->label(__('crm::filament/clusters/leads/resources/lead.table.columns.team'))
                    ->relationship('team', 'name', fn (Builder $query): Builder => $query->where(owned_by_company(current_company_id())))
                    ->multiple()
                    ->preload(),
                SelectFilter::make('tags')
                    ->label(__('crm::filament/clusters/leads/resources/lead.table.columns.tags'))
                    ->relationship('tags', 'name')
                    ->multiple()
                    ->preload(),
                SelectFilter::make('priority')
                    ->label(__('crm::filament/clusters/leads/resources/lead.table.columns.priority'))
                    ->options(OpportunityPriority::class),
                SelectFilter::make('utm_source_id')
                    ->label(__('crm::filament/clusters/leads/resources/lead.table.columns.source'))
                    ->relationship('source', 'name')
                    ->multiple()
                    ->preload(),
            ])
            ->recordActions([
                ActivityTableAction::make()
                    ->tooltip(__('crm::filament/clusters/leads/resources/lead.actions.schedule-activity.label'))
                    ->visible(fn (Lead $record): bool => ! $record->trashed() && LeadResource::canEdit($record)),
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make()
                        ->hidden(fn (Lead $record): bool => $record->trashed()),
                    ConvertToOpportunityAction::make(),
                    MarkAsLostAction::make(),
                    RestoreAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('crm::filament/clusters/leads/resources/lead.actions.restore.notification.title'))
                                ->body(__('crm::filament/clusters/leads/resources/lead.actions.restore.notification.body')),
                        ),
                    ForceDeleteAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('crm::filament/clusters/leads/resources/lead.actions.force-delete.notification.title'))
                                ->body(__('crm::filament/clusters/leads/resources/lead.actions.force-delete.notification.body')),
                        ),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    MassConvertLeadsBulkAction::make(),
                    MergeLeadsBulkAction::make(),
                    RestoreBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                ]),
            ]);
    }
}
