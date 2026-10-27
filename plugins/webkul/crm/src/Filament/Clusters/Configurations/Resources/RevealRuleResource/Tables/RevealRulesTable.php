<?php

namespace Webkul\Crm\Filament\Clusters\Configurations\Resources\RevealRuleResource\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Crm\Enums\LeadType;
use Webkul\Crm\Filament\Actions\RecordBulkActions;
use Webkul\Crm\Filament\Exports\RevealRuleExporter;

class RevealRulesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query): Builder => $query
                ->with(['countries', 'team', 'user'])
                ->withCount([
                    'leads as leads_count'         => fn (Builder $query): Builder => $query->withoutGlobalScopes()->where('type', LeadType::LEAD),
                    'leads as opportunities_count' => fn (Builder $query): Builder => $query->withoutGlobalScopes()->where('type', LeadType::OPPORTUNITY),
                ]))
            ->columns([
                TextColumn::make('name')
                    ->label(__('crm::filament/clusters/configurations/resources/reveal-rule.table.name'))
                    ->weight('medium')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('regex_url')
                    ->label(__('crm::filament/clusters/configurations/resources/reveal-rule.table.url'))
                    ->placeholder(__('crm::filament/clusters/configurations/resources/reveal-rule.table.whole-website'))
                    ->fontFamily('mono'),
                TextColumn::make('countries.name')
                    ->label(__('crm::filament/clusters/configurations/resources/reveal-rule.table.countries'))
                    ->badge()
                    ->placeholder(__('crm::filament/clusters/configurations/resources/reveal-rule.table.all-countries')),
                TextColumn::make('lead_type')
                    ->label(__('crm::filament/clusters/configurations/resources/reveal-rule.table.type'))
                    ->badge(),
                TextColumn::make('team.name')
                    ->label(__('crm::filament/clusters/configurations/resources/reveal-rule.table.team'))
                    ->placeholder('—'),
                TextColumn::make('user.name')
                    ->label(__('crm::filament/clusters/configurations/resources/reveal-rule.table.salesperson'))
                    ->placeholder('—'),
                TextColumn::make('leads_count')
                    ->label(__('crm::filament/clusters/configurations/resources/reveal-rule.table.leads'))
                    ->numeric(),
                TextColumn::make('opportunities_count')
                    ->label(__('crm::filament/clusters/configurations/resources/reveal-rule.table.opportunities'))
                    ->numeric(),
                ToggleColumn::make('is_active')
                    ->label(__('crm::filament/clusters/configurations/resources/reveal-rule.table.active')),
            ])
            ->defaultSort('sort')
            ->reorderable('sort')
            ->filters([
                TernaryFilter::make('is_active')
                    ->label(__('crm::filament/clusters/configurations/resources/reveal-rule.table.active')),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                RecordBulkActions::make(exporter: RevealRuleExporter::class, copyRelations: ['countries', 'states', 'tags', 'industries', 'otherRoles']),
            ]);
    }
}
