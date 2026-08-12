<?php

namespace Webkul\Support\Filament\Resources\CompanyResource\Schemas;

use Filament\Actions\Action;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Support\Models\Currency;

class CompanyForm
{
    public static function configure(Schema $schema, array $customFormFields = []): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Group::make()
                            ->schema([
                                Section::make(__('support::filament/resources/company.form.sections.company-information.title'))
                                    ->schema([
                                        TextInput::make('name')
                                            ->label(__('support::filament/resources/company.form.sections.company-information.fields.name'))
                                            ->required()
                                            ->maxLength(255)
                                            ->unique(table: 'companies', ignoreRecord: true)
                                            ->validationMessages([
                                                'unique' => 'Company name already exists. Please use a unique name.',
                                            ])
                                            ->live(onBlur: true),
                                        TextInput::make('registration_number')
                                            ->label(__('support::filament/resources/company.form.sections.company-information.fields.registration-number'))
                                            ->maxLength(255),
                                        TextInput::make('company_id')
                                            ->label(__('support::filament/resources/company.form.sections.company-information.fields.company-id'))
                                            ->unique(ignoreRecord: true)
                                            ->maxLength(255)
                                            ->hintIcon('heroicon-o-question-mark-circle', tooltip: 'The Company ID is a unique identifier for your company.'),
                                        TextInput::make('tax_id')
                                            ->label(__('support::filament/resources/company.form.sections.company-information.fields.tax-id'))
                                            ->unique(ignoreRecord: true)
                                            ->maxLength(255)
                                            ->hintIcon('heroicon-o-question-mark-circle', tooltip: __('support::filament/resources/company.form.sections.company-information.fields.tax-id-tooltip')),
                                        TextInput::make('website')
                                            ->url()
                                            ->prefixIcon('heroicon-o-globe-alt')
                                            ->maxLength(255)
                                            ->label(__('support::filament/resources/company.form.sections.company-information.fields.website'))
                                            ->unique(ignoreRecord: true),
                                    ])
                                    ->columns(2),
                                Section::make(__('support::filament/resources/company.form.sections.address-information.title'))
                                    ->schema([
                                        Group::make()
                                            ->schema([
                                                TextInput::make('street1')
                                                    ->label(__('support::filament/resources/company.form.sections.address-information.fields.street1'))
                                                    ->maxLength(255),
                                                TextInput::make('street2')
                                                    ->label(__('support::filament/resources/company.form.sections.address-information.fields.street2')),
                                                TextInput::make('city')
                                                    ->maxLength(255),
                                                TextInput::make('zip')
                                                    ->live()
                                                    ->label(__('support::filament/resources/company.form.sections.address-information.fields.zipcode'))
                                                    ->maxLength(255),
                                                Select::make('country_id')
                                                    ->label(__('support::filament/resources/company.form.sections.address-information.fields.country'))
                                                    ->relationship(name: 'country', titleAttribute: 'name')
                                                    ->afterStateUpdated(fn (Set $set) => $set('state_id', null))
                                                    ->searchable()
                                                    ->preload()
                                                    ->live(),
                                                Select::make('state_id')
                                                    ->label(__('support::filament/resources/company.form.sections.address-information.fields.state'))
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
                                                                    ->label(__('support::filament/resources/company.form.sections.address-information.fields.state-name'))
                                                                    ->required(),
                                                                TextInput::make('code')
                                                                    ->label(__('support::filament/resources/company.form.sections.address-information.fields.state-code'))
                                                                    ->required()
                                                                    ->unique('states'),
                                                                Select::make('country_id')
                                                                    ->label(__('support::filament/resources/company.form.sections.address-information.fields.country'))
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
                                Section::make(__('support::filament/resources/company.form.sections.additional-information.title'))
                                    ->schema([
                                        Select::make('currency_id')
                                            ->relationship(
                                                name: 'currency',
                                                titleAttribute: 'name',
                                            )
                                            ->label(__('support::filament/resources/company.form.sections.additional-information.fields.default-currency'))
                                            ->searchable()
                                            ->required()
                                            ->live()
                                            ->preload()
                                            ->default(Currency::active()->first()?->id)
                                            ->createOptionForm([
                                                Section::make()
                                                    ->schema([
                                                        TextInput::make('name')
                                                            ->label(__('support::filament/resources/company.form.sections.additional-information.fields.currency-name'))
                                                            ->required()
                                                            ->maxLength(255)
                                                            ->unique('currencies', 'name', ignoreRecord: true),
                                                        TextInput::make('full_name')
                                                            ->label(__('support::filament/resources/company.form.sections.additional-information.fields.currency-full-name'))
                                                            ->required()
                                                            ->maxLength(255)
                                                            ->unique('currencies', 'full_name', ignoreRecord: true),
                                                        TextInput::make('symbol')
                                                            ->label(__('support::filament/resources/company.form.sections.additional-information.fields.currency-symbol'))
                                                            ->maxLength(255)
                                                            ->required(),
                                                        TextInput::make('iso_numeric')
                                                            ->label(__('support::filament/resources/company.form.sections.additional-information.fields.currency-iso-numeric'))
                                                            ->numeric()
                                                            ->required(),
                                                        TextInput::make('decimal_places')
                                                            ->numeric()
                                                            ->label(__('support::filament/resources/company.form.sections.additional-information.fields.currency-decimal-places'))
                                                            ->required()
                                                            ->rules('min:0', 'max:10'),
                                                        TextInput::make('rounding')
                                                            ->numeric()
                                                            ->label(__('support::filament/resources/company.form.sections.additional-information.fields.currency-rounding'))
                                                            ->required(),
                                                        Toggle::make('active')
                                                            ->label(__('support::filament/resources/company.form.sections.additional-information.fields.currency-status'))
                                                            ->default(true),
                                                    ])->columns(2),
                                            ])
                                            ->createOptionAction(
                                                fn (Action $action) => $action
                                                    ->modalHeading(__('support::filament/resources/company.form.sections.additional-information.fields.currency-create'))
                                                    ->modalSubmitActionLabel(__('support::filament/resources/company.form.sections.additional-information.fields.currency-create'))
                                                    ->modalWidth('xl')
                                            ),
                                        DatePicker::make('founded_date')
                                            ->native(false)
                                            ->label(__('support::filament/resources/company.form.sections.additional-information.fields.company-foundation-date')),
                                        ...$customFormFields,
                                    ])->columns(2),
                            ])
                            ->columnSpan(['lg' => 2]),
                        Group::make()
                            ->schema([
                                Section::make(__('support::filament/resources/company.form.sections.branding.title'))
                                    ->schema([
                                        Group::make()
                                            ->relationship('partner', 'avatar')
                                            ->schema([
                                                FileUpload::make('avatar')
                                                    ->label(__('support::filament/resources/company.form.sections.branding.fields.company-logo'))
                                                    ->image()
                                                    ->directory('company-logos')
                                                    ->visibility('public'),
                                            ]),
                                        ColorPicker::make('color')
                                            ->label(__('support::filament/resources/company.form.sections.branding.fields.color'))
                                            ->hexColor(),
                                    ]),
                                Section::make(__('support::filament/resources/company.form.sections.contact-information.title'))
                                    ->schema([
                                        TextInput::make('phone')
                                            ->label(__('support::filament/resources/company.form.sections.contact-information.fields.phone'))
                                            ->maxLength(255)
                                            ->tel(),
                                        TextInput::make('mobile')
                                            ->label(__('support::filament/resources/company.form.sections.contact-information.fields.mobile'))
                                            ->maxLength(255)
                                            ->tel(),
                                        TextInput::make('email')
                                            ->label(__('support::filament/resources/company.form.sections.contact-information.fields.email'))
                                            ->maxLength(255)
                                            ->email(),
                                    ]),
                            ])
                            ->columnSpan(['lg' => 1]),
                    ])
                    ->columns(3),
            ])
            ->columns(1);
    }
}
