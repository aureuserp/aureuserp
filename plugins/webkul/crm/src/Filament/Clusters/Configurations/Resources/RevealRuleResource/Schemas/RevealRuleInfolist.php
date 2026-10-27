<?php

namespace Webkul\Crm\Filament\Clusters\Configurations\Resources\RevealRuleResource\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Webkul\Crm\Enums\LeadType;
use Webkul\Crm\Enums\RevealRuleContactFilter;
use Webkul\Crm\Enums\RevealRuleLeadFor;
use Webkul\Crm\Models\RevealRule;

class RevealRuleInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make([
                    Section::make()
                        ->schema([
                            TextEntry::make('name')
                                ->label(__('crm::filament/clusters/configurations/resources/reveal-rule.form.name'))
                                ->weight('bold')
                                ->size('lg'),
                        ]),
                    Section::make(__('crm::filament/clusters/configurations/resources/reveal-rule.form.sections.traffic'))
                        ->schema([
                            TextEntry::make('countries.name')
                                ->label(__('crm::filament/clusters/configurations/resources/reveal-rule.form.countries'))
                                ->badge()
                                ->placeholder(__('crm::filament/clusters/configurations/resources/reveal-rule.table.all-countries')),
                            TextEntry::make('states.name')
                                ->label(__('crm::filament/clusters/configurations/resources/reveal-rule.form.states'))
                                ->badge()
                                ->visible(fn (RevealRule $record): bool => $record->states->isNotEmpty()),
                            TextEntry::make('regex_url')
                                ->label(__('crm::filament/clusters/configurations/resources/reveal-rule.form.url'))
                                ->fontFamily('mono')
                                ->placeholder(__('crm::filament/clusters/configurations/resources/reveal-rule.table.whole-website')),
                        ]),
                    Section::make(__('crm::filament/clusters/configurations/resources/reveal-rule.form.sections.contact-filter'))
                        ->columns(2)
                        ->visible(fn (RevealRule $record): bool => $record->lead_for === RevealRuleLeadFor::PEOPLE)
                        ->schema([
                            TextEntry::make('extra_contacts')
                                ->label(__('crm::filament/clusters/configurations/resources/reveal-rule.form.extra-contacts')),
                            TextEntry::make('contact_filter_type')
                                ->label(__('crm::filament/clusters/configurations/resources/reveal-rule.form.contact-filter'))
                                ->badge(),
                            TextEntry::make('preferredRole.name')
                                ->label(__('crm::filament/clusters/configurations/resources/reveal-rule.form.preferred-role'))
                                ->placeholder('—')
                                ->visible(fn (RevealRule $record): bool => $record->contact_filter_type !== RevealRuleContactFilter::SENIORITY),
                            TextEntry::make('otherRoles.name')
                                ->label(__('crm::filament/clusters/configurations/resources/reveal-rule.form.other-roles'))
                                ->badge()
                                ->placeholder('—')
                                ->visible(fn (RevealRule $record): bool => $record->contact_filter_type !== RevealRuleContactFilter::SENIORITY),
                            TextEntry::make('seniority.name')
                                ->label(__('crm::filament/clusters/configurations/resources/reveal-rule.form.seniority'))
                                ->placeholder('—')
                                ->visible(fn (RevealRule $record): bool => $record->contact_filter_type === RevealRuleContactFilter::SENIORITY),
                        ]),
                    Section::make(fn (RevealRule $record): string => $record->lead_type === LeadType::LEAD
                        ? __('crm::filament/clusters/configurations/resources/reveal-rule.form.sections.lead-data')
                        : __('crm::filament/clusters/configurations/resources/reveal-rule.form.sections.opportunity-data'))
                        ->columns(2)
                        ->schema([
                            TextEntry::make('tags.name')
                                ->label(__('crm::filament/clusters/configurations/resources/reveal-rule.form.tags'))
                                ->badge()
                                ->placeholder('—'),
                            TextEntry::make('suffix')
                                ->label(__('crm::filament/clusters/configurations/resources/reveal-rule.form.suffix'))
                                ->placeholder('—'),
                            TextEntry::make('priority')
                                ->label(__('crm::filament/clusters/configurations/resources/reveal-rule.form.priority'))
                                ->badge(),
                            TextEntry::make('team.name')
                                ->label(__('crm::filament/clusters/configurations/resources/reveal-rule.form.team'))
                                ->placeholder('—'),
                            TextEntry::make('company.name')
                                ->label(__('crm::filament/clusters/configurations/resources/reveal-rule.form.company'))
                                ->placeholder('—'),
                            TextEntry::make('user.name')
                                ->label(__('crm::filament/clusters/configurations/resources/reveal-rule.form.salesperson'))
                                ->placeholder('—'),
                        ]),
                ])
                    ->columnSpan(['lg' => 2]),
                Group::make([
                    Section::make(__('crm::filament/clusters/configurations/resources/reveal-rule.form.sections.settings'))
                        ->schema([
                            IconEntry::make('is_active')
                                ->label(__('crm::filament/clusters/configurations/resources/reveal-rule.form.active'))
                                ->boolean(),
                            TextEntry::make('lead_for')
                                ->label(__('crm::filament/clusters/configurations/resources/reveal-rule.form.lead-for'))
                                ->badge(),
                            TextEntry::make('lead_type')
                                ->label(__('crm::filament/clusters/configurations/resources/reveal-rule.form.type'))
                                ->badge(),
                        ]),
                    Section::make(fn (RevealRule $record): string => $record->lead_type === LeadType::LEAD
                        ? __('crm::filament/clusters/configurations/resources/reveal-rule.form.sections.lead-generation')
                        : __('crm::filament/clusters/configurations/resources/reveal-rule.form.sections.generation'))
                        ->schema([
                            TextEntry::make('industries.name')
                                ->label(__('crm::filament/clusters/configurations/resources/reveal-rule.form.industries'))
                                ->badge()
                                ->placeholder('—'),
                            TextEntry::make('company_size')
                                ->label(__('crm::filament/clusters/configurations/resources/reveal-rule.form.filter-on-size'))
                                ->state(fn (RevealRule $record): string => $record->company_size_min.' – '.$record->company_size_max.' '.__('crm::filament/clusters/configurations/resources/reveal-rule.form.employees'))
                                ->visible(fn (RevealRule $record): bool => (bool) $record->filter_on_size),
                        ]),
                ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }
}
