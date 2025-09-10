<?php

namespace Webkul\Partner\Filament\Resources\Partners\Schemas;

use Filament\Actions\Action;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Webkul\Partner\Enums\AccountType;
use Webkul\Partner\Filament\Resources\Partners\PartnerResource;

class PartnerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('partners::filament/resources/partner.form.sections.general.title'))
                    ->schema([
                        Group::make()
                            ->schema([
                                Group::make()
                                    ->schema([
                                        Radio::make('account_type')
                                            ->hiddenLabel()
                                            ->inline()
                                            ->columnSpan(2)
                                            ->options(AccountType::class)
                                            ->default(AccountType::INDIVIDUAL->value)
                                            ->options(function () {
                                                $options = AccountType::options();

                                                unset($options[AccountType::ADDRESS->value]);

                                                return $options;
                                            })
                                            ->live(),
                                        TextInput::make('name')
                                            ->hiddenLabel()
                                            ->required()
                                            ->maxLength(255)
                                            ->columnSpan(2)
                                            ->placeholder(fn (Get $get): string => $get('account_type') === AccountType::INDIVIDUAL->value ? 'Jhon Doe' : 'ACME Corp')
                                            ->extraInputAttributes(['style' => 'font-size: 1.5rem;height: 3rem;']),
                                        Select::make('parent_id')
                                            ->label(__('partners::filament/resources/partner.form.sections.general.fields.company'))
                                            ->relationship(
                                                name: 'parent',
                                                titleAttribute: 'name',
                                                // modifyQueryUsing: fn (Builder $query) => $query->where('account_type', AccountType::COMPANY->value),
                                            )
                                            ->visible(fn (Get $get): bool => $get('account_type') === AccountType::INDIVIDUAL->value)
                                            ->searchable()
                                            ->preload()
                                            ->columnSpan(2)
                                            ->createOptionForm(fn (Schema $schema): Schema => PartnerResource::form($schema))
                                            ->editOptionForm(fn (Schema $schema): Schema => PartnerResource::form($schema))
                                            ->createOptionAction(function (Action $action) {
                                                $action
                                                    ->fillForm(function (array $arguments): array {
                                                        return [
                                                            'account_type' => AccountType::COMPANY->value,
                                                        ];
                                                    })
                                                    ->mutateDataUsing(function (array $data) {
                                                        $data['account_type'] = AccountType::COMPANY->value;

                                                        return $data;
                                                    });
                                            }),
                                    ]),
                                Group::make()
                                    ->schema([
                                        FileUpload::make('avatar')
                                            ->image()
                                            ->hiddenLabel()
                                            ->imageResizeMode('cover')
                                            ->imageEditor()
                                            ->avatar()
                                            ->directory('partners/avatar')
                                            ->visibility('private'),
                                    ]),
                            ])->columns(2),
                        Group::make()
                            ->schema([
                                TextInput::make('tax_id')
                                    ->label(__('partners::filament/resources/partner.form.sections.general.fields.tax-id'))
                                    ->placeholder('e.g. 29ABCDE1234F1Z5')
                                    ->maxLength(255),
                                TextInput::make('job_title')
                                    ->label(__('partners::filament/resources/partner.form.sections.general.fields.job-title'))
                                    ->placeholder('e.g. CEO')
                                    ->maxLength(255),
                                TextInput::make('phone')
                                    ->label(__('partners::filament/resources/partner.form.sections.general.fields.phone'))
                                    ->tel()
                                    ->maxLength(255),
                                TextInput::make('mobile')
                                    ->label(__('partners::filament/resources/partner.form.sections.general.fields.mobile'))
                                    ->maxLength(255)
                                    ->tel(),
                                TextInput::make('email')
                                    ->label(__('partners::filament/resources/partner.form.sections.general.fields.email'))
                                    ->email()
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true),
                                TextInput::make('website')
                                    ->label(__('partners::filament/resources/partner.form.sections.general.fields.website'))
                                    ->maxLength(255)
                                    ->url(),
                                Select::make('title_id')
                                    ->label(__('partners::filament/resources/partner.form.sections.general.fields.title'))
                                    ->relationship('title', 'name')
                                    ->createOptionForm([
                                        TextInput::make('name')
                                            ->label(__('partners::filament/resources/partner.form.sections.general.fields.name'))
                                            ->required()
                                            ->maxLength(255)
                                            ->unique('partners_titles'),
                                        TextInput::make('short_name')
                                            ->label(__('partners::filament/resources/partner.form.sections.general.fields.short-name'))
                                            ->label('Short Name')
                                            ->required()
                                            ->maxLength(255)
                                            ->unique('partners_titles'),
                                        Hidden::make('creator_id')
                                            ->default(Auth::user()->id),
                                    ]),
                                Select::make('tags')
                                    ->label(__('partners::filament/resources/partner.form.sections.general.fields.tags'))
                                    ->relationship(name: 'tags', titleAttribute: 'name')
                                    ->multiple()
                                    ->searchable()
                                    ->preload()
                                    ->createOptionForm([
                                        Group::make()
                                            ->schema([
                                                TextInput::make('name')
                                                    ->label(__('partners::filament/resources/partner.form.sections.general.fields.name'))
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->unique('partners_tags'),
                                                ColorPicker::make('color')
                                                    ->label(__('partners::filament/resources/partner.form.sections.general.fields.color'))
                                                    ->hexColor(),
                                            ])
                                            ->columns(2),
                                    ]),

                                Fieldset::make('Address')
                                    ->schema([
                                        TextInput::make('street1')
                                            ->label(__('partners::filament/resources/partner.form.sections.general.address.fields.street1'))
                                            ->maxLength(255),
                                        TextInput::make('street2')
                                            ->label(__('partners::filament/resources/partner.form.sections.general.address.fields.street2'))
                                            ->maxLength(255),
                                        TextInput::make('city')
                                            ->label(__('partners::filament/resources/partner.form.sections.general.address.fields.city'))
                                            ->maxLength(255),
                                        TextInput::make('zip')
                                            ->label(__('partners::filament/resources/partner.form.sections.general.address.fields.zip'))
                                            ->maxLength(255),
                                        Select::make('country_id')
                                            ->label(__('partners::filament/resources/partner.form.sections.general.address.fields.country'))
                                            ->relationship(name: 'country', titleAttribute: 'name')
                                            ->afterStateUpdated(fn (Set $set) => $set('state_id', null))
                                            ->searchable()
                                            ->preload()
                                            ->afterStateUpdated(function (Set $set, Get $get) {
                                                $set('state_id', null);
                                            })
                                            ->live(),
                                        Select::make('state_id')
                                            ->label(__('partners::filament/resources/partner.form.sections.general.address.fields.state'))
                                            ->relationship(
                                                name: 'state',
                                                titleAttribute: 'name',
                                                modifyQueryUsing: fn (Get $get, Builder $query) => $query->where('country_id', $get('country_id')),
                                            )
                                            ->createOptionForm(function (Schema $schema, Get $get, Set $set) {
                                                return $schema
                                                    ->components([
                                                        TextInput::make('name')
                                                            ->label(__('partners::filament/resources/partner.form.sections.general.address.fields.name'))
                                                            ->required()
                                                            ->maxLength(255),
                                                        TextInput::make('code')
                                                            ->label(__('partners::filament/resources/partner.form.sections.general.address.fields.code'))
                                                            ->required()
                                                            ->unique('states')
                                                            ->maxLength(255),
                                                        Select::make('country_id')
                                                            ->label(__('partners::filament/resources/partner.form.sections.general.address.fields.country'))
                                                            ->relationship('country', 'name')
                                                            ->searchable()
                                                            ->preload()
                                                            ->live()
                                                            ->default($get('country_id'))
                                                            ->afterStateUpdated(function (Get $get) use ($set) {
                                                                $set('country_id', $get('country_id'));
                                                            }),
                                                    ]);
                                            })
                                            ->searchable()
                                            ->preload(),
                                    ])
                                    ->columnSpanFull(),
                            ])
                            ->columns(2),
                    ])->columnSpanFull(),

                Tabs::make('tabs')
                    ->tabs([
                        Tab::make(__('partners::filament/resources/partner.form.tabs.sales-purchase.title'))
                            ->icon('heroicon-o-currency-dollar')
                            ->schema([
                                Fieldset::make('Sales')
                                    ->schema([
                                        Select::make('user_id')
                                            ->label(__('partners::filament/resources/partner.form.tabs.sales-purchase.fields.responsible'))
                                            ->relationship('user', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('partners::filament/resources/partner.form.tabs.sales-purchase.fields.responsible-hint-text')),
                                    ])
                                    ->columns(1),

                                Fieldset::make('Others')
                                    ->schema([
                                        TextInput::make('company_registry')
                                            ->label(__('partners::filament/resources/partner.form.tabs.sales-purchase.fields.company-id'))
                                            ->maxLength(255)
                                            ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('partners::filament/resources/partner.form.tabs.sales-purchase.fields.company-id-hint-text')),
                                        TextInput::make('reference')
                                            ->label(__('partners::filament/resources/partner.form.tabs.sales-purchase.fields.reference'))
                                            ->maxLength(255),
                                        Select::make('industry_id')
                                            ->label(__('partners::filament/resources/partner.form.tabs.sales-purchase.fields.industry'))
                                            ->relationship('industry', 'name'),
                                    ])
                                    ->columns(2),
                            ])
                            ->columns(2),
                    ])
                    ->columnSpan(2),
            ])
            ->columns(2);
    }
}
