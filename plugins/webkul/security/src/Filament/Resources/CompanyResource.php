<?php

namespace Webkul\Security\Filament\Resources;

use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Webkul\Field\Filament\Traits\HasCustomFields;
use Webkul\Security\Enums\CompanyStatus;
use Webkul\Security\Filament\Resources\CompanyResource\Pages;
use Webkul\Security\Filament\Resources\CompanyResource\RelationManagers;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Country;
use Webkul\Support\Models\Currency;
use Webkul\Support\Models\State;

class CompanyResource extends Resource
{
    use HasCustomFields;

    protected static ?string $model = Company::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';

    protected static ?int $navigationSort = 2;

    public static function getNavigationLabel(): string
    {
        return __('security::filament/resources/company.navigation.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('security::filament/resources/company.navigation.group');
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'email'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            __('security::filament/resources/company.global-search.name')  => $record->name ?? '—',
            __('security::filament/resources/company.global-search.email') => $record->email ?? '—',
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Group::make()
                            ->schema([
                                Forms\Components\Section::make(__('security::filament/resources/company.form.sections.company-information.title'))
                                    ->schema([
                                        Forms\Components\Hidden::make('sort')
                                            ->default(Company::max('sort') + 1),
                                        Forms\Components\TextInput::make('name')
                                            ->label(__('security::filament/resources/company.form.sections.company-information.fields.name'))
                                            ->required()
                                            ->maxLength(255)
                                            ->live(onBlur: true),
                                        Forms\Components\TextInput::make('registration_number')
                                            ->label(__('security::filament/resources/company.form.sections.company-information.fields.registration-number'))
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('company_id')
                                            ->label(__('security::filament/resources/company.form.sections.company-information.fields.company-id'))
                                            ->required()
                                            ->unique(ignoreRecord: true)
                                            ->maxLength(255)
                                            ->hintIcon('heroicon-o-question-mark-circle', tooltip: 'The Company ID is a unique identifier for your company.'),
                                        Forms\Components\TextInput::make('tax_id')
                                            ->label(__('security::filament/resources/company.form.sections.company-information.fields.tax-id'))
                                            ->unique(ignoreRecord: true)
                                            ->maxLength(255)
                                            ->hintIcon('heroicon-o-question-mark-circle', tooltip: __('security::filament/resources/company.form.sections.company-information.fields.tax-id-tooltip')),
                                        Forms\Components\TextInput::make('website')
                                            ->url()
                                            ->prefixIcon('heroicon-o-globe-alt')
                                            ->maxLength(255)
                                            ->label(__('security::filament/resources/company.form.sections.company-information.fields.website'))
                                            ->unique(ignoreRecord: true),
                                    ])
                                    ->columns(2),
                                Forms\Components\Section::make(__('security::filament/resources/company.form.sections.address-information.title'))
                                    ->schema([
                                        Forms\Components\Group::make()
                                            ->relationship('address')
                                            ->schema([
                                                Forms\Components\TextInput::make('street1')
                                                    ->label(__('security::filament/resources/company.form.sections.address-information.fields.street1'))
                                                    ->maxLength(255)
                                                    ->required(),
                                                Forms\Components\TextInput::make('street2')
                                                    ->label(__('security::filament/resources/company.form.sections.address-information.fields.street2')),
                                                Forms\Components\TextInput::make('city')
                                                    ->maxLength(255)
                                                    ->required(),
                                                Forms\Components\TextInput::make('zip')
                                                    ->live()
                                                    ->label(__('security::filament/resources/company.form.sections.address-information.fields.zipcode'))
                                                    ->maxLength(255)
                                                    ->required(fn (Get $get) => Country::find($get('country_id'))?->zip_required),
                                                Forms\Components\Select::make('country_id')
                                                    ->label(__('security::filament/resources/company.form.sections.address-information.fields.country'))
                                                    ->relationship(name: 'country', titleAttribute: 'name')
                                                    ->afterStateUpdated(fn (Set $set) => $set('state_id', null))
                                                    ->createOptionForm([
                                                        Forms\Components\Select::make('currency_id')
                                                            ->options(fn () => Currency::pluck('full_name', 'id'))
                                                            ->searchable()
                                                            ->preload()
                                                            ->label(__('security::filament/resources/company.form.sections.address-information.fields.currency-name'))
                                                            ->required(),
                                                        Forms\Components\TextInput::make('phone_code')
                                                            ->label(__('security::filament/resources/company.form.sections.address-information.fields.phone-code'))
                                                            ->required()
                                                            ->maxLength(255),
                                                        Forms\Components\TextInput::make('code')
                                                            ->label(__('security::filament/resources/company.form.sections.address-information.fields.code'))
                                                            ->required()
                                                            ->rules('max:2'),
                                                        Forms\Components\TextInput::make('name')
                                                            ->label(__('security::filament/resources/company.form.sections.address-information.fields.country-name'))
                                                            ->maxLength(255)
                                                            ->required(),
                                                        Forms\Components\Toggle::make('state_required')
                                                            ->label(__('security::filament/resources/company.form.sections.address-information.fields.state-required'))
                                                            ->maxLength(255)
                                                            ->required(),
                                                        Forms\Components\Toggle::make('zip_required')
                                                            ->label(__('security::filament/resources/company.form.sections.address-information.fields.zip-required'))
                                                            ->maxLength(255)
                                                            ->required(),
                                                    ])
                                                    ->createOptionAction(
                                                        fn (Action $action) => $action
                                                            ->modalHeading(__('security::filament/resources/company.form.sections.address-information.fields.create-country'))
                                                            ->modalSubmitActionLabel(__('security::filament/resources/company.form.sections.address-information.fields.create-country'))
                                                            ->modalWidth('lg')
                                                    )
                                                    ->searchable()
                                                    ->preload()
                                                    ->live()
                                                    ->required(),
                                                Forms\Components\Select::make('state_id')
                                                    ->label(__('security::filament/resources/company.form.sections.address-information.fields.state'))
                                                    ->options(
                                                        fn (Get $get): Collection => State::query()
                                                            ->where('country_id', $get('country_id'))
                                                            ->pluck('name', 'id')
                                                    )
                                                    ->createOptionForm([
                                                        Forms\Components\TextInput::make('name')
                                                            ->label(__('security::filament/resources/company.form.sections.address-information.fields.state-name'))
                                                            ->required()
                                                            ->maxLength(255),
                                                        Forms\Components\TextInput::make('code')
                                                            ->label(__('security::filament/resources/company.form.sections.address-information.fields.state-code'))
                                                            ->required()
                                                            ->maxLength(255),
                                                    ])
                                                    ->createOptionAction(
                                                        fn (Action $action) => $action
                                                            ->modalHeading('Create State')
                                                            ->modalSubmitActionLabel('Create State')
                                                            ->modalWidth('lg')
                                                    )
                                                    ->searchable()
                                                    ->preload()
                                                    ->required(fn (Get $get) => Country::find($get('country_id'))?->state_required),
                                            ])
                                            ->columns(2),
                                    ]),
                                Forms\Components\Section::make(__('security::filament/resources/company.form.sections.additional-information.title'))
                                    ->schema([
                                        Forms\Components\Select::make('currency_id')
                                            ->relationship('currency', 'full_name')
                                            ->label(__('security::filament/resources/company.form.sections.additional-information.fields.default-currency'))
                                            ->searchable()
                                            ->required()
                                            ->live()
                                            ->preload()
                                            ->options(fn () => Currency::pluck('full_name', 'id'))
                                            ->createOptionForm([
                                                Forms\Components\Section::make()
                                                    ->schema([
                                                        Forms\Components\TextInput::make('name')
                                                            ->label(__('security::filament/resources/company.form.sections.additional-information.fields.currency-name'))
                                                            ->required()
                                                            ->maxLength(255)
                                                            ->unique('currencies', 'name', ignoreRecord: true),
                                                        Forms\Components\TextInput::make('full_name')
                                                            ->label(__('security::filament/resources/company.form.sections.additional-information.fields.currency-full-name'))
                                                            ->required()
                                                            ->maxLength(255)
                                                            ->unique('currencies', 'full_name', ignoreRecord: true),
                                                        Forms\Components\TextInput::make('symbol')
                                                            ->label(__('security::filament/resources/company.form.sections.additional-information.fields.currency-symbol'))
                                                            ->maxLength(255)
                                                            ->required(),
                                                        Forms\Components\TextInput::make('iso_numeric')
                                                            ->label(__('security::filament/resources/company.form.sections.additional-information.fields.currency-iso-numeric'))
                                                            ->numeric()
                                                            ->required(),
                                                        Forms\Components\TextInput::make('decimal_places')
                                                            ->numeric()
                                                            ->label(__('security::filament/resources/company.form.sections.additional-information.fields.currency-decimal-places'))
                                                            ->required()
                                                            ->rules('min:0', 'max:10'),
                                                        Forms\Components\TextInput::make('rounding')
                                                            ->numeric()
                                                            ->label(__('security::filament/resources/company.form.sections.additional-information.fields.currency-rounding'))
                                                            ->required(),
                                                        Forms\Components\Toggle::make('active')
                                                            ->label(__('security::filament/resources/company.form.sections.additional-information.fields.currency-status'))
                                                            ->default(true),
                                                    ])->columns(2),
                                            ])
                                            ->createOptionAction(
                                                fn (Action $action) => $action
                                                    ->modalHeading(__('security::filament/resources/company.form.sections.additional-information.fields.currency-create'))
                                                    ->modalSubmitActionLabel(__('security::filament/resources/company.form.sections.additional-information.fields.currency-create'))
                                                    ->modalWidth('xl')
                                            ),
                                        Forms\Components\DatePicker::make('founded_date')
                                            ->native(false)
                                            ->label(__('security::filament/resources/company.form.sections.additional-information.fields.company-foundation-date')),
                                        Forms\Components\Toggle::make('is_active')
                                            ->label(__('security::filament/resources/company.form.sections.additional-information.fields.status'))
                                            ->default(true),
                                        ...static::getCustomFormFields(),
                                    ])->columns(2),
                            ])
                            ->columnSpan(['lg' => 2]),
                        Forms\Components\Group::make()
                            ->schema([
                                Forms\Components\Section::make(__('security::filament/resources/company.form.sections.branding.title'))
                                    ->relationship('partner', 'avatar')
                                    ->schema([
                                        Forms\Components\FileUpload::make('avatar')
                                            ->label(__('security::filament/resources/company.form.sections.branding.fields.company-logo'))
                                            ->image()
                                            ->directory('company-logos')
                                            ->visibility('private'),
                                        Forms\Components\ColorPicker::make('color')
                                            ->label(__('security::filament/resources/company.form.sections.branding.fields.color'))
                                            ->hexColor(),
                                    ]),
                                Forms\Components\Section::make(__('security::filament/resources/company.form.sections.contact-information.title'))
                                    ->schema([
                                        Forms\Components\TextInput::make('phone')
                                            ->label(__('security::filament/resources/company.form.sections.contact-information.fields.phone'))
                                            ->maxLength(255)
                                            ->required(),
                                        Forms\Components\TextInput::make('mobile')
                                            ->label(__('security::filament/resources/company.form.sections.contact-information.fields.mobile'))
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('email')
                                            ->label(__('security::filament/resources/company.form.sections.contact-information.fields.email'))
                                            ->maxLength(255)
                                            ->email(),
                                    ]),
                            ])
                            ->columnSpan(['lg' => 1]),
                    ])
                    ->columns(3),
            ])
            ->columns('full');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('partner.avatar')
                    ->circular()
                    ->size(50)
                    ->label(__('security::filament/resources/company.table.columns.logo')),
                Tables\Columns\TextColumn::make('name')
                    ->label(__('security::filament/resources/company.table.columns.company-name'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('branches.name')
                    ->label(__('security::filament/resources/company.table.columns.branches'))
                    ->placeholder('-')
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label(__('security::filament/resources/company.table.columns.email'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('address.city')
                    ->label(__('security::filament/resources/company.table.columns.city'))
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('address.country.name')
                    ->label(__('security::filament/resources/company.table.columns.country'))
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('currency.full_name')
                    ->label(__('security::filament/resources/company.table.columns.currency'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->sortable()
                    ->label(__('security::filament/resources/company.table.columns.status'))
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('security::filament/resources/company.table.columns.created-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('security::filament/resources/company.table.columns.updated-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->columnToggleFormColumns(2)
            ->groups([
                Tables\Grouping\Group::make('name')
                    ->label(__('security::filament/resources/company.table.groups.company-name'))
                    ->collapsible(),
                Tables\Grouping\Group::make('city')
                    ->label(__('security::filament/resources/company.table.groups.city'))
                    ->collapsible(),
                Tables\Grouping\Group::make('country.name')
                    ->label(__('security::filament/resources/company.table.groups.country'))
                    ->collapsible(),
                Tables\Grouping\Group::make('state.name')
                    ->label(__('security::filament/resources/company.table.groups.state'))
                    ->collapsible(),
                Tables\Grouping\Group::make('email')
                    ->label(__('security::filament/resources/company.table.groups.email'))
                    ->collapsible(),
                Tables\Grouping\Group::make('phone')
                    ->label(__('security::filament/resources/company.table.groups.phone'))
                    ->collapsible(),
                Tables\Grouping\Group::make('currency_id')
                    ->label(__('security::filament/resources/company.table.groups.currency'))
                    ->collapsible(),
                Tables\Grouping\Group::make('created_at')
                    ->label(__('security::filament/resources/company.table.groups.created-at'))
                    ->collapsible(),
                Tables\Grouping\Group::make('updated_at')
                    ->label(__('security::filament/resources/company.table.groups.updated-at'))
                    ->date()
                    ->collapsible(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
                Tables\Filters\SelectFilter::make('is_active')
                    ->label(__('security::filament/resources/company.table.filters.status'))
                    ->options(CompanyStatus::options()),
                Tables\Filters\SelectFilter::make('country')
                    ->label(__('security::filament/resources/company.table.filters.country'))
                    ->multiple()
                    ->options(function () {
                        return Country::pluck('name', 'name');
                    }),
            ])
            ->filtersFormColumns(2)
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title((__('security::filament/resources/company.table.actions.edit.notification.title')))
                                ->body(__('security::filament/resources/company.table.actions.edit.notification.body')),
                        ),
                    Tables\Actions\DeleteAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title((__('security::filament/resources/company.table.actions.delete.notification.title')))
                                ->body(__('security::filament/resources/company.table.actions.delete.notification.body')),
                        ),
                    Tables\Actions\RestoreAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title((__('security::filament/resources/company.table.actions.restore.notification.title')))
                                ->body(__('security::filament/resources/company.table.actions.restore.notification.body')),
                        ),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title((__('security::filament/resources/company.table.bulk-actions.delete.notification.title')))
                                ->body(__('security::filament/resources/company.table.bulk-actions.delete.notification.body')),
                        ),
                    Tables\Actions\ForceDeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title((__('security::filament/resources/company.table.bulk-actions.force-delete.notification.title')))
                                ->body(__('security::filament/resources/company.table.bulk-actions.force-delete.notification.body')),
                        ),
                    Tables\Actions\RestoreBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title((__('security::filament/resources/company.table.bulk-actions.restore.notification.title')))
                                ->body(__('security::filament/resources/company.table.bulk-actions.restore.notification.body')),
                        ),
                ]),
            ])->modifyQueryUsing(function (Builder $query) {
                $query
                    ->where('creator_id', Auth::user()->id)
                    ->whereNull('parent_id');
            })
            ->reorderable('sequence');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Grid::make(['default' => 3])
                    ->schema([
                        Infolists\Components\Group::make()
                            ->schema([
                                Infolists\Components\Section::make(__('security::filament/resources/company.infolist.sections.company-information.title'))
                                    ->schema([
                                        Infolists\Components\TextEntry::make('name')
                                            ->icon('heroicon-o-building-office')
                                            ->placeholder('—')
                                            ->label(__('security::filament/resources/company.infolist.sections.company-information.entries.name')),
                                        Infolists\Components\TextEntry::make('registration_number')
                                            ->icon('heroicon-o-document-text')
                                            ->placeholder('—')
                                            ->label(__('security::filament/resources/company.infolist.sections.company-information.entries.registration-number')),
                                        Infolists\Components\TextEntry::make('company_id')
                                            ->icon('heroicon-o-identification')
                                            ->placeholder('—')
                                            ->label(__('security::filament/resources/company.infolist.sections.company-information.entries.company-id')),
                                        Infolists\Components\TextEntry::make('tax_id')
                                            ->icon('heroicon-o-currency-dollar')
                                            ->placeholder('—')
                                            ->label(__('security::filament/resources/company.infolist.sections.company-information.entries.tax-id')),
                                        Infolists\Components\TextEntry::make('website')
                                            ->icon('heroicon-o-globe-alt')
                                            ->placeholder('—')
                                            ->label(__('security::filament/resources/company.infolist.sections.company-information.entries.website')),
                                    ])
                                    ->columns(2),

                                Infolists\Components\Section::make(__('security::filament/resources/company.infolist.sections.address-information.title'))
                                    ->schema([
                                        Infolists\Components\TextEntry::make('address.street1')
                                            ->icon('heroicon-o-map-pin')
                                            ->placeholder('—')
                                            ->label(__('security::filament/resources/company.infolist.sections.address-information.entries.street1')),
                                        Infolists\Components\TextEntry::make('address.street2')
                                            ->placeholder('—')
                                            ->label(__('security::filament/resources/company.infolist.sections.address-information.entries.street2')),
                                        Infolists\Components\TextEntry::make('address.city')
                                            ->label(__('security::filament/resources/company.infolist.sections.address-information.entries.city'))
                                            ->icon('heroicon-o-building-library')
                                            ->placeholder('—'),
                                        Infolists\Components\TextEntry::make('address.zip')
                                            ->placeholder('—')
                                            ->label(__('security::filament/resources/company.infolist.sections.address-information.entries.zipcode')),
                                        Infolists\Components\TextEntry::make('address.country.name')
                                            ->icon('heroicon-o-globe-alt')
                                            ->placeholder('—')
                                            ->label(__('security::filament/resources/company.infolist.sections.address-information.entries.country')),
                                        Infolists\Components\TextEntry::make('address.state.name')
                                            ->placeholder('—')
                                            ->label(__('security::filament/resources/company.infolist.sections.address-information.entries.state')),
                                    ])
                                    ->columns(2),

                                Infolists\Components\Section::make(__('security::filament/resources/company.infolist.sections.additional-information.title'))
                                    ->schema([
                                        Infolists\Components\TextEntry::make('currency.full_name')
                                            ->icon('heroicon-o-currency-dollar')
                                            ->placeholder('—')
                                            ->label(__('security::filament/resources/company.infolist.sections.additional-information.entries.default-currency')),
                                        Infolists\Components\TextEntry::make('founded_date')
                                            ->icon('heroicon-o-calendar')
                                            ->placeholder('—')
                                            ->date()
                                            ->label(__('security::filament/resources/company.infolist.sections.additional-information.entries.company-foundation-date')),
                                        Infolists\Components\IconEntry::make('is_active')
                                            ->label(__('security::filament/resources/company.infolist.sections.additional-information.entries.status'))
                                            ->boolean(),
                                    ])
                                    ->columns(2),
                            ])
                            ->columnSpan(2),

                        Infolists\Components\Group::make([
                            Infolists\Components\Section::make(__('security::filament/resources/company.infolist.sections.branding.title'))
                                ->schema([
                                    Infolists\Components\ImageEntry::make('partner.avatar')
                                        ->label(__('security::filament/resources/company.infolist.sections.branding.entries.company-logo'))
                                        ->hiddenLabel()
                                        ->circular()
                                        ->placeholder('—'),
                                    Infolists\Components\ColorEntry::make('color')
                                        ->placeholder('—')
                                        ->label(__('security::filament/resources/company.infolist.sections.branding.entries.color')),
                                ]),

                            Infolists\Components\Section::make(__('security::filament/resources/company.infolist.sections.contact-information.title'))
                                ->schema([
                                    Infolists\Components\TextEntry::make('phone')
                                        ->icon('heroicon-o-phone')
                                        ->placeholder('—')
                                        ->label(__('security::filament/resources/company.infolist.sections.contact-information.entries.phone')),
                                    Infolists\Components\TextEntry::make('mobile')
                                        ->icon('heroicon-o-device-phone-mobile')
                                        ->placeholder('—')
                                        ->label(__('security::filament/resources/company.infolist.sections.contact-information.entries.mobile')),
                                    Infolists\Components\TextEntry::make('email')
                                        ->icon('heroicon-o-envelope')
                                        ->placeholder('—')
                                        ->label(__('security::filament/resources/company.infolist.sections.contact-information.entries.email'))
                                        ->copyable()
                                        ->copyMessage('Email address copied')
                                        ->copyMessageDuration(1500),
                                ]),
                        ])->columnSpan(1),
                    ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\BranchesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListCompanies::route('/'),
            'create' => Pages\CreateCompany::route('/create'),
            'view'   => Pages\ViewCompany::route('/{record}'),
            'edit'   => Pages\EditCompany::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
