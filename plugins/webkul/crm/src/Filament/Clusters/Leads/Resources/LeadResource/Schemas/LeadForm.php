<?php

namespace Webkul\Crm\Filament\Clusters\Leads\Resources\LeadResource\Schemas;

use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Crm\Enums\OpportunityPriority;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\TagResource\Schemas\TagForm;

class LeadForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        TextInput::make('name')
                            ->hiddenLabel()
                            ->placeholder(__('crm::filament/clusters/leads/resources/lead.form.fields.name-placeholder'))
                            ->required()
                            ->maxLength(255)
                            ->autofocus()
                            ->extraInputAttributes(['style' => 'font-size: 1.5rem;height: 3rem;'])
                            ->columnSpanFull(),
                        TextInput::make('probability')
                            ->label(__('crm::filament/clusters/leads/resources/lead.form.fields.probability'))
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->suffix('%')
                            ->default(10)
                            ->extraFieldWrapperAttributes(['style' => 'max-width: 12rem;']),
                        Grid::make(['default' => 1, 'lg' => 2])
                            ->schema([
                                Group::make()
                                    ->schema([
                                        TextInput::make('company_name')
                                            ->label(__('crm::filament/clusters/leads/resources/lead.form.fields.company-name'))
                                            ->maxLength(255),
                                        Fieldset::make(__('crm::filament/clusters/leads/resources/lead.form.fields.address'))
                                            ->schema([
                                                TextInput::make('street1')
                                                    ->hiddenLabel()
                                                    ->placeholder(__('crm::filament/clusters/leads/resources/lead.form.fields.street1'))
                                                    ->maxLength(255)
                                                    ->columnSpanFull(),
                                                TextInput::make('street2')
                                                    ->hiddenLabel()
                                                    ->placeholder(__('crm::filament/clusters/leads/resources/lead.form.fields.street2'))
                                                    ->maxLength(255)
                                                    ->columnSpanFull(),
                                                TextInput::make('city')
                                                    ->hiddenLabel()
                                                    ->placeholder(__('crm::filament/clusters/leads/resources/lead.form.fields.city'))
                                                    ->maxLength(255),
                                                TextInput::make('zip')
                                                    ->hiddenLabel()
                                                    ->placeholder(__('crm::filament/clusters/leads/resources/lead.form.fields.zip'))
                                                    ->maxLength(255),
                                                Select::make('state_id')
                                                    ->hiddenLabel()
                                                    ->placeholder(__('crm::filament/clusters/leads/resources/lead.form.fields.state'))
                                                    ->relationship(
                                                        name: 'state',
                                                        titleAttribute: 'name',
                                                        modifyQueryUsing: fn (Get $get, Builder $query): Builder => $query->where('country_id', $get('country_id')),
                                                    )
                                                    ->searchable()
                                                    ->preload(),
                                                Select::make('country_id')
                                                    ->hiddenLabel()
                                                    ->placeholder(__('crm::filament/clusters/leads/resources/lead.form.fields.country'))
                                                    ->relationship('country', 'name')
                                                    ->searchable()
                                                    ->preload()
                                                    ->live()
                                                    ->afterStateUpdated(fn (Set $set) => $set('state_id', null)),
                                            ])
                                            ->columns(3),
                                        TextInput::make('website')
                                            ->label(__('crm::filament/clusters/leads/resources/lead.form.fields.website'))
                                            ->placeholder(__('crm::filament/clusters/leads/resources/lead.form.fields.website-placeholder'))
                                            ->url()
                                            ->maxLength(255),
                                        Select::make('user_id')
                                            ->label(__('crm::filament/clusters/leads/resources/lead.form.fields.salesperson'))
                                            ->relationship('user', 'name', modifyQueryUsing: fn (Builder $query, Get $get): Builder => static::scopeSalespeople($query, $get('company_id')))
                                            ->searchable()
                                            ->preload()
                                            ->default(fn (): ?int => auth()->id()),
                                        Select::make('team_id')
                                            ->label(__('crm::filament/clusters/leads/resources/lead.form.fields.team'))
                                            ->relationship('team', 'name', modifyQueryUsing: fn (Builder $query, Get $get): Builder => $query->where(owned_by_company($get('company_id'))))
                                            ->searchable()
                                            ->preload(),
                                    ]),
                                Group::make()
                                    ->schema([
                                        TextInput::make('contact_name')
                                            ->label(__('crm::filament/clusters/leads/resources/lead.form.fields.contact-name'))
                                            ->maxLength(255),
                                        TextInput::make('email')
                                            ->label(__('crm::filament/clusters/leads/resources/lead.form.fields.email'))
                                            ->email()
                                            ->maxLength(255),
                                        TextInput::make('job_title')
                                            ->label(__('crm::filament/clusters/leads/resources/lead.form.fields.job-position'))
                                            ->maxLength(255),
                                        TextInput::make('phone')
                                            ->label(__('crm::filament/clusters/leads/resources/lead.form.fields.phone'))
                                            ->tel()
                                            ->maxLength(255),
                                        Select::make('priority')
                                            ->label(__('crm::filament/clusters/leads/resources/lead.form.fields.priority'))
                                            ->options(OpportunityPriority::class)
                                            ->default(OpportunityPriority::Low),
                                        Select::make('tags')
                                            ->label(__('crm::filament/clusters/leads/resources/lead.form.fields.tags'))
                                            ->relationship('tags', 'name')
                                            ->multiple()
                                            ->searchable()
                                            ->preload()
                                            ->createOptionForm(fn (Schema $schema): Schema => TagForm::configure($schema)),
                                    ]),
                            ]),
                    ]),
                Tabs::make()
                    ->tabs([
                        Tab::make(__('crm::filament/clusters/leads/resources/lead.form.tabs.notes'))
                            ->icon('heroicon-o-pencil-square')
                            ->schema([
                                RichEditor::make('description')
                                    ->hiddenLabel(),
                            ]),
                        Tab::make(__('crm::filament/clusters/leads/resources/lead.form.tabs.extra-info'))
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Grid::make(['default' => 1, 'lg' => 2])
                                    ->schema([
                                        Fieldset::make(__('crm::filament/clusters/leads/resources/lead.form.fieldsets.marketing'))
                                            ->schema([
                                                Select::make('company_id')
                                                    ->label(__('crm::filament/clusters/leads/resources/lead.form.fields.company'))
                                                    ->relationship('company', 'name')
                                                    ->live()
                                                    ->searchable()
                                                    ->preload()
                                                    ->default(fn (): ?int => current_company_id()),
                                                Select::make('utm_campaign_id')
                                                    ->label(__('crm::filament/clusters/leads/resources/lead.form.fields.campaign'))
                                                    ->relationship('campaign', 'name')
                                                    ->searchable()
                                                    ->preload(),
                                                Select::make('utm_medium_id')
                                                    ->label(__('crm::filament/clusters/leads/resources/lead.form.fields.medium'))
                                                    ->relationship('medium', 'name')
                                                    ->searchable()
                                                    ->preload(),
                                                Select::make('utm_source_id')
                                                    ->label(__('crm::filament/clusters/leads/resources/lead.form.fields.source'))
                                                    ->relationship('source', 'name')
                                                    ->searchable()
                                                    ->preload(),
                                                TextInput::make('referred_by')
                                                    ->label(__('crm::filament/clusters/leads/resources/lead.form.fields.referred-by'))
                                                    ->maxLength(255),
                                            ])
                                            ->columns(1),
                                        Fieldset::make(__('crm::filament/clusters/leads/resources/lead.form.fieldsets.analysis'))
                                            ->schema([
                                                TextEntry::make('assigned_at')
                                                    ->label(__('crm::filament/clusters/leads/resources/lead.form.fields.assignment-date'))
                                                    ->dateTime()
                                                    ->placeholder('—'),
                                                TextEntry::make('closed_at')
                                                    ->label(__('crm::filament/clusters/leads/resources/lead.form.fields.closed-date'))
                                                    ->dateTime()
                                                    ->placeholder('—'),
                                            ])
                                            ->columns(1),
                                    ]),
                            ]),
                    ])
                    ->persistTabInQueryString()
                    ->columnSpanFull(),
            ])
            ->columns(1);
    }

    /**
     * Active users allowed to work in the given company (all active users when no company is set).
     */
    public static function scopeSalespeople(Builder $query, mixed $companyId): Builder
    {
        return $query
            ->where('is_active', true)
            ->when(filled($companyId), fn (Builder $query): Builder => $query->where(fn (Builder $query): Builder => $query
                ->where('default_company_id', $companyId)
                ->orWhereHas('allowedCompanies', fn (Builder $query): Builder => $query->whereKey($companyId))));
    }
}
