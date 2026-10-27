<?php

namespace Webkul\Crm\Filament\Clusters\Configurations\Resources\RevealRuleResource\Schemas;

use Closure;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Crm\Enums\LeadType;
use Webkul\Crm\Enums\OpportunityPriority;
use Webkul\Crm\Enums\RevealRuleContactFilter;
use Webkul\Crm\Enums\RevealRuleLeadFor;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource\Schemas\OpportunityForm;
use Webkul\Crm\Models\RevealRule;
use Webkul\Crm\Settings\CRMSettings;
use Webkul\Security\Models\User;
use Webkul\Support\Models\State;
use Webkul\Support\Services\CompanyContext;

class RevealRuleForm
{
    public static function configure(Schema $schema): Schema
    {
        $isPeople = fn (Get $get): bool => $get('lead_for') === RevealRuleLeadFor::PEOPLE;
        $isRoleFilter = fn (Get $get): bool => $get('contact_filter_type') !== RevealRuleContactFilter::SENIORITY;

        return $schema
            ->components([
                Group::make([
                    Section::make()
                        ->schema([
                            TextInput::make('name')
                                ->label(__('crm::filament/clusters/configurations/resources/reveal-rule.form.name'))
                                ->placeholder(__('crm::filament/clusters/configurations/resources/reveal-rule.form.name-placeholder'))
                                ->required()
                                ->maxLength(255),
                        ]),
                    Section::make(__('crm::filament/clusters/configurations/resources/reveal-rule.form.sections.traffic'))
                        ->schema([
                            Select::make('countries')
                                ->label(__('crm::filament/clusters/configurations/resources/reveal-rule.form.countries'))
                                ->helperText(__('crm::filament/clusters/configurations/resources/reveal-rule.form.countries-helper'))
                                ->relationship('countries', 'name')
                                ->multiple()
                                ->searchable()
                                ->preload()
                                ->live(),
                            Select::make('states')
                                ->label(__('crm::filament/clusters/configurations/resources/reveal-rule.form.states'))
                                ->helperText(__('crm::filament/clusters/configurations/resources/reveal-rule.form.states-helper'))
                                ->relationship('states', 'name', fn (Builder $query, Get $get): Builder => $query->whereIn('country_id', (array) $get('countries')))
                                ->getOptionLabelFromRecordUsing(fn (State $record): string => $record->name.' ('.$record->country?->name.')')
                                ->multiple()
                                ->searchable()
                                ->visible(fn (Get $get): bool => filled($get('countries'))),
                            TextInput::make('regex_url')
                                ->label(__('crm::filament/clusters/configurations/resources/reveal-rule.form.url'))
                                ->placeholder(__('crm::filament/clusters/configurations/resources/reveal-rule.form.url-placeholder'))
                                ->helperText(__('crm::filament/clusters/configurations/resources/reveal-rule.form.url-helper'))
                                ->maxLength(255)
                                ->rule(fn (): Closure => function (string $attribute, mixed $value, Closure $fail): void {
                                    if (filled($value) && ! RevealRule::isValidPattern((string) $value)) {
                                        $fail(__('crm::filament/clusters/configurations/resources/reveal-rule.form.url-invalid'));
                                    }
                                }),
                        ]),
                    Section::make(__('crm::filament/clusters/configurations/resources/reveal-rule.form.sections.contact-filter'))
                        ->description(__('crm::filament/clusters/configurations/resources/reveal-rule.form.gdpr'))
                        ->columns(2)
                        ->visible($isPeople)
                        ->schema([
                            TextInput::make('extra_contacts')
                                ->label(__('crm::filament/clusters/configurations/resources/reveal-rule.form.extra-contacts'))
                                ->helperText(__('crm::filament/clusters/configurations/resources/reveal-rule.form.extra-contacts-helper'))
                                ->numeric()
                                ->integer()
                                ->minValue(1)
                                ->maxValue(RevealRule::MAX_EXTRA_CONTACTS)
                                ->default(1)
                                ->required(),
                            Radio::make('contact_filter_type')
                                ->label(__('crm::filament/clusters/configurations/resources/reveal-rule.form.contact-filter'))
                                ->options(RevealRuleContactFilter::class)
                                ->default(RevealRuleContactFilter::ROLE)
                                ->inline()
                                ->live()
                                ->required(),
                            Select::make('preferred_role_id')
                                ->label(__('crm::filament/clusters/configurations/resources/reveal-rule.form.preferred-role'))
                                ->relationship('preferredRole', 'name')
                                ->searchable()
                                ->preload()
                                ->live()
                                ->createOptionForm([
                                    TextInput::make('name')->label(__('crm::filament/clusters/configurations/resources/reveal-rule.form.name'))->required()->maxLength(255)->unique('crm_lead_roles', 'name'),
                                ])
                                ->visible($isRoleFilter),
                            Select::make('otherRoles')
                                ->label(__('crm::filament/clusters/configurations/resources/reveal-rule.form.other-roles'))
                                ->relationship('otherRoles', 'name')
                                ->multiple()
                                ->searchable()
                                ->preload()
                                ->createOptionForm([
                                    TextInput::make('name')->label(__('crm::filament/clusters/configurations/resources/reveal-rule.form.name'))->required()->maxLength(255)->unique('crm_lead_roles', 'name'),
                                ])
                                ->visible(fn (Get $get): bool => $isRoleFilter($get) && filled($get('preferred_role_id'))),
                            Select::make('seniority_id')
                                ->label(__('crm::filament/clusters/configurations/resources/reveal-rule.form.seniority'))
                                ->relationship('seniority', 'name')
                                ->searchable()
                                ->preload()
                                ->createOptionForm([
                                    TextInput::make('name')->label(__('crm::filament/clusters/configurations/resources/reveal-rule.form.name'))->required()->maxLength(255)->unique('crm_lead_seniorities', 'name'),
                                ])
                                ->visible(fn (Get $get): bool => ! $isRoleFilter($get)),
                        ]),
                    Section::make(fn (Get $get): string => $get('lead_type') === LeadType::LEAD ? __('crm::filament/clusters/configurations/resources/reveal-rule.form.sections.lead-data') : __('crm::filament/clusters/configurations/resources/reveal-rule.form.sections.opportunity-data'))
                        ->columns(2)
                        ->schema([
                            Select::make('tags')
                                ->label(__('crm::filament/clusters/configurations/resources/reveal-rule.form.tags'))
                                ->relationship('tags', 'name')
                                ->multiple()
                                ->searchable()
                                ->preload(),
                            TextInput::make('suffix')
                                ->label(__('crm::filament/clusters/configurations/resources/reveal-rule.form.suffix'))
                                ->helperText(__('crm::filament/clusters/configurations/resources/reveal-rule.form.suffix-helper'))
                                ->maxLength(255),
                            Select::make('priority')
                                ->label(__('crm::filament/clusters/configurations/resources/reveal-rule.form.priority'))
                                ->options(OpportunityPriority::class)
                                ->default(OpportunityPriority::LOW)
                                ->selectablePlaceholder(false),
                            Select::make('team_id')
                                ->label(__('crm::filament/clusters/configurations/resources/reveal-rule.form.team'))
                                ->relationship('team', 'name', fn (Builder $query): Builder => $query->where(owned_by_company(current_company_id())))
                                ->searchable()
                                ->preload(),
                            Select::make('company_id')
                                ->label(__('crm::filament/clusters/configurations/resources/reveal-rule.form.company'))
                                ->helperText(__('crm::filament/clusters/configurations/resources/reveal-rule.form.company-helper'))
                                ->relationship('company', 'name', fn (Builder $query): Builder => $query->whereKey(app(CompanyContext::class)->activeIds()))
                                ->rule(fn (): Closure => function (string $attribute, mixed $value, Closure $fail): void {
                                    if (filled($value) && ! in_array((int) $value, array_map('intval', app(CompanyContext::class)->activeIds()), true)) {
                                        $fail(__('crm::filament/assignment.team.company-not-active'));
                                    }
                                })
                                ->searchable()
                                ->preload()
                                ->live()
                                ->afterStateUpdated(fn (Set $set): mixed => $set('user_id', null)),
                            Select::make('user_id')
                                ->label(__('crm::filament/clusters/configurations/resources/reveal-rule.form.salesperson'))
                                ->relationship('user', 'name', fn (Builder $query, Get $get): Builder => OpportunityForm::scopeSalespeople($query, $get('company_id') ?? current_company_id()))
                                ->rule(fn (Get $get): Closure => function (string $attribute, mixed $value, Closure $fail) use ($get): void {
                                    if (filled($value) && ! OpportunityForm::scopeSalespeople(User::query(), $get('company_id') ?? current_company_id())->whereKey($value)->exists()) {
                                        $fail(__('validation.exists', ['attribute' => $attribute]));
                                    }
                                })
                                ->searchable()
                                ->preload(),
                        ]),
                ])
                    ->columnSpan(['lg' => 2]),
                Group::make([
                    Section::make(__('crm::filament/clusters/configurations/resources/reveal-rule.form.sections.settings'))
                        ->schema([
                            Toggle::make('is_active')
                                ->label(__('crm::filament/clusters/configurations/resources/reveal-rule.form.active'))
                                ->default(true)
                                ->inline(false),
                            Radio::make('lead_for')
                                ->label(__('crm::filament/clusters/configurations/resources/reveal-rule.form.lead-for'))
                                ->options(RevealRuleLeadFor::class)
                                ->default(RevealRuleLeadFor::COMPANIES)
                                ->required()
                                ->live(),
                            Radio::make('lead_type')
                                ->label(__('crm::filament/clusters/configurations/resources/reveal-rule.form.type'))
                                ->options(LeadType::class)
                                ->default(fn (): LeadType => app(CRMSettings::class)->use_leads ? LeadType::LEAD : LeadType::OPPORTUNITY)
                                ->visible(fn (): bool => (bool) app(CRMSettings::class)->use_leads)
                                ->inline()
                                ->live()
                                ->required(),
                        ]),
                    Section::make(fn (Get $get): string => $get('lead_type') === LeadType::LEAD
                        ? __('crm::filament/clusters/configurations/resources/reveal-rule.form.sections.lead-generation')
                        : __('crm::filament/clusters/configurations/resources/reveal-rule.form.sections.generation'))
                        ->schema([
                            Select::make('industries')
                                ->label(__('crm::filament/clusters/configurations/resources/reveal-rule.form.industries'))
                                ->helperText(__('crm::filament/clusters/configurations/resources/reveal-rule.form.industries-helper'))
                                ->relationship('industries', 'name')
                                ->multiple()
                                ->searchable()
                                ->preload()
                                ->createOptionForm([
                                    TextInput::make('name')->label(__('crm::filament/clusters/configurations/resources/reveal-rule.form.name'))->required()->maxLength(255)->unique('crm_lead_industries', 'name'),
                                ]),
                            Toggle::make('filter_on_size')
                                ->label(__('crm::filament/clusters/configurations/resources/reveal-rule.form.filter-on-size'))
                                ->default(true)
                                ->live(),
                            Group::make()
                                ->visible(fn (Get $get): bool => (bool) $get('filter_on_size'))
                                ->schema([
                                    TextInput::make('company_size_min')
                                        ->label(__('crm::filament/clusters/configurations/resources/reveal-rule.form.size-min'))
                                        ->numeric()
                                        ->integer()
                                        ->minValue(0)
                                        ->default(0)
                                        ->required(),
                                    TextInput::make('company_size_max')
                                        ->label(__('crm::filament/clusters/configurations/resources/reveal-rule.form.size-max'))
                                        ->suffix(__('crm::filament/clusters/configurations/resources/reveal-rule.form.employees'))
                                        ->numeric()
                                        ->integer()
                                        ->minValue(0)
                                        ->default(1000)
                                        ->gte('company_size_min')
                                        ->required(),
                                ]),
                        ]),
                ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }
}
