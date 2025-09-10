<?php

namespace Webkul\Security\Filament\Resources\Companies\RelationManagers\Schemas;

use Filament\Actions\Action;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Support\Models\Currency;

class BranchForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Tabs::make()
                    ->tabs([
                        Tab::make(__('security::filament/resources/company/relation-managers/manage-branch.form.tabs.general-information.title'))
                            ->schema([
                                Section::make(__('security::filament/resources/company/relation-managers/manage-branch.form.tabs.general-information.sections.branch-information.title'))
                                    ->schema([
                                        TextInput::make('name')
                                            ->label(__('security::filament/resources/company/relation-managers/manage-branch.form.tabs.general-information.sections.branch-information.fields.company-name'))
                                            ->required()
                                            ->maxLength(255)
                                            ->live(onBlur: true),
                                        TextInput::make('registration_number')
                                            ->label(__('security::filament/resources/company/relation-managers/manage-branch.form.tabs.general-information.sections.branch-information.fields.registration-number')),
                                        TextInput::make('company_id')
                                            ->label(__('security::filament/resources/company/relation-managers/manage-branch.form.tabs.general-information.sections.branch-information.fields.company-id'))
                                            ->unique(ignoreRecord: true)
                                            ->hintIcon('heroicon-o-question-mark-circle', tooltip: __('security::filament/resources/company/relation-managers/manage-branch.form.tabs.general-information.sections.branch-information.fields.company-id-tooltip')),
                                        TextInput::make('tax_id')
                                            ->label(__('security::filament/resources/company/relation-managers/manage-branch.form.tabs.general-information.sections.branch-information.fields.tax-id'))
                                            ->unique(ignoreRecord: true)
                                            ->hintIcon('heroicon-o-question-mark-circle', tooltip: __('security::filament/resources/company/relation-managers/manage-branch.form.tabs.general-information.sections.branch-information.fields.tax-id-tooltip')),
                                        ColorPicker::make('color')
                                            ->label(__('security::filament/resources/company/relation-managers/manage-branch.form.tabs.general-information.sections.branch-information.fields.color'))
                                            ->hexColor(),
                                    ])
                                    ->columns(2),
                                Section::make(__('security::filament/resources/company/relation-managers/manage-branch.form.tabs.general-information.sections.branding.title'))
                                    ->relationship('partner', 'avatar')
                                    ->schema([
                                        FileUpload::make('avatar')
                                            ->label(__('security::filament/resources/company/relation-managers/manage-branch.form.tabs.general-information.sections.branding.fields.branch-logo'))
                                            ->image()
                                            ->directory('company-logos')
                                            ->visibility('private'),
                                    ]),
                            ])
                            ->columnSpanFull(),
                        Tab::make(__('security::filament/resources/company/relation-managers/manage-branch.form.tabs.address-information.title'))
                            ->schema([
                                Section::make(__('security::filament/resources/company/relation-managers/manage-branch.form.tabs.address-information.sections.address-information.title'))
                                    ->schema([
                                        Group::make()
                                            ->schema([
                                                TextInput::make('street1')
                                                    ->label(__('security::filament/resources/company/relation-managers/manage-branch.form.tabs.address-information.sections.address-information.fields.street1')),
                                                TextInput::make('street2')
                                                    ->label(__('security::filament/resources/company/relation-managers/manage-branch.form.tabs.address-information.sections.address-information.fields.street2')),
                                                TextInput::make('city')
                                                    ->label(__('security::filament/resources/company/relation-managers/manage-branch.form.tabs.address-information.sections.address-information.fields.city')),
                                                TextInput::make('zip')
                                                    ->live()
                                                    ->label(__('security::filament/resources/company/relation-managers/manage-branch.form.tabs.address-information.sections.address-information.fields.zip-code')),
                                                Select::make('country_id')
                                                    ->label(__('security::filament/resources/company/relation-managers/manage-branch.form.tabs.address-information.sections.address-information.fields.country'))
                                                    ->relationship(name: 'country', titleAttribute: 'name')
                                                    ->afterStateUpdated(fn (Set $set) => $set('state_id', null))
                                                    ->searchable()
                                                    ->preload()
                                                    ->live(),
                                                Select::make('state_id')
                                                    ->label(__('security::filament/resources/company/relation-managers/manage-branch.form.tabs.address-information.sections.address-information.fields.state'))
                                                    ->relationship(
                                                        name: 'state',
                                                        titleAttribute: 'name',
                                                        modifyQueryUsing: fn (Get $get, Builder $query) => $query->where('country_id', $get('country_id')),
                                                    )
                                                    ->searchable()
                                                    ->preload()
                                                    ->createOptionForm(function (Schema $schema, Get $get, Set $set) {
                                                        return $schema
                                                            ->components([
                                                                TextInput::make('name')
                                                                    ->label(__('security::filament/resources/company/relation-managers/manage-branch.form.tabs.address-information.sections.address-information.fields.state-name'))
                                                                    ->required(),
                                                                TextInput::make('code')
                                                                    ->label(__('security::filament/resources/company/relation-managers/manage-branch.form.tabs.address-information.sections.address-information.fields.state-code'))
                                                                    ->required()
                                                                    ->unique('states'),
                                                                Select::make('country_id')
                                                                    ->label(__('security::filament/resources/company/relation-managers/manage-branch.form.tabs.address-information.sections.address-information.fields.country'))
                                                                    ->relationship('country', 'name')
                                                                    ->searchable()
                                                                    ->preload()
                                                                    ->live()
                                                                    ->default($get('country_id'))
                                                                    ->afterStateUpdated(function (Get $get) use ($set) {
                                                                        $set('country_id', $get('country_id'));
                                                                    }),
                                                            ]);
                                                    }),
                                            ])
                                            ->columns(2),
                                    ]),
                                Section::make(__('security::filament/resources/company/relation-managers/manage-branch.form.tabs.address-information.sections.additional-information.title'))
                                    ->schema([
                                        Select::make('currency_id')
                                            ->relationship('currency', 'full_name')
                                            ->label(__('security::filament/resources/company/relation-managers/manage-branch.form.tabs.address-information.sections.additional-information.fields.default-currency'))
                                            ->relationship('currency', 'full_name')
                                            ->searchable()
                                            ->required()
                                            ->live()
                                            ->preload()
                                            ->default(Currency::first()?->id)
                                            ->createOptionForm([
                                                Section::make()
                                                    ->schema([
                                                        TextInput::make('name')
                                                            ->label(__('security::filament/resources/company/relation-managers/manage-branch.form.tabs.address-information.sections.additional-information.fields.currency-name'))
                                                            ->required()
                                                            ->maxLength(255)
                                                            ->unique('currencies', 'name', ignoreRecord: true),
                                                        TextInput::make('full_name')
                                                            ->label(__('security::filament/resources/company/relation-managers/manage-branch.form.tabs.address-information.sections.additional-information.fields.currency-full-name'))
                                                            ->required()
                                                            ->maxLength(255)
                                                            ->unique('currencies', 'full_name', ignoreRecord: true),
                                                        TextInput::make('symbol')
                                                            ->label(__('security::filament/resources/company/relation-managers/manage-branch.form.tabs.address-information.sections.additional-information.fields.currency-symbol'))
                                                            ->required(),
                                                        TextInput::make('iso_numeric')
                                                            ->label(__('security::filament/resources/company/relation-managers/manage-branch.form.tabs.address-information.sections.additional-information.fields.currency-iso-numeric'))
                                                            ->numeric()
                                                            ->required(),
                                                        TextInput::make('decimal_places')
                                                            ->numeric()
                                                            ->label(__('security::filament/resources/company/relation-managers/manage-branch.form.tabs.address-information.sections.additional-information.fields.currency-decimal-places'))
                                                            ->required()
                                                            ->rules('min:0', 'max:10'),
                                                        TextInput::make('rounding')
                                                            ->numeric()
                                                            ->label(__('security::filament/resources/company/relation-managers/manage-branch.form.tabs.address-information.sections.additional-information.fields.currency-rounding'))
                                                            ->required(),
                                                        Toggle::make('active')
                                                            ->label('Active')
                                                            ->label(__('security::filament/resources/company/relation-managers/manage-branch.form.tabs.address-information.sections.additional-information.fields.currency-status'))
                                                            ->default(true),
                                                    ])->columns(2),
                                            ])
                                            ->createOptionAction(
                                                fn (Action $action) => $action
                                                    ->modalHeading(__('security::filament/resources/company/relation-managers/manage-branch.form.tabs.address-information.sections.additional-information.fields.currency-create'))
                                                    ->modalSubmitActionLabel(__('security::filament/resources/company/relation-managers/manage-branch.form.tabs.address-information.sections.additional-information.fields.currency-create'))
                                                    ->modalWidth('lg')
                                            ),
                                        DatePicker::make('founded_date')
                                            ->native(false)
                                            ->label(__('security::filament/resources/company/relation-managers/manage-branch.form.tabs.address-information.sections.additional-information.fields.company-foundation-date')),
                                        Toggle::make('is_active')
                                            ->label(__('security::filament/resources/company/relation-managers/manage-branch.form.tabs.address-information.sections.additional-information.fields.status'))
                                            ->default(true),
                                    ]),
                            ])
                            ->columnSpanFull(),
                        Tab::make(__('security::filament/resources/company/relation-managers/manage-branch.form.tabs.contact-information.title'))
                            ->schema([
                                Section::make(__('security::filament/resources/company/relation-managers/manage-branch.form.tabs.contact-information.sections.contact-information.title'))
                                    ->schema([
                                        TextInput::make('phone')
                                            ->label(__('security::filament/resources/company/relation-managers/manage-branch.form.tabs.contact-information.sections.contact-information.fields.phone-number'))
                                            ->tel(),
                                        TextInput::make('mobile')
                                            ->label(__('security::filament/resources/company/relation-managers/manage-branch.form.tabs.contact-information.sections.contact-information.fields.mobile-number'))
                                            ->tel(),
                                        TextInput::make('email')
                                            ->label(__('security::filament/resources/company/relation-managers/manage-branch.form.tabs.contact-information.sections.contact-information.fields.email-address'))
                                            ->email(),
                                    ])
                                    ->columns(2),
                            ])
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
