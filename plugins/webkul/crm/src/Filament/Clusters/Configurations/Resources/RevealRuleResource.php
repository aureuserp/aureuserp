<?php

namespace Webkul\Crm\Filament\Clusters\Configurations\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Crm\Enums\OpportunityPriority;
use Webkul\Crm\Filament\Clusters\Configurations;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\RevealRuleResource\Pages\CreateRevealRule;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\RevealRuleResource\Pages\EditRevealRule;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\RevealRuleResource\Pages\ListRevealRules;
use Webkul\Crm\Models\Lead;
use Webkul\Crm\Models\RevealRule;
use Webkul\Crm\Services\VisitsToLeads;
use Webkul\Crm\Settings\CrmSettings;
use Webkul\Support\Models\State;

class RevealRuleResource extends Resource
{
    protected static ?string $model = RevealRule::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cursor-arrow-ripple';

    protected static ?string $cluster = Configurations::class;

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'name';

    public static function canViewAny(): bool
    {
        return VisitsToLeads::enabled() && parent::canViewAny();
    }

    public static function getModelLabel(): string
    {
        return __('crm::filament/clusters/configurations/resources/reveal-rule.model-label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('crm::filament/clusters/configurations/resources/reveal-rule.plural-model-label');
    }

    public static function getNavigationLabel(): string
    {
        return __('crm::filament/clusters/configurations/resources/reveal-rule.navigation.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('crm::filament/clusters/configurations/resources/reveal-rule.navigation.group');
    }

    public static function form(Schema $schema): Schema
    {
        $label = fn (string $key): string => __('crm::filament/clusters/configurations/resources/reveal-rule.form.'.$key);
        $isPeople = fn (Get $get): bool => $get('lead_for') === RevealRule::LEAD_FOR_PEOPLE;
        $isRoleFilter = fn (Get $get): bool => $get('contact_filter_type') !== RevealRule::CONTACT_FILTER_SENIORITY;

        return $schema
            ->components([
                Section::make()
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label($label('name'))
                            ->placeholder($label('name-placeholder'))
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Radio::make('lead_for')
                            ->label($label('lead-for'))
                            ->options([
                                RevealRule::LEAD_FOR_COMPANIES => $label('lead-for-companies'),
                                RevealRule::LEAD_FOR_PEOPLE    => $label('lead-for-people'),
                            ])
                            ->default(RevealRule::LEAD_FOR_COMPANIES)
                            ->required()
                            ->live(),
                        Toggle::make('is_active')
                            ->label($label('active'))
                            ->default(true)
                            ->inline(false),
                    ]),
                Grid::make(['default' => 1, 'lg' => 2])
                    ->schema([
                        Section::make($label('sections.traffic'))
                            ->schema([
                                Select::make('countries')
                                    ->label($label('countries'))
                                    ->helperText($label('countries-helper'))
                                    ->relationship('countries', 'name')
                                    ->multiple()
                                    ->searchable()
                                    ->preload()
                                    ->live(),
                                Select::make('states')
                                    ->label($label('states'))
                                    ->helperText($label('states-helper'))
                                    ->relationship('states', 'name', fn (Builder $query, Get $get): Builder => $query->whereIn('country_id', (array) $get('countries')))
                                    ->getOptionLabelFromRecordUsing(fn (State $record): string => $record->name.' ('.$record->country?->name.')')
                                    ->multiple()
                                    ->searchable()
                                    ->visible(fn (Get $get): bool => filled($get('countries'))),
                                TextInput::make('regex_url')
                                    ->label($label('url'))
                                    ->placeholder($label('url-placeholder'))
                                    ->helperText($label('url-helper'))
                                    ->maxLength(255)
                                    ->rule(fn (): \Closure => function (string $attribute, mixed $value, \Closure $fail) use ($label): void {
                                        if (filled($value) && ! RevealRule::isValidPattern((string) $value)) {
                                            $fail($label('url-invalid'));
                                        }
                                    }),
                            ]),
                        Section::make($label('sections.generation'))
                            ->schema([
                                Select::make('industries')
                                    ->label($label('industries'))
                                    ->helperText($label('industries-helper'))
                                    ->relationship('industries', 'name')
                                    ->multiple()
                                    ->searchable()
                                    ->preload()
                                    ->createOptionForm([
                                        TextInput::make('name')->label($label('name'))->required()->maxLength(255)->unique('crm_lead_industries', 'name'),
                                    ]),
                                Toggle::make('filter_on_size')
                                    ->label($label('filter-on-size'))
                                    ->default(true)
                                    ->live(),
                                Grid::make(2)
                                    ->visible(fn (Get $get): bool => (bool) $get('filter_on_size'))
                                    ->schema([
                                        TextInput::make('company_size_min')
                                            ->label($label('size-min'))
                                            ->numeric()
                                            ->integer()
                                            ->minValue(0)
                                            ->default(0)
                                            ->required(),
                                        TextInput::make('company_size_max')
                                            ->label($label('size-max'))
                                            ->suffix($label('employees'))
                                            ->numeric()
                                            ->integer()
                                            ->minValue(0)
                                            ->default(1000)
                                            ->gte('company_size_min')
                                            ->required(),
                                    ]),
                            ]),
                    ]),
                Section::make($label('sections.contact-filter'))
                    ->description($label('gdpr'))
                    ->columns(2)
                    ->visible($isPeople)
                    ->schema([
                        TextInput::make('extra_contacts')
                            ->label($label('extra-contacts'))
                            ->helperText($label('extra-contacts-helper'))
                            ->numeric()
                            ->integer()
                            ->minValue(1)
                            ->maxValue(RevealRule::MAX_EXTRA_CONTACTS)
                            ->default(1)
                            ->required(),
                        Radio::make('contact_filter_type')
                            ->label($label('contact-filter'))
                            ->options([
                                RevealRule::CONTACT_FILTER_ROLE      => $label('contact-filter-role'),
                                RevealRule::CONTACT_FILTER_SENIORITY => $label('contact-filter-seniority'),
                            ])
                            ->default(RevealRule::CONTACT_FILTER_ROLE)
                            ->inline()
                            ->live()
                            ->required(),
                        Select::make('preferred_role_id')
                            ->label($label('preferred-role'))
                            ->relationship('preferredRole', 'name')
                            ->searchable()
                            ->preload()
                            ->live()
                            ->createOptionForm([
                                TextInput::make('name')->label($label('name'))->required()->maxLength(255)->unique('crm_lead_roles', 'name'),
                            ])
                            ->visible($isRoleFilter),
                        Select::make('otherRoles')
                            ->label($label('other-roles'))
                            ->relationship('otherRoles', 'name')
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                TextInput::make('name')->label($label('name'))->required()->maxLength(255)->unique('crm_lead_roles', 'name'),
                            ])
                            ->visible(fn (Get $get): bool => $isRoleFilter($get) && filled($get('preferred_role_id'))),
                        Select::make('seniority_id')
                            ->label($label('seniority'))
                            ->relationship('seniority', 'name')
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                TextInput::make('name')->label($label('name'))->required()->maxLength(255)->unique('crm_lead_seniorities', 'name'),
                            ])
                            ->visible(fn (Get $get): bool => ! $isRoleFilter($get)),
                    ]),
                Section::make(fn (Get $get): string => $get('lead_type') === Lead::TYPE_LEAD ? $label('sections.lead-data') : $label('sections.opportunity-data'))
                    ->columns(2)
                    ->schema([
                        Radio::make('lead_type')
                            ->label($label('type'))
                            ->options([
                                Lead::TYPE_LEAD        => __('crm::models/lead.title'),
                                Lead::TYPE_OPPORTUNITY => __('crm::models/opportunity.title'),
                            ])
                            ->default(fn (): string => app(CrmSettings::class)->use_leads ? Lead::TYPE_LEAD : Lead::TYPE_OPPORTUNITY)
                            ->visible(fn (): bool => (bool) app(CrmSettings::class)->use_leads)
                            ->inline()
                            ->live()
                            ->required(),
                        Select::make('tags')
                            ->label($label('tags'))
                            ->relationship('tags', 'name')
                            ->multiple()
                            ->searchable()
                            ->preload(),
                        TextInput::make('suffix')
                            ->label($label('suffix'))
                            ->helperText($label('suffix-helper'))
                            ->maxLength(255),
                        Select::make('priority')
                            ->label($label('priority'))
                            ->options(OpportunityPriority::class)
                            ->default(OpportunityPriority::Low->value)
                            ->selectablePlaceholder(false),
                        Select::make('team_id')
                            ->label($label('team'))
                            ->relationship('team', 'name', fn (Builder $query): Builder => $query->where(owned_by_company(current_company_id())))
                            ->searchable()
                            ->preload(),
                        Select::make('company_id')
                            ->label($label('company'))
                            ->helperText($label('company-helper'))
                            ->relationship('company', 'name', fn (Builder $query): Builder => $query->where('is_active', true))
                            ->searchable()
                            ->preload(),
                        Select::make('user_id')
                            ->label($label('salesperson'))
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload(),
                    ]),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        $label = fn (string $key): string => __('crm::filament/clusters/configurations/resources/reveal-rule.table.'.$key);

        return $table
            ->modifyQueryUsing(fn (Builder $query): Builder => $query
                ->with(['countries', 'team', 'user'])
                ->withCount([
                    'leads as leads_count'         => fn (Builder $query): Builder => $query->withoutGlobalScopes()->where('type', Lead::TYPE_LEAD),
                    'leads as opportunities_count' => fn (Builder $query): Builder => $query->withoutGlobalScopes()->where('type', Lead::TYPE_OPPORTUNITY),
                ]))
            ->columns([
                TextColumn::make('name')
                    ->label($label('name'))
                    ->weight('medium')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('regex_url')
                    ->label($label('url'))
                    ->placeholder($label('whole-website'))
                    ->fontFamily('mono'),
                TextColumn::make('countries.name')
                    ->label($label('countries'))
                    ->badge()
                    ->placeholder($label('all-countries')),
                TextColumn::make('lead_type')
                    ->label($label('type'))
                    ->formatStateUsing(fn (string $state): string => $state === Lead::TYPE_LEAD ? __('crm::models/lead.title') : __('crm::models/opportunity.title'))
                    ->badge(),
                TextColumn::make('team.name')
                    ->label($label('team'))
                    ->placeholder('—'),
                TextColumn::make('user.name')
                    ->label($label('salesperson'))
                    ->placeholder('—'),
                TextColumn::make('leads_count')
                    ->label($label('leads'))
                    ->numeric(),
                TextColumn::make('opportunities_count')
                    ->label($label('opportunities'))
                    ->numeric(),
                ToggleColumn::make('is_active')
                    ->label($label('active')),
            ])
            ->defaultSort('sort')
            ->reorderable('sort')
            ->filters([
                TernaryFilter::make('is_active')
                    ->label($label('active')),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListRevealRules::route('/'),
            'create' => CreateRevealRule::route('/create'),
            'edit'   => EditRevealRule::route('/{record}/edit'),
        ];
    }
}
