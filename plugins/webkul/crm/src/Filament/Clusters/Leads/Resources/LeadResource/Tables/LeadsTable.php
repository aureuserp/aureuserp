<?php

namespace Webkul\Crm\Filament\Clusters\Leads\Resources\LeadResource\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportBulkAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Chatter\Filament\Actions\ActivityTableAction;
use Webkul\Crm\Filament\Actions\MarkLeadsAsLostBulkAction;
use Webkul\Crm\Filament\Actions\MergeLeadsBulkAction;
use Webkul\Crm\Filament\Actions\SendLeadEmailAction;
use Webkul\Crm\Filament\Actions\SendLeadsEmailBulkAction;
use Webkul\Crm\Filament\Actions\SendLeadSMSAction;
use Webkul\Crm\Filament\Actions\SendLeadsSMSBulkAction;
use Webkul\Crm\Filament\Clusters\Leads\Resources\LeadResource;
use Webkul\Crm\Filament\Clusters\Leads\Resources\LeadResource\Actions\ConvertToOpportunityAction;
use Webkul\Crm\Filament\Clusters\Leads\Resources\LeadResource\Actions\MassConvertLeadsBulkAction;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource\Actions\MarkAsLostAction;
use Webkul\Crm\Filament\Exports\LeadExporter;
use Webkul\Crm\Filament\Support\LeadTableFilters;
use Webkul\Crm\Models\Lead;

class LeadsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns(static::listColumns())
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->with(['user', 'team', 'country', 'tags']))
            ->defaultSort('created_at', 'desc')
            ->groups([
                Group::make('user.name')->label(__('crm::filament/clusters/leads/resources/lead.table.columns.salesperson'))->collapsible(),
                Group::make('team.name')->label(__('crm::filament/clusters/leads/resources/lead.table.columns.team'))->collapsible(),
                Group::make('source.name')->label(__('crm::filament/clusters/leads/resources/lead.table.columns.source'))->collapsible(),
                Group::make('priority')->label(__('crm::filament/clusters/leads/resources/lead.table.columns.priority'))->collapsible(),
            ])
            ->filtersFormColumns(2)
            ->filters([
                LeadTableFilters::leads(),
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
                    SendLeadEmailAction::make(),
                    SendLeadSMSAction::make(),
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
                    BulkActionGroup::make([
                        ExportBulkAction::make()
                            ->label(__('crm::filament/transfers.actions.export.label'))
                            ->icon('heroicon-o-arrow-up-tray')
                            ->exporter(LeadExporter::class),
                        RestoreBulkAction::make(),
                        DeleteBulkAction::make(),
                        ForceDeleteBulkAction::make(),
                    ])->dropdown(false),
                    BulkActionGroup::make([
                        MarkLeadsAsLostBulkAction::make(),
                        MassConvertLeadsBulkAction::make(),
                        MergeLeadsBulkAction::make(),
                        SendLeadsEmailBulkAction::make(),
                        SendLeadsSMSBulkAction::make(),
                    ])->dropdown(false),
                ]),
            ]);
    }

    public static function listColumns(): array
    {
        return [
            TextColumn::make('name')
                ->label(__('crm::filament/clusters/leads/resources/lead.table.columns.name'))
                ->weight('medium')
                ->searchable()
                ->sortable(),
            TextColumn::make('contact_name')
                ->label(__('crm::filament/clusters/leads/resources/lead.table.columns.contact-name'))
                ->searchable()
                ->placeholder('—')
                ->toggleable(isToggledHiddenByDefault: true),
            TextColumn::make('company_name')
                ->label(__('crm::filament/clusters/leads/resources/lead.table.columns.company-name'))
                ->searchable()
                ->placeholder('—')
                ->toggleable(isToggledHiddenByDefault: true),
            TextColumn::make('email')
                ->label(__('crm::filament/clusters/leads/resources/lead.table.columns.email'))
                ->searchable()
                ->placeholder('—')
                ->toggleable(),
            TextColumn::make('phone')
                ->label(__('crm::filament/clusters/leads/resources/lead.table.columns.phone'))
                ->placeholder('—')
                ->toggleable(isToggledHiddenByDefault: true),
            TextColumn::make('city')
                ->label(__('crm::filament/clusters/leads/resources/lead.table.columns.city'))
                ->placeholder('—')
                ->toggleable(),
            TextColumn::make('country.name')
                ->label(__('crm::filament/clusters/leads/resources/lead.table.columns.country'))
                ->placeholder('—')
                ->toggleable(),
            TextColumn::make('user.name')
                ->label(__('crm::filament/clusters/leads/resources/lead.table.columns.salesperson'))
                ->placeholder('—')
                ->sortable()
                ->toggleable(),
            TextColumn::make('team.name')
                ->label(__('crm::filament/clusters/leads/resources/lead.table.columns.team'))
                ->placeholder('—')
                ->toggleable(),
            TextColumn::make('priority')
                ->label(__('crm::filament/clusters/leads/resources/lead.table.columns.priority'))
                ->badge()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
            TextColumn::make('tags.name')
                ->label(__('crm::filament/clusters/leads/resources/lead.table.columns.tags'))
                ->badge()
                ->toggleable(isToggledHiddenByDefault: true),
            TextColumn::make('source.name')
                ->label(__('crm::filament/clusters/leads/resources/lead.table.columns.source'))
                ->placeholder('—')
                ->toggleable(isToggledHiddenByDefault: true),
            TextColumn::make('medium.name')
                ->label(__('crm::filament/clusters/leads/resources/lead.table.columns.medium'))
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
                ->toggleable(isToggledHiddenByDefault: true),
        ];
    }
}
