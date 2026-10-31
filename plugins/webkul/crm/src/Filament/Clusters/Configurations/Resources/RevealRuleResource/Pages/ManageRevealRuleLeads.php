<?php

namespace Webkul\Crm\Filament\Clusters\Configurations\Resources\RevealRuleResource\Pages;

use BackedEnum;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Crm\Enums\LeadType;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\RevealRuleResource;
use Webkul\Crm\Filament\Concerns\HasCRMClusterNavigation;
use Webkul\Crm\Filament\Support\LeadResourceResolver;
use Webkul\Crm\Models\Lead;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class ManageRevealRuleLeads extends ManageRelatedRecords
{
    use HasCRMClusterNavigation, HasRecordNavigationTabs {
        HasCRMClusterNavigation::getSubNavigation insteadof HasRecordNavigationTabs;
        HasCRMClusterNavigation::getSubNavigationPosition insteadof HasRecordNavigationTabs;
    }

    protected static string $resource = RevealRuleResource::class;

    protected static string $relationship = 'leads';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-funnel';

    public static function getNavigationLabel(): string
    {
        return __('crm::filament/clusters/configurations/resources/reveal-rule.pages.leads.navigation');
    }

    public function getTitle(): string|Htmlable
    {
        return __('crm::filament/clusters/configurations/resources/reveal-rule.pages.leads.title', ['rule' => $this->getRecord()->name]);
    }

    public function getBreadcrumb(): string
    {
        return __('crm::filament/clusters/configurations/resources/reveal-rule.pages.leads.navigation');
    }

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->withTrashed()->with(['stage', 'user', 'country']))
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->label(__('crm::filament/clusters/configurations/resources/reveal-rule.pages.leads.columns.name'))
                    ->weight('medium')
                    ->searchable(),
                TextColumn::make('type')
                    ->label(__('crm::filament/clusters/configurations/resources/reveal-rule.table.type'))
                    ->badge(),
                TextColumn::make('country.name')
                    ->label(__('crm::filament/clusters/configurations/resources/reveal-rule.pages.leads.columns.country'))
                    ->placeholder('—'),
                TextColumn::make('stage.name')
                    ->label(__('crm::filament/clusters/configurations/resources/reveal-rule.pages.leads.columns.stage'))
                    ->placeholder('—'),
                TextColumn::make('user.name')
                    ->label(__('crm::filament/clusters/configurations/resources/reveal-rule.form.salesperson'))
                    ->placeholder('—'),
                TextColumn::make('created_at')
                    ->label(__('crm::filament/clusters/configurations/resources/reveal-rule.pages.leads.columns.created-at'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('type')
                    ->label(__('crm::filament/clusters/configurations/resources/reveal-rule.table.type'))
                    ->options(LeadType::class),
            ])
            ->recordUrl(fn (Lead $record): ?string => $record->trashed() ? null : LeadResourceResolver::viewUrl($record))
            ->headerActions([])
            ->recordActions([])
            ->toolbarActions([]);
    }
}
