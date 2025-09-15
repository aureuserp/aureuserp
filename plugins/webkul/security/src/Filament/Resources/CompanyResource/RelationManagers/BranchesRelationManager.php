<?php

namespace Webkul\Security\Filament\Resources\CompanyResource\RelationManagers;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Webkul\Security\Enums\CompanyStatus;
use Webkul\Support\Models\Country;
use Webkul\Support\Models\Currency;

class BranchesRelationManager extends RelationManager
{
    protected static string $relationship = 'branches';

    protected static ?string $recordTitleAttribute = 'name';

    public function form(Schema $schema): Schema
    {
        return $schema
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
                                            ->visibility('public'),
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
            ])
            ->columns(1);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('partner.avatar')
                    ->size(50)
                    ->label(__('security::filament/resources/company/relation-managers/manage-branch.table.columns.logo')),
                TextColumn::make('name')
                    ->label(__('security::filament/resources/company/relation-managers/manage-branch.table.columns.company-name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label(__('security::filament/resources/company/relation-managers/manage-branch.table.columns.email'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('city')
                    ->label(__('security::filament/resources/company/relation-managers/manage-branch.table.columns.city'))
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('country.name')
                    ->label(__('security::filament/resources/company/relation-managers/manage-branch.table.columns.country'))
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('currency.full_name')
                    ->label(__('security::filament/resources/company/relation-managers/manage-branch.table.columns.currency'))
                    ->sortable()
                    ->searchable(),
                IconColumn::make('is_active')
                    ->sortable()
                    ->label(__('security::filament/resources/company/relation-managers/manage-branch.table.columns.status'))
                    ->boolean(),
                TextColumn::make('created_at')
                    ->label(__('security::filament/resources/company/relation-managers/manage-branch.table.columns.created-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('security::filament/resources/company/relation-managers/manage-branch.table.columns.updated-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->columnToggleFormColumns(2)
            ->groups([
                Tables\Grouping\Group::make('name')
                    ->label(__('security::filament/resources/company/relation-managers/manage-branch.table.groups.company-name'))
                    ->collapsible(),
                Tables\Grouping\Group::make('city')
                    ->label(__('security::filament/resources/company/relation-managers/manage-branch.table.groups.city'))
                    ->collapsible(),
                Tables\Grouping\Group::make('country.name')
                    ->label(__('security::filament/resources/company/relation-managers/manage-branch.table.groups.country'))
                    ->collapsible(),
                Tables\Grouping\Group::make('state.name')
                    ->label(__('security::filament/resources/company/relation-managers/manage-branch.table.groups.state'))
                    ->collapsible(),
                Tables\Grouping\Group::make('email')
                    ->label(__('security::filament/resources/company/relation-managers/manage-branch.table.groups.email'))
                    ->collapsible(),
                Tables\Grouping\Group::make('phone')
                    ->label(__('security::filament/resources/company/relation-managers/manage-branch.table.groups.phone'))
                    ->collapsible(),
                Tables\Grouping\Group::make('currency_id')
                    ->label(__('security::filament/resources/company/relation-managers/manage-branch.table.groups.currency'))
                    ->collapsible(),
                Tables\Grouping\Group::make('created_at')
                    ->label(__('security::filament/resources/company/relation-managers/manage-branch.table.groups.created-at'))
                    ->collapsible(),
                Tables\Grouping\Group::make('updated_at')
                    ->label(__('security::filament/resources/company/relation-managers/manage-branch.table.groups.updated-at'))
                    ->date()
                    ->collapsible(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->icon('heroicon-o-plus-circle')
                    ->mutateDataUsing(function ($livewire, array $data): array {
                        $data['user_id'] = Auth::user()->id;

                        $data['parent_id'] = $livewire->ownerRecord->id;

                        return $data;
                    })
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title((__('security::filament/resources/company/relation-managers/manage-branch.table.header-actions.create.notification.title')))
                            ->body(__('security::filament/resources/company/relation-managers/manage-branch.table.header-actions.create.notification.body')),
                    ),
            ])
            ->filters([
                TrashedFilter::make()
                    ->label(__('security::filament/resources/company/relation-managers/manage-branch.table.filters.trashed')),
                SelectFilter::make('is_active')
                    ->label(__('security::filament/resources/company/relation-managers/manage-branch.table.filters.status'))
                    ->options(CompanyStatus::options()),
                SelectFilter::make('country')
                    ->label(__('security::filament/resources/company/relation-managers/manage-branch.table.filters.country'))
                    ->multiple()
                    ->options(function () {
                        return Country::pluck('name', 'name');
                    }),
            ])
            ->filtersFormColumns(2)
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title((__('security::filament/resources/company/relation-managers/manage-branch.table.actions.edit.notification.title')))
                                ->body(__('security::filament/resources/company/relation-managers/manage-branch.table.actions.edit.notification.body')),
                        ),
                    DeleteAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title((__('security::filament/resources/company/relation-managers/manage-branch.table.actions.delete.notification.title')))
                                ->body(__('security::filament/resources/company/relation-managers/manage-branch.table.actions.delete.notification.body')),
                        ),
                    RestoreAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title((__('security::filament/resources/company/relation-managers/manage-branch.table.actions.restore.notification.title')))
                                ->body(__('security::filament/resources/company/relation-managers/manage-branch.table.actions.restore.notification.body')),
                        ),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title((__('security::filament/resources/company/relation-managers/manage-branch.table.bulk-actions.delete.notification.title')))
                                ->body(__('security::filament/resources/company/relation-managers/manage-branch.table.bulk-actions.delete.notification.body')),
                        ),
                    ForceDeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title((__('security::filament/resources/company/relation-managers/manage-branch.table.bulk-actions.force-delete.notification.title')))
                                ->body(__('security::filament/resources/company/relation-managers/manage-branch.table.bulk-actions.force-delete.notification.body')),
                        ),
                    RestoreBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title((__('security::filament/resources/company/relation-managers/manage-branch.table.bulk-actions.restore.notification.title')))
                                ->body(__('security::filament/resources/company/relation-managers/manage-branch.table.bulk-actions.restore.notification.body')),
                        ),
                ]),
            ])
            ->reorderable('sort');
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Branch Information')
                    ->tabs([
                        Tab::make(__('security::filament/resources/company/relation-managers/manage-branch.infolist.tabs.general-information.title'))
                            ->schema([
                                Section::make(__('security::filament/resources/company/relation-managers/manage-branch.infolist.tabs.general-information.sections.branch-information.title'))
                                    ->schema([
                                        TextEntry::make('name')
                                            ->icon('heroicon-o-building-office')
                                            ->placeholder('—')
                                            ->label(__('security::filament/resources/company/relation-managers/manage-branch.infolist.tabs.general-information.sections.branch-information.entries.company-name')),
                                        TextEntry::make('registration_number')
                                            ->icon('heroicon-o-document-text')
                                            ->placeholder('—')
                                            ->label(__('security::filament/resources/company/relation-managers/manage-branch.infolist.tabs.general-information.sections.branch-information.entries.registration-number')),
                                        TextEntry::make('tax_id')
                                            ->icon('heroicon-o-currency-dollar')
                                            ->placeholder('—')
                                            ->label('Tax ID'),
                                        TextEntry::make('color')
                                            ->icon('heroicon-o-swatch')
                                            ->placeholder('—')
                                            ->badge()
                                            ->color(fn ($record) => $record->color ?? 'gray')
                                            ->label(__('security::filament/resources/company/relation-managers/manage-branch.infolist.tabs.general-information.sections.branch-information.entries.color')),
                                    ])
                                    ->columns(2),

                                Section::make(__('security::filament/resources/company/relation-managers/manage-branch.infolist.tabs.general-information.sections.branding.title'))
                                    ->schema([
                                        ImageEntry::make('partner.avatar')
                                            ->hiddenLabel()
                                            ->label(__('security::filament/resources/company/relation-managers/manage-branch.infolist.tabs.general-information.sections.branding.entries.branch-logo'))
                                            ->placeholder('—'),
                                    ]),
                            ]),

                        Tab::make(__('security::filament/resources/company/relation-managers/manage-branch.infolist.tabs.address-information.title'))
                            ->schema([
                                Section::make(__('security::filament/resources/company/relation-managers/manage-branch.infolist.tabs.address-information.sections.address-information.title'))
                                    ->schema([
                                        TextEntry::make('address.street1')
                                            ->icon('heroicon-o-map-pin')
                                            ->placeholder('—')
                                            ->label(__('security::filament/resources/company/relation-managers/manage-branch.infolist.tabs.address-information.sections.address-information.entries.street1')),
                                        TextEntry::make('address.street2')
                                            ->placeholder('—')
                                            ->label(__('security::filament/resources/company/relation-managers/manage-branch.infolist.tabs.address-information.sections.address-information.entries.street2')),
                                        TextEntry::make('address.city')
                                            ->icon('heroicon-o-building-library')
                                            ->placeholder('—')
                                            ->label(__('security::filament/resources/company/relation-managers/manage-branch.infolist.tabs.address-information.sections.address-information.entries.city')),
                                        TextEntry::make('address.zip')
                                            ->placeholder('—')
                                            ->label(__('security::filament/resources/company/relation-managers/manage-branch.infolist.tabs.address-information.sections.address-information.entries.zip-code')),
                                        TextEntry::make('address.country.name')
                                            ->icon('heroicon-o-globe-alt')
                                            ->placeholder('—')
                                            ->label(__('security::filament/resources/company/relation-managers/manage-branch.infolist.tabs.address-information.sections.address-information.entries.country')),
                                        TextEntry::make('address.state.name')
                                            ->placeholder('—')
                                            ->label(__('security::filament/resources/company/relation-managers/manage-branch.infolist.tabs.address-information.sections.address-information.entries.state')),
                                    ])
                                    ->columns(2),

                                Section::make(__('security::filament/resources/company/relation-managers/manage-branch.infolist.tabs.address-information.sections.additional-information.title'))
                                    ->schema([
                                        TextEntry::make('currency.full_name')
                                            ->icon('heroicon-o-currency-dollar')
                                            ->placeholder('—')
                                            ->label(__('security::filament/resources/company/relation-managers/manage-branch.infolist.tabs.address-information.sections.additional-information.entries.default-currency')),
                                        TextEntry::make('founded_date')
                                            ->icon('heroicon-o-calendar')
                                            ->placeholder('—')
                                            ->date()
                                            ->label(__('security::filament/resources/company/relation-managers/manage-branch.infolist.tabs.address-information.sections.additional-information.entries.company-foundation-date')),
                                        IconEntry::make('is_active')
                                            ->label(__('security::filament/resources/company/relation-managers/manage-branch.infolist.tabs.address-information.sections.additional-information.entries.status'))
                                            ->boolean(),
                                    ])
                                    ->columns(2),
                            ]),

                        Tab::make(__('security::filament/resources/company/relation-managers/manage-branch.infolist.tabs.contact-information.title'))
                            ->schema([
                                Section::make(__('security::filament/resources/company/relation-managers/manage-branch.infolist.tabs.contact-information.sections.contact-information.title'))
                                    ->schema([
                                        TextEntry::make('phone')
                                            ->icon('heroicon-o-phone')
                                            ->placeholder('—')
                                            ->label(__('security::filament/resources/company/relation-managers/manage-branch.infolist.tabs.contact-information.sections.contact-information.entries.phone-number')),
                                        TextEntry::make('mobile')
                                            ->icon('heroicon-o-device-phone-mobile')
                                            ->placeholder('—')
                                            ->label(__('security::filament/resources/company/relation-managers/manage-branch.infolist.tabs.contact-information.sections.contact-information.entries.mobile-number')),
                                        TextEntry::make('email')
                                            ->icon('heroicon-o-envelope')
                                            ->placeholder('—')
                                            ->copyable()
                                            ->copyMessage('Email copied')
                                            ->copyMessageDuration(1500)
                                            ->label(__('security::filament/resources/company/relation-managers/manage-branch.infolist.tabs.contact-information.sections.contact-information.entries.email-address')),
                                    ])
                                    ->columns(2),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
