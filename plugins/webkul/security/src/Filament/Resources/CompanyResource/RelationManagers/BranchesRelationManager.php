<?php

namespace Webkul\Security\Filament\Resources\CompanyResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
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

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make()
                    ->tabs([
                        Forms\Components\Tabs\Tab::make(__('security::filament/resources/company/relation-managers/manage-branch.form.tabs.general-information.title'))
                            ->schema([
                                Forms\Components\Section::make(__('security::filament/resources/company/relation-managers/manage-branch.form.tabs.general-information.sections.branch-information.title'))
                                    ->schema([
                                        Forms\Components\TextInput::make('name')
                                            ->label(__('security::filament/resources/company/relation-managers/manage-branch.form.tabs.general-information.sections.branch-information.fields.company-name'))
                                            ->required()
                                            ->maxLength(255)
                                            ->live(onBlur: true),
                                        Forms\Components\TextInput::make('registration_number')
                                            ->label(__('security::filament/resources/company/relation-managers/manage-branch.form.tabs.general-information.sections.branch-information.fields.registration-number')),
                                        Forms\Components\TextInput::make('company_id')
                                            ->label(__('security::filament/resources/company/relation-managers/manage-branch.form.tabs.general-information.sections.branch-information.fields.company-id'))
                                            ->unique(ignoreRecord: true)
                                            ->hintIcon('heroicon-o-question-mark-circle', tooltip: __('security::filament/resources/company/relation-managers/manage-branch.form.tabs.general-information.sections.branch-information.fields.company-id-tooltip')),
                                        Forms\Components\TextInput::make('tax_id')
                                            ->label(__('security::filament/resources/company/relation-managers/manage-branch.form.tabs.general-information.sections.branch-information.fields.tax-id'))
                                            ->unique(ignoreRecord: true)
                                            ->hintIcon('heroicon-o-question-mark-circle', tooltip: __('security::filament/resources/company/relation-managers/manage-branch.form.tabs.general-information.sections.branch-information.fields.tax-id-tooltip')),
                                        Forms\Components\ColorPicker::make('color')
                                            ->label(__('security::filament/resources/company/relation-managers/manage-branch.form.tabs.general-information.sections.branch-information.fields.color'))
                                            ->hexColor(),
                                    ])
                                    ->columns(2),
                                Forms\Components\Section::make(__('security::filament/resources/company/relation-managers/manage-branch.form.tabs.general-information.sections.branding.title'))
                                    ->relationship('partner', 'avatar')
                                    ->schema([
                                        Forms\Components\FileUpload::make('avatar')
                                            ->label(__('security::filament/resources/company/relation-managers/manage-branch.form.tabs.general-information.sections.branding.fields.branch-logo'))
                                            ->image()
                                            ->directory('company-logos')
                                            ->visibility('private'),
                                    ]),
                            ])
                            ->columnSpanFull(),
                        Forms\Components\Tabs\Tab::make(__('security::filament/resources/company/relation-managers/manage-branch.form.tabs.address-information.title'))
                            ->schema([
                                Forms\Components\Section::make(__('security::filament/resources/company/relation-managers/manage-branch.form.tabs.address-information.sections.address-information.title'))
                                    ->schema([
                                        Forms\Components\Group::make()
                                            ->schema([
                                                Forms\Components\TextInput::make('street1')
                                                    ->label(__('security::filament/resources/company/relation-managers/manage-branch.form.tabs.address-information.sections.address-information.fields.street1')),
                                                Forms\Components\TextInput::make('street2')
                                                    ->label(__('security::filament/resources/company/relation-managers/manage-branch.form.tabs.address-information.sections.address-information.fields.street2')),
                                                Forms\Components\TextInput::make('city')
                                                    ->label(__('security::filament/resources/company/relation-managers/manage-branch.form.tabs.address-information.sections.address-information.fields.city')),
                                                Forms\Components\TextInput::make('zip')
                                                    ->live()
                                                    ->label(__('security::filament/resources/company/relation-managers/manage-branch.form.tabs.address-information.sections.address-information.fields.zip-code')),
                                                Forms\Components\Select::make('country_id')
                                                    ->label(__('security::filament/resources/company/relation-managers/manage-branch.form.tabs.address-information.sections.address-information.fields.country'))
                                                    ->relationship(name: 'country', titleAttribute: 'name')
                                                    ->afterStateUpdated(fn (Set $set) => $set('state_id', null))
                                                    ->searchable()
                                                    ->preload()
                                                    ->live(),
                                                Forms\Components\Select::make('state_id')
                                                    ->label(__('security::filament/resources/company/relation-managers/manage-branch.form.tabs.address-information.sections.address-information.fields.state'))
                                                    ->relationship(
                                                        name: 'state',
                                                        titleAttribute: 'name',
                                                        modifyQueryUsing: fn (Forms\Get $get, Builder $query) => $query->where('country_id', $get('country_id')),
                                                    )
                                                    ->searchable()
                                                    ->preload()
                                                    ->createOptionForm(function (Form $form, Forms\Get $get, Forms\Set $set) {
                                                        return $form
                                                            ->schema([
                                                                Forms\Components\TextInput::make('name')
                                                                    ->label(__('security::filament/resources/company/relation-managers/manage-branch.form.tabs.address-information.sections.address-information.fields.state-name'))
                                                                    ->required(),
                                                                Forms\Components\TextInput::make('code')
                                                                    ->label(__('security::filament/resources/company/relation-managers/manage-branch.form.tabs.address-information.sections.address-information.fields.state-code'))
                                                                    ->required()
                                                                    ->unique('states'),
                                                                Forms\Components\Select::make('country_id')
                                                                    ->label(__('security::filament/resources/company/relation-managers/manage-branch.form.tabs.address-information.sections.address-information.fields.country'))
                                                                    ->relationship('country', 'name')
                                                                    ->searchable()
                                                                    ->preload()
                                                                    ->live()
                                                                    ->default($get('country_id'))
                                                                    ->afterStateUpdated(function (Forms\Get $get) use ($set) {
                                                                        $set('country_id', $get('country_id'));
                                                                    }),
                                                            ]);
                                                    }),
                                            ])
                                            ->columns(2),
                                    ]),
                                Forms\Components\Section::make(__('security::filament/resources/company/relation-managers/manage-branch.form.tabs.address-information.sections.additional-information.title'))
                                    ->schema([
                                        Forms\Components\Select::make('currency_id')
                                            ->relationship('currency', 'full_name')
                                            ->label(__('security::filament/resources/company/relation-managers/manage-branch.form.tabs.address-information.sections.additional-information.fields.default-currency'))
                                            ->relationship('currency', 'full_name')
                                            ->searchable()
                                            ->required()
                                            ->live()
                                            ->preload()
                                            ->default(Currency::first()?->id)
                                            ->createOptionForm([
                                                Forms\Components\Section::make()
                                                    ->schema([
                                                        Forms\Components\TextInput::make('name')
                                                            ->label(__('security::filament/resources/company/relation-managers/manage-branch.form.tabs.address-information.sections.additional-information.fields.currency-name'))
                                                            ->required()
                                                            ->maxLength(255)
                                                            ->unique('currencies', 'name', ignoreRecord: true),
                                                        Forms\Components\TextInput::make('full_name')
                                                            ->label(__('security::filament/resources/company/relation-managers/manage-branch.form.tabs.address-information.sections.additional-information.fields.currency-full-name'))
                                                            ->required()
                                                            ->maxLength(255)
                                                            ->unique('currencies', 'full_name', ignoreRecord: true),
                                                        Forms\Components\TextInput::make('symbol')
                                                            ->label(__('security::filament/resources/company/relation-managers/manage-branch.form.tabs.address-information.sections.additional-information.fields.currency-symbol'))
                                                            ->required(),
                                                        Forms\Components\TextInput::make('iso_numeric')
                                                            ->label(__('security::filament/resources/company/relation-managers/manage-branch.form.tabs.address-information.sections.additional-information.fields.currency-iso-numeric'))
                                                            ->numeric()
                                                            ->required(),
                                                        Forms\Components\TextInput::make('decimal_places')
                                                            ->numeric()
                                                            ->label(__('security::filament/resources/company/relation-managers/manage-branch.form.tabs.address-information.sections.additional-information.fields.currency-decimal-places'))
                                                            ->required()
                                                            ->rules('min:0', 'max:10'),
                                                        Forms\Components\TextInput::make('rounding')
                                                            ->numeric()
                                                            ->label(__('security::filament/resources/company/relation-managers/manage-branch.form.tabs.address-information.sections.additional-information.fields.currency-rounding'))
                                                            ->required(),
                                                        Forms\Components\Toggle::make('active')
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
                                        Forms\Components\DatePicker::make('founded_date')
                                            ->native(false)
                                            ->label(__('security::filament/resources/company/relation-managers/manage-branch.form.tabs.address-information.sections.additional-information.fields.company-foundation-date')),
                                        Forms\Components\Toggle::make('is_active')
                                            ->label(__('security::filament/resources/company/relation-managers/manage-branch.form.tabs.address-information.sections.additional-information.fields.status'))
                                            ->default(true),
                                    ]),
                            ])
                            ->columnSpanFull(),
                        Forms\Components\Tabs\Tab::make(__('security::filament/resources/company/relation-managers/manage-branch.form.tabs.contact-information.title'))
                            ->schema([
                                Forms\Components\Section::make(__('security::filament/resources/company/relation-managers/manage-branch.form.tabs.contact-information.sections.contact-information.title'))
                                    ->schema([
                                        Forms\Components\TextInput::make('phone')
                                            ->label(__('security::filament/resources/company/relation-managers/manage-branch.form.tabs.contact-information.sections.contact-information.fields.phone-number'))
                                            ->tel(),
                                        Forms\Components\TextInput::make('mobile')
                                            ->label(__('security::filament/resources/company/relation-managers/manage-branch.form.tabs.contact-information.sections.contact-information.fields.mobile-number'))
                                            ->tel(),
                                        Forms\Components\TextInput::make('email')
                                            ->label(__('security::filament/resources/company/relation-managers/manage-branch.form.tabs.contact-information.sections.contact-information.fields.email-address'))
                                            ->email(),
                                    ])
                                    ->columns(2),
                            ])
                            ->columnSpanFull(),
                    ]),
            ])
            ->columns('full');
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('partner.avatar')
                    ->size(50)
                    ->label(__('security::filament/resources/company/relation-managers/manage-branch.table.columns.logo')),
                Tables\Columns\TextColumn::make('name')
                    ->label(__('security::filament/resources/company/relation-managers/manage-branch.table.columns.company-name'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->label(__('security::filament/resources/company/relation-managers/manage-branch.table.columns.email'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('city')
                    ->label(__('security::filament/resources/company/relation-managers/manage-branch.table.columns.city'))
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('country.name')
                    ->label(__('security::filament/resources/company/relation-managers/manage-branch.table.columns.country'))
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('currency.full_name')
                    ->label(__('security::filament/resources/company/relation-managers/manage-branch.table.columns.currency'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->sortable()
                    ->label(__('security::filament/resources/company/relation-managers/manage-branch.table.columns.status'))
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('security::filament/resources/company/relation-managers/manage-branch.table.columns.created-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
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
                Tables\Actions\CreateAction::make()
                    ->icon('heroicon-o-plus-circle')
                    ->mutateFormDataUsing(function ($livewire, array $data): array {
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
                Tables\Filters\TrashedFilter::make()
                    ->label(__('security::filament/resources/company/relation-managers/manage-branch.table.filters.trashed')),
                Tables\Filters\SelectFilter::make('is_active')
                    ->label(__('security::filament/resources/company/relation-managers/manage-branch.table.filters.status'))
                    ->options(CompanyStatus::options()),
                Tables\Filters\SelectFilter::make('country')
                    ->label(__('security::filament/resources/company/relation-managers/manage-branch.table.filters.country'))
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
                                ->title((__('security::filament/resources/company/relation-managers/manage-branch.table.actions.edit.notification.title')))
                                ->body(__('security::filament/resources/company/relation-managers/manage-branch.table.actions.edit.notification.body')),
                        ),
                    Tables\Actions\DeleteAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title((__('security::filament/resources/company/relation-managers/manage-branch.table.actions.delete.notification.title')))
                                ->body(__('security::filament/resources/company/relation-managers/manage-branch.table.actions.delete.notification.body')),
                        ),
                    Tables\Actions\RestoreAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title((__('security::filament/resources/company/relation-managers/manage-branch.table.actions.restore.notification.title')))
                                ->body(__('security::filament/resources/company/relation-managers/manage-branch.table.actions.restore.notification.body')),
                        ),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title((__('security::filament/resources/company/relation-managers/manage-branch.table.bulk-actions.delete.notification.title')))
                                ->body(__('security::filament/resources/company/relation-managers/manage-branch.table.bulk-actions.delete.notification.body')),
                        ),
                    Tables\Actions\ForceDeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title((__('security::filament/resources/company/relation-managers/manage-branch.table.bulk-actions.force-delete.notification.title')))
                                ->body(__('security::filament/resources/company/relation-managers/manage-branch.table.bulk-actions.force-delete.notification.body')),
                        ),
                    Tables\Actions\RestoreBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title((__('security::filament/resources/company/relation-managers/manage-branch.table.bulk-actions.restore.notification.title')))
                                ->body(__('security::filament/resources/company/relation-managers/manage-branch.table.bulk-actions.restore.notification.body')),
                        ),
                ]),
            ])
            ->reorderable('sequence');
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Tabs::make('Branch Information')
                    ->tabs([
                        Infolists\Components\Tabs\Tab::make(__('security::filament/resources/company/relation-managers/manage-branch.infolist.tabs.general-information.title'))
                            ->schema([
                                Infolists\Components\Section::make(__('security::filament/resources/company/relation-managers/manage-branch.infolist.tabs.general-information.sections.branch-information.title'))
                                    ->schema([
                                        Infolists\Components\TextEntry::make('name')
                                            ->icon('heroicon-o-building-office')
                                            ->placeholder('—')
                                            ->label(__('security::filament/resources/company/relation-managers/manage-branch.infolist.tabs.general-information.sections.branch-information.entries.company-name')),
                                        Infolists\Components\TextEntry::make('registration_number')
                                            ->icon('heroicon-o-document-text')
                                            ->placeholder('—')
                                            ->label(__('security::filament/resources/company/relation-managers/manage-branch.infolist.tabs.general-information.sections.branch-information.entries.registration-number')),
                                        Infolists\Components\TextEntry::make('tax_id')
                                            ->icon('heroicon-o-currency-dollar')
                                            ->placeholder('—')
                                            ->label('Tax ID'),
                                        Infolists\Components\TextEntry::make('color')
                                            ->icon('heroicon-o-swatch')
                                            ->placeholder('—')
                                            ->badge()
                                            ->color(fn ($record) => $record->color ?? 'gray')
                                            ->label(__('security::filament/resources/company/relation-managers/manage-branch.infolist.tabs.general-information.sections.branch-information.entries.color')),
                                    ])
                                    ->columns(2),

                                Infolists\Components\Section::make(__('security::filament/resources/company/relation-managers/manage-branch.infolist.tabs.general-information.sections.branding.title'))
                                    ->schema([
                                        Infolists\Components\ImageEntry::make('partner.avatar')
                                            ->hiddenLabel()
                                            ->label(__('security::filament/resources/company/relation-managers/manage-branch.infolist.tabs.general-information.sections.branding.entries.branch-logo'))
                                            ->placeholder('—'),
                                    ]),
                            ]),

                        Infolists\Components\Tabs\Tab::make(__('security::filament/resources/company/relation-managers/manage-branch.infolist.tabs.address-information.title'))
                            ->schema([
                                Infolists\Components\Section::make(__('security::filament/resources/company/relation-managers/manage-branch.infolist.tabs.address-information.sections.address-information.title'))
                                    ->schema([
                                        Infolists\Components\TextEntry::make('address.street1')
                                            ->icon('heroicon-o-map-pin')
                                            ->placeholder('—')
                                            ->label(__('security::filament/resources/company/relation-managers/manage-branch.infolist.tabs.address-information.sections.address-information.entries.street1')),
                                        Infolists\Components\TextEntry::make('address.street2')
                                            ->placeholder('—')
                                            ->label(__('security::filament/resources/company/relation-managers/manage-branch.infolist.tabs.address-information.sections.address-information.entries.street2')),
                                        Infolists\Components\TextEntry::make('address.city')
                                            ->icon('heroicon-o-building-library')
                                            ->placeholder('—')
                                            ->label(__('security::filament/resources/company/relation-managers/manage-branch.infolist.tabs.address-information.sections.address-information.entries.city')),
                                        Infolists\Components\TextEntry::make('address.zip')
                                            ->placeholder('—')
                                            ->label(__('security::filament/resources/company/relation-managers/manage-branch.infolist.tabs.address-information.sections.address-information.entries.zip-code')),
                                        Infolists\Components\TextEntry::make('address.country.name')
                                            ->icon('heroicon-o-globe-alt')
                                            ->placeholder('—')
                                            ->label(__('security::filament/resources/company/relation-managers/manage-branch.infolist.tabs.address-information.sections.address-information.entries.country')),
                                        Infolists\Components\TextEntry::make('address.state.name')
                                            ->placeholder('—')
                                            ->label(__('security::filament/resources/company/relation-managers/manage-branch.infolist.tabs.address-information.sections.address-information.entries.state')),
                                    ])
                                    ->columns(2),

                                Infolists\Components\Section::make(__('security::filament/resources/company/relation-managers/manage-branch.infolist.tabs.address-information.sections.additional-information.title'))
                                    ->schema([
                                        Infolists\Components\TextEntry::make('currency.full_name')
                                            ->icon('heroicon-o-currency-dollar')
                                            ->placeholder('—')
                                            ->label(__('security::filament/resources/company/relation-managers/manage-branch.infolist.tabs.address-information.sections.additional-information.entries.default-currency')),
                                        Infolists\Components\TextEntry::make('founded_date')
                                            ->icon('heroicon-o-calendar')
                                            ->placeholder('—')
                                            ->date()
                                            ->label(__('security::filament/resources/company/relation-managers/manage-branch.infolist.tabs.address-information.sections.additional-information.entries.company-foundation-date')),
                                        Infolists\Components\IconEntry::make('is_active')
                                            ->label(__('security::filament/resources/company/relation-managers/manage-branch.infolist.tabs.address-information.sections.additional-information.entries.status'))
                                            ->boolean(),
                                    ])
                                    ->columns(2),
                            ]),

                        Infolists\Components\Tabs\Tab::make(__('security::filament/resources/company/relation-managers/manage-branch.infolist.tabs.contact-information.title'))
                            ->schema([
                                Infolists\Components\Section::make(__('security::filament/resources/company/relation-managers/manage-branch.infolist.tabs.contact-information.sections.contact-information.title'))
                                    ->schema([
                                        Infolists\Components\TextEntry::make('phone')
                                            ->icon('heroicon-o-phone')
                                            ->placeholder('—')
                                            ->label(__('security::filament/resources/company/relation-managers/manage-branch.infolist.tabs.contact-information.sections.contact-information.entries.phone-number')),
                                        Infolists\Components\TextEntry::make('mobile')
                                            ->icon('heroicon-o-device-phone-mobile')
                                            ->placeholder('—')
                                            ->label(__('security::filament/resources/company/relation-managers/manage-branch.infolist.tabs.contact-information.sections.contact-information.entries.mobile-number')),
                                        Infolists\Components\TextEntry::make('email')
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
