<?php

namespace Webkul\Account\Filament\Resources;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Webkul\Account\Enums\CommunicationStandard;
use Webkul\Account\Enums\CommunicationType;
use Webkul\Account\Enums\JournalType;
use Webkul\Account\Filament\Resources\JournalResource\Pages;
use Webkul\Account\Models\Journal;

class JournalResource extends Resource
{
    protected static ?string $model = Journal::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Group::make()
                            ->schema([
                                Forms\Components\Tabs::make()
                                    ->tabs([
                                        Forms\Components\Tabs\Tab::make(__('accounts::filament/resources/journal.form.tabs.journal-entries.title'))
                                            ->schema([
                                                Forms\Components\Fieldset::make(__('accounts::filament/resources/journal.form.tabs.journal-entries.field-set.accounting-information.title'))
                                                    ->schema([
                                                        Forms\Components\Group::make()
                                                            ->schema([
                                                                Forms\Components\Toggle::make('refund_order')
                                                                    ->hidden(function (Get $get) {
                                                                        return ! in_array($get('type'), [JournalType::SALE->value, JournalType::PURCHASE->value]);
                                                                    })
                                                                    ->label(__('accounts::filament/resources/journal.form.tabs.journal-entries.field-set.accounting-information.fields.dedicated-credit-note-sequence')),
                                                                Forms\Components\Toggle::make('payment_order')
                                                                    ->hidden(function (Get $get) {
                                                                        return ! in_array($get('type'), [JournalType::BANK->value, JournalType::CASH->value, JournalType::CREDIT_CARD->value]);
                                                                    })
                                                                    ->label(__('accounts::filament/resources/journal.form.tabs.journal-entries.field-set.accounting-information.fields.dedicated-payment-sequence')),
                                                                Forms\Components\TextInput::make('code')
                                                                    ->label(__('accounts::filament/resources/journal.form.tabs.journal-entries.field-set.accounting-information.fields.sort-code'))
                                                                    ->placeholder(__('accounts::filament/resources/journal.form.tabs.journal-entries.field-set.accounting-information.fields.sort-code-placeholder')),
                                                                Forms\Components\Select::make('currency_id')
                                                                    ->label(__('accounts::filament/resources/journal.form.tabs.journal-entries.field-set.accounting-information.fields.currency'))
                                                                    ->relationship('currency', 'name')
                                                                    ->preload()
                                                                    ->searchable(),
                                                                Forms\Components\ColorPicker::make('color')
                                                                    ->label(__('accounts::filament/resources/journal.form.tabs.journal-entries.field-set.accounting-information.fields.color'))
                                                                    ->hexColor(),
                                                            ]),
                                                    ]),
                                                Forms\Components\Fieldset::make(__('accounts::filament/resources/journal.form.tabs.journal-entries.field-set.bank-account-number.title'))
                                                    ->visible(function (Get $get) {
                                                        return $get('type') === JournalType::BANK->value;
                                                    })
                                                    ->schema([
                                                        Forms\Components\Group::make()
                                                            ->schema([
                                                                Forms\Components\Select::make('bank_account_id')
                                                                    ->searchable()
                                                                    ->preload()
                                                                    ->relationship('bankAccount', 'account_number')
                                                                    ->hiddenLabel(),
                                                            ]),
                                                    ]),
                                            ]),
                                        Forms\Components\Tabs\Tab::make(__('accounts::filament/resources/journal.form.tabs.incoming-payments.title'))
                                            ->visible(function (Get $get) {
                                                return in_array($get('type'), [
                                                    JournalType::BANK->value,
                                                    JournalType::CASH->value,
                                                    JournalType::BANK->value,
                                                    JournalType::CREDIT_CARD->value,
                                                ]);
                                            })
                                            ->schema([
                                                Forms\Components\Textarea::make('relation_notes')
                                                    ->label(__('accounts::filament/resources/journal.form.tabs.incoming-payments.fields.relation-notes'))
                                                    ->placeholder(__('accounts::filament/resources/journal.form.tabs.incoming-payments.fields.relation-notes-placeholder')),
                                            ]),
                                        Forms\Components\Tabs\Tab::make(__('accounts::filament/resources/journal.form.tabs.outgoing-payments.title'))
                                            ->visible(function (Get $get) {
                                                return in_array($get('type'), [
                                                    JournalType::BANK->value,
                                                    JournalType::CASH->value,
                                                    JournalType::BANK->value,
                                                    JournalType::CREDIT_CARD->value,
                                                ]);
                                            })
                                            ->schema([
                                                Forms\Components\Textarea::make('relation_notes')
                                                    ->label('Relation Notes')
                                                    ->label(__('accounts::filament/resources/journal.form.tabs.outgoing-payments.fields.relation-notes'))
                                                    ->label(__('accounts::filament/resources/journal.form.tabs.outgoing-payments.fields.relation-notes-placeholder')),
                                            ]),
                                        Forms\Components\Tabs\Tab::make(__('accounts::filament/resources/journal.form.tabs.advanced-settings.title'))
                                            ->schema([
                                                Forms\Components\Fieldset::make(__('accounts::filament/resources/journal.form.tabs.advanced-settings.fields.control-access'))
                                                    ->schema([
                                                        Forms\Components\Group::make()
                                                            ->schema([
                                                                Forms\Components\Select::make('invoices_journal_accounts')
                                                                    ->relationship('allowedAccounts', 'name')
                                                                    ->multiple()
                                                                    ->preload()
                                                                    ->label(__('accounts::filament/resources/journal.form.tabs.advanced-settings.fields.allowed-accounts')),
                                                                Forms\Components\Toggle::make('auto_check_on_post')
                                                                    ->label(__('accounts::filament/resources/journal.form.tabs.advanced-settings.fields.auto-check-on-post')),
                                                            ]),
                                                    ]),
                                                Forms\Components\Fieldset::make(__('accounts::filament/resources/journal.form.tabs.advanced-settings.fields.payment-communication'))
                                                    ->visible(fn (Get $get) => $get('type') === JournalType::SALE->value)
                                                    ->schema([
                                                        Forms\Components\Select::make('invoice_reference_type')
                                                            ->options(CommunicationType::options())
                                                            ->label(__('accounts::filament/resources/journal.form.tabs.advanced-settings.fields.communication-type')),
                                                        Forms\Components\Select::make('invoice_reference_model')
                                                            ->options(CommunicationStandard::options())
                                                            ->label(__('accounts::filament/resources/journal.form.tabs.advanced-settings.fields.communication-standard')),
                                                    ]),
                                            ]),
                                    ])
                                    ->persistTabInQueryString(),
                            ])
                            ->columnSpan(['lg' => 2]),
                        Forms\Components\Group::make()
                            ->schema([
                                Forms\Components\Section::make(__('accounts::filament/resources/journal.form.general.title'))
                                    ->schema([
                                        Forms\Components\Group::make()
                                            ->schema([
                                                Forms\Components\TextInput::make('name')
                                                    ->label(__('accounts::filament/resources/journal.form.general.fields.name'))
                                                    ->required(),
                                                Forms\Components\Select::make('type')
                                                    ->label(__('accounts::filament/resources/journal.form.general.fields.type'))
                                                    ->options(JournalType::options())
                                                    ->required()
                                                    ->live(),
                                                Forms\Components\Select::make('company_id')
                                                    ->label(__('accounts::filament/resources/journal.form.general.fields.company'))
                                                    ->disabled()
                                                    ->relationship('company', 'name')
                                                    ->default(Auth::user()->default_company_id)
                                                    ->required(),
                                            ]),
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
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->label(__('accounts::filament/resources/journal.table.columns.name')),
                Tables\Columns\TextColumn::make('type')
                    ->searchable()
                    ->formatStateUsing(fn ($state) => JournalType::options()[$state] ?? $state)
                    ->sortable()
                    ->label(__('accounts::filament/resources/journal.table.columns.type')),
                Tables\Columns\TextColumn::make('code')
                    ->searchable()
                    ->sortable()
                    ->label(__('accounts::filament/resources/journal.table.columns.code')),
                Tables\Columns\TextColumn::make('currency.name')
                    ->searchable()
                    ->sortable()
                    ->label(__('accounts::filament/resources/journal.table.columns.currency')),
                Tables\Columns\TextColumn::make('createdBy.name')
                    ->searchable()
                    ->sortable()
                    ->label(__('accounts::filament/resources/journal.table.columns.created-by')),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->title(__('accounts::filament/resources/journal.table.actions.delete.notification.title'))
                            ->body(__('accounts::filament/resources/journal.table.actions.delete.notification.body'))
                    ),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->title(__('accounts::filament/resources/journal.table.bulk-actions.delete.notification.title'))
                                ->body(__('accounts::filament/resources/journal.table.bulk-actions.delete.notification.body'))
                        ),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Grid::make(['default' => 3])
                    ->schema([
                        Infolists\Components\Group::make()
                            ->schema([
                                Infolists\Components\Tabs::make('Journal Information')
                                    ->tabs([
                                        Infolists\Components\Tabs\Tab::make(__('accounts::filament/resources/journal.infolist.tabs.journal-entries.title'))
                                            ->schema([
                                                Infolists\Components\Fieldset::make(__('accounts::filament/resources/journal.infolist.tabs.journal-entries.field-set.accounting-information.title'))
                                                    ->schema([
                                                        Infolists\Components\IconEntry::make('refund_order')
                                                            ->boolean()
                                                            ->visible(fn ($record) => in_array($record->type, [JournalType::SALE->value, JournalType::PURCHASE->value]))
                                                            ->placeholder('-')
                                                            ->label(__('accounts::filament/resources/journal.infolist.tabs.journal-entries.field-set.accounting-information.entries.dedicated-credit-note-sequence')),
                                                        Infolists\Components\IconEntry::make('payment_order')
                                                            ->boolean()
                                                            ->placeholder('-')
                                                            ->visible(fn ($record) => in_array($record->type, [JournalType::BANK->value, JournalType::CASH->value, JournalType::CREDIT_CARD->value]))
                                                            ->label(__('accounts::filament/resources/journal.infolist.tabs.journal-entries.field-set.accounting-information.entries.dedicated-payment-sequence')),
                                                        Infolists\Components\TextEntry::make('code')
                                                            ->placeholder('-')
                                                            ->label(__('accounts::filament/resources/journal.infolist.tabs.journal-entries.field-set.accounting-information.entries.sort-code')),
                                                        Infolists\Components\TextEntry::make('currency.name')
                                                            ->placeholder('-')
                                                            ->label(__('accounts::filament/resources/journal.infolist.tabs.journal-entries.field-set.accounting-information.entries.currency')),
                                                        Infolists\Components\ColorEntry::make('color')
                                                            ->placeholder('-')
                                                            ->label(__('accounts::filament/resources/journal.infolist.tabs.journal-entries.field-set.accounting-information.entries.color')),
                                                    ])->columns(2),
                                                Infolists\Components\Section::make(__('accounts::filament/resources/journal.infolist.tabs.journal-entries.field-set.bank-account.title'))
                                                    ->visible(fn ($record) => $record->type === JournalType::BANK->value)
                                                    ->schema([
                                                        Infolists\Components\TextEntry::make('bankAccount.account_number')
                                                            ->placeholder('-')
                                                            ->label(__('accounts::filament/resources/journal.infolist.tabs.journal-entries.field-set.bank-account.entries.account-number')),
                                                    ]),
                                            ]),
                                        Infolists\Components\Tabs\Tab::make(__('accounts::filament/resources/journal.infolist.tabs.incoming-payments.title'))
                                            ->visible(fn ($record) => in_array($record->type, [JournalType::BANK->value, JournalType::CASH->value, JournalType::CREDIT_CARD->value]))
                                            ->schema([
                                                Infolists\Components\TextEntry::make('relation_notes')
                                                    ->placeholder('-')
                                                    ->label(__('accounts::filament/resources/journal.infolist.tabs.incoming-payments.entries.relation-notes'))
                                                    ->markdown(),
                                            ]),
                                        Infolists\Components\Tabs\Tab::make(__('accounts::filament/resources/journal.infolist.tabs.outgoing-payments.title'))
                                            ->visible(fn ($record) => in_array($record->type, [JournalType::BANK->value, JournalType::CASH->value, JournalType::CREDIT_CARD->value]))
                                            ->schema([
                                                Infolists\Components\TextEntry::make('relation_notes')
                                                    ->placeholder('-')
                                                    ->label(__('accounts::filament/resources/journal.infolist.tabs.outgoing-payments.entries.relation-notes'))
                                                    ->markdown(),
                                            ]),
                                        Infolists\Components\Tabs\Tab::make(__('accounts::filament/resources/journal.infolist.tabs.advanced-settings.title'))
                                            ->schema([
                                                Infolists\Components\Fieldset::make(__('accounts::filament/resources/journal.infolist.tabs.advanced-settings.title'))
                                                    ->schema([
                                                        Infolists\Components\TextEntry::make('allowedAccounts.name')
                                                            ->placeholder('-')
                                                            ->listWithLineBreaks()
                                                            ->label(__('accounts::filament/resources/journal.infolist.tabs.advanced-settings.entries.allowed-accounts')),
                                                        Infolists\Components\IconEntry::make('auto_check_on_post')
                                                            ->boolean()
                                                            ->placeholder('-')
                                                            ->label(__('accounts::filament/resources/journal.infolist.tabs.advanced-settings.entries.auto-check-on-post')),
                                                    ]),
                                                Infolists\Components\Fieldset::make(__('accounts::filament/resources/journal.infolist.tabs.advanced-settings.payment-communication.title'))
                                                    ->visible(fn ($record) => $record->type === JournalType::SALE->value)
                                                    ->schema([
                                                        Infolists\Components\TextEntry::make('invoice_reference_type')
                                                            ->placeholder('-')
                                                            ->label(__('accounts::filament/resources/journal.infolist.tabs.advanced-settings.payment-communication.entries.communication-type')),
                                                        Infolists\Components\TextEntry::make('invoice_reference_model')
                                                            ->placeholder('-')
                                                            ->label(__('accounts::filament/resources/journal.infolist.tabs.advanced-settings.payment-communication.entries.communication-standard')),
                                                    ]),
                                            ]),
                                    ]),
                            ])->columnSpan(2),
                        Infolists\Components\Group::make()
                            ->schema([
                                Infolists\Components\Section::make(__('accounts::filament/resources/journal.infolist.general.title'))
                                    ->schema([
                                        Infolists\Components\TextEntry::make('name')
                                            ->placeholder('-')
                                            ->label(__('accounts::filament/resources/journal.infolist.general.entries.name'))
                                            ->icon('heroicon-o-document-text'),
                                        Infolists\Components\TextEntry::make('type')
                                            ->placeholder('-')
                                            ->label(__('accounts::filament/resources/journal.infolist.general.entries.type'))
                                            ->icon('heroicon-o-tag'),
                                        Infolists\Components\TextEntry::make('company.name')
                                            ->placeholder('-')
                                            ->label(__('accounts::filament/resources/journal.infolist.general.entries.company'))
                                            ->icon('heroicon-o-building-office'),
                                    ]),
                            ])->columnSpan(1),
                    ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListJournals::route('/'),
            'create' => Pages\CreateJournal::route('/create'),
            'view'   => Pages\ViewJournal::route('/{record}'),
            'edit'   => Pages\EditJournal::route('/{record}/edit'),
        ];
    }
}
