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
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
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
use Webkul\Crm\Filament\Support\LeadTableFilters;
use Webkul\Crm\Models\Lead;

class LeadsTable
{
    public static function configure(Table $table, bool $grid = false): Table
    {
        $table = $grid
            ? $table->columns(static::gridColumns())->contentGrid(['md' => 2, 'xl' => 3, '2xl' => 4])
            : $table->columns(static::listColumns());

        return $table
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

    public static function listColumns(): array
    {
        $label = fn (string $key): string => __('crm::filament/clusters/leads/resources/lead.table.columns.'.$key);

        return [
            TextColumn::make('name')
                ->label($label('name'))
                ->weight('medium')
                ->searchable()
                ->sortable(),
            TextColumn::make('contact_name')
                ->label($label('contact-name'))
                ->searchable()
                ->placeholder('—')
                ->toggleable(isToggledHiddenByDefault: true),
            TextColumn::make('company_name')
                ->label($label('company-name'))
                ->searchable()
                ->placeholder('—')
                ->toggleable(isToggledHiddenByDefault: true),
            TextColumn::make('email')
                ->label($label('email'))
                ->searchable()
                ->placeholder('—')
                ->toggleable(),
            TextColumn::make('phone')
                ->label($label('phone'))
                ->placeholder('—')
                ->toggleable(isToggledHiddenByDefault: true),
            TextColumn::make('city')
                ->label($label('city'))
                ->placeholder('—')
                ->toggleable(),
            TextColumn::make('country.name')
                ->label($label('country'))
                ->placeholder('—')
                ->toggleable(),
            TextColumn::make('user.name')
                ->label($label('salesperson'))
                ->placeholder('—')
                ->sortable()
                ->toggleable(),
            TextColumn::make('team.name')
                ->label($label('team'))
                ->placeholder('—')
                ->toggleable(),
            TextColumn::make('priority')
                ->label($label('priority'))
                ->badge()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
            TextColumn::make('tags.name')
                ->label($label('tags'))
                ->badge()
                ->toggleable(isToggledHiddenByDefault: true),
            TextColumn::make('source.name')
                ->label($label('source'))
                ->placeholder('—')
                ->toggleable(isToggledHiddenByDefault: true),
            TextColumn::make('lostReason.name')
                ->label($label('lost-reason'))
                ->placeholder('—')
                ->toggleable(isToggledHiddenByDefault: true),
            TextColumn::make('created_at')
                ->label($label('created-at'))
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
        ];
    }

    public static function gridColumns(): array
    {
        $label = fn (string $key): string => __('crm::filament/clusters/leads/resources/lead.table.columns.'.$key);

        return [
            Stack::make([
                TextColumn::make('name')
                    ->label($label('name'))
                    ->weight(FontWeight::SemiBold)
                    ->searchable(['name', 'contact_name', 'company_name', 'email'])
                    ->sortable(),
                TextColumn::make('contact_name')
                    ->label($label('contact-name'))
                    ->state(fn (Lead $record): ?string => $record->contact_name ?: $record->company_name)
                    ->color('gray'),
                TextColumn::make('email')
                    ->label($label('email'))
                    ->icon('heroicon-o-envelope')
                    ->color('gray')
                    ->limit(40),
                TextColumn::make('city')
                    ->label($label('city'))
                    ->state(fn (Lead $record): ?string => collect([$record->city, $record->country?->name])->filter()->implode(', ') ?: null)
                    ->icon('heroicon-o-map-pin')
                    ->color('gray'),
                TextColumn::make('tags.name')
                    ->label($label('tags'))
                    ->badge()
                    ->color('info'),
                Split::make([
                    TextColumn::make('priority')
                        ->label($label('priority'))
                        ->formatStateUsing(fn (?OpportunityPriority $state): string => str_repeat('★', $state?->value ?? 0).str_repeat('☆', 3 - ($state?->value ?? 0)))
                        ->tooltip(fn (Lead $record): ?string => $record->priority?->getLabel())
                        ->color('warning')
                        ->size(TextSize::Large)
                        ->sortable(),
                    TextColumn::make('user.name')
                        ->label($label('salesperson'))
                        ->icon('heroicon-o-user-circle')
                        ->color('gray')
                        ->alignEnd()
                        ->sortable(),
                ]),
            ])->space(2),
        ];
    }
}
