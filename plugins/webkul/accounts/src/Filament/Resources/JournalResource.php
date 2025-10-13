<?php

namespace Webkul\Account\Filament\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\ColorEntry;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Webkul\Account\Enums\CommunicationStandard;
use Webkul\Account\Enums\CommunicationType;
use Webkul\Account\Enums\JournalType;
use Webkul\Account\Filament\Resources\JournalResource\Pages\CreateJournal;
use Webkul\Account\Filament\Resources\JournalResource\Pages\EditJournal;
use Webkul\Account\Filament\Resources\JournalResource\Pages\ListJournals;
use Webkul\Account\Filament\Resources\JournalResource\Pages\ViewJournal;
use Webkul\Account\Models\Journal;

class JournalResource extends Resource
{
    protected static ?string $model = Journal::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-book-open';

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Group::make()
                            ->schema([
                                Tabs::make()
                                    ->tabs([
                                        Tab::make(__('accounts::filament/resources/journal.form.tabs.journal-entries.title'))
                                            ->schema([
                                                Fieldset::make(__('accounts::filament/resources/journal.form.tabs.journal-entries.field-set.accounting-information.title'))
                                                    ->schema([
                                                        Group::make()
                                                            ->schema([
                                                                Toggle::make('refund_order')
                                                                    ->hidden(function (Get $get) {
                                                                        return ! in_array($get('type'), [JournalType::SALE->value, JournalType::PURCHASE->value]);
                                                                    })
                                                                    ->label(__('accounts::filament/resources/journal.form.tabs.journal-entries.field-set.accounting-information.fields.dedicated-credit-note-sequence')),
                                                                Toggle::make('payment_order')
                                                                    ->hidden(function (Get $get) {
                                                                        return ! in_array($get('type'), [JournalType::BANK->value, JournalType::CASH->value, JournalType::CREDIT_CARD->value]);
                                                                    })
                                                                    ->label(__('accounts::filament/resources/journal.form.tabs.journal-entries.field-set.accounting-information.fields.dedicated-payment-sequence')),
                                                                TextInput::make('code')
                                                                    ->label(__('accounts::filament/resources/journal.form.tabs.journal-entries.field-set.accounting-information.fields.sort-code'))
                                                                    ->placeholder(__('accounts::filament/resources/journal.form.tabs.journal-entries.field-set.accounting-information.fields.sort-code-placeholder')),
                                                                Select::make('currency_id')
                                                                    ->label(__('accounts::filament/resources/journal.form.tabs.journal-entries.field-set.accounting-information.fields.currency'))
                                                                    ->relationship('currency', 'name')
                                                                    ->preload()
                                                                    ->searchable(),
                                                                ColorPicker::make('color')
                                                                    ->label(__('accounts::filament/resources/journal.form.tabs.journal-entries.field-set.accounting-information.fields.color'))
                                                                    ->hexColor(),
                                                            ]),
                                                    ]),
                                                Fieldset::make(__('accounts::filament/resources/journal.form.tabs.journal-entries.field-set.bank-account-number.title'))
                                                    ->visible(function (Get $get) {
                                                        return $get('type') === JournalType::BANK->value;
                                                    })
                                                    ->schema([
                                                        Group::make()
                                                            ->schema([
                                                                Select::make('bank_account_id')
                                                                    ->searchable()
                                                                    ->preload()
                                                                    ->relationship('bankAccount', 'account_number')
                                                                    ->hiddenLabel(),
                                                            ]),
                                                    ]),
                                            ]),
                                        Tab::make(__('accounts::filament/resources/journal.form.tabs.incoming-payments.title'))
                                            ->visible(function (Get $get) {
                                                return in_array($get('type'), [
                                                    JournalType::BANK->value,
                                                    JournalType::CASH->value,
                                                    JournalType::BANK->value,
                                                    JournalType::CREDIT_CARD->value,
                                                ]);
                                            })
                                            ->schema([
                                                Textarea::make('relation_notes')
                                                    ->label(__('accounts::filament/resources/journal.form.tabs.incoming-payments.fields.relation-notes'))
                                                    ->placeholder(__('accounts::filament/resources/journal.form.tabs.incoming-payments.fields.relation-notes-placeholder')),
                                            ]),
                                        Tab::make(__('accounts::filament/resources/journal.form.tabs.outgoing-payments.title'))
                                            ->visible(function (Get $get) {
                                                return in_array($get('type'), [
                                                    JournalType::BANK->value,
                                                    JournalType::CASH->value,
                                                    JournalType::BANK->value,
                                                    JournalType::CREDIT_CARD->value,
                                                ]);
                                            })
                                            ->schema([
                                                Textarea::make('relation_notes')
                                                    ->label('Relation Notes')
                                                    ->label(__('accounts::filament/resources/journal.form.tabs.outgoing-payments.fields.relation-notes'))
                                                    ->label(__('accounts::filament/resources/journal.form.tabs.outgoing-payments.fields.relation-notes-placeholder')),
                                            ]),
                                        Tab::make(__('accounts::filament/resources/journal.form.tabs.advanced-settings.title'))
                                            ->schema([
                                                Fieldset::make(__('accounts::filament/resources/journal.form.tabs.advanced-settings.fields.control-access'))
                                                    ->schema([
                                                        Group::make()
                                                            ->schema([
                                                                Select::make('invoices_journal_accounts')
                                                                    ->relationship('allowedAccounts', 'name')
                                                                    ->multiple()
                                                                    ->preload()
                                                                    ->label(__('accounts::filament/resources/journal.form.tabs.advanced-settings.fields.allowed-accounts')),
                                                                Toggle::make('auto_check_on_post')
                                                                    ->label(__('accounts::filament/resources/journal.form.tabs.advanced-settings.fields.auto-check-on-post')),
                                                            ]),
                                                    ]),
                                                Fieldset::make(__('accounts::filament/resources/journal.form.tabs.advanced-settings.fields.payment-communication'))
                                                    ->visible(fn (Get $get) => $get('type') === JournalType::SALE->value)
                                                    ->schema([
                                                        Select::make('invoice_reference_type')
                                                            ->options(CommunicationType::options())
                                                            ->label(__('accounts::filament/resources/journal.form.tabs.advanced-settings.fields.communication-type')),
                                                        Select::make('invoice_reference_model')
                                                            ->options(CommunicationStandard::options())
                                                            ->label(__('accounts::filament/resources/journal.form.tabs.advanced-settings.fields.communication-standard')),
                                                    ]),
                                            ]),
                                    ])
                                    ->persistTabInQueryString(),
                            ])
                            ->columnSpan(['lg' => 2]),
                        Group::make()
                            ->schema([
                                Section::make(__('accounts::filament/resources/journal.form.general.title'))
                                    ->schema([
                                        Group::make()
                                            ->schema([
                                                TextInput::make('name')
                                                    ->label(__('accounts::filament/resources/journal.form.general.fields.name'))
                                                    ->required(),
                                                Select::make('type')
                                                    ->label(__('accounts::filament/resources/journal.form.general.fields.type'))
                                                    ->options(JournalType::options())
                                                    ->required()
                                                    ->live(),
                                                Select::make('company_id')
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
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->label(__('accounts::filament/resources/journal.table.columns.name')),
                TextColumn::make('type')
                    ->searchable()
                    ->formatStateUsing(fn ($state) => JournalType::options()[$state] ?? $state)
                    ->sortable()
                    ->label(__('accounts::filament/resources/journal.table.columns.type')),
                TextColumn::make('code')
                    ->searchable()
                    ->sortable()
                    ->label(__('accounts::filament/resources/journal.table.columns.code')),
                TextColumn::make('currency.name')
                    ->searchable()
                    ->sortable()
                    ->label(__('accounts::filament/resources/journal.table.columns.currency')),
                TextColumn::make('createdBy.name')
                    ->searchable()
                    ->sortable()
                    ->label(__('accounts::filament/resources/journal.table.columns.created-by')),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->title(__('accounts::filament/resources/journal.table.actions.delete.notification.title'))
                            ->body(__('accounts::filament/resources/journal.table.actions.delete.notification.body'))
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->title(__('accounts::filament/resources/journal.table.bulk-actions.delete.notification.title'))
                                ->body(__('accounts::filament/resources/journal.table.bulk-actions.delete.notification.body'))
                        ),
                ]),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(['default' => 3])
                    ->schema([
                        Group::make()
                            ->schema([
                                Tabs::make('Journal Information')
                                    ->tabs([
                                        Tab::make(__('accounts::filament/resources/journal.infolist.tabs.journal-entries.title'))
                                            ->schema([
                                                Fieldset::make(__('accounts::filament/resources/journal.infolist.tabs.journal-entries.field-set.accounting-information.title'))
                                                    ->schema([
                                                        IconEntry::make('refund_order')
                                                            ->boolean()
                                                            ->visible(fn ($record) => in_array($record->type, [JournalType::SALE->value, JournalType::PURCHASE->value]))
                                                            ->placeholder('-')
                                                            ->label(__('accounts::filament/resources/journal.infolist.tabs.journal-entries.field-set.accounting-information.entries.dedicated-credit-note-sequence')),
                                                        IconEntry::make('payment_order')
                                                            ->boolean()
                                                            ->placeholder('-')
                                                            ->visible(fn ($record) => in_array($record->type, [JournalType::BANK->value, JournalType::CASH->value, JournalType::CREDIT_CARD->value]))
                                                            ->label(__('accounts::filament/resources/journal.infolist.tabs.journal-entries.field-set.accounting-information.entries.dedicated-payment-sequence')),
                                                        TextEntry::make('code')
                                                            ->placeholder('-')
                                                            ->label(__('accounts::filament/resources/journal.infolist.tabs.journal-entries.field-set.accounting-information.entries.sort-code')),
                                                        TextEntry::make('currency.name')
                                                            ->placeholder('-')
                                                            ->label(__('accounts::filament/resources/journal.infolist.tabs.journal-entries.field-set.accounting-information.entries.currency')),
                                                        ColorEntry::make('color')
                                                            ->placeholder('-')
                                                            ->label(__('accounts::filament/resources/journal.infolist.tabs.journal-entries.field-set.accounting-information.entries.color')),
                                                    ])->columns(2),
                                                Section::make(__('accounts::filament/resources/journal.infolist.tabs.journal-entries.field-set.bank-account.title'))
                                                    ->visible(fn ($record) => $record->type === JournalType::BANK->value)
                                                    ->schema([
                                                        TextEntry::make('bankAccount.account_number')
                                                            ->placeholder('-')
                                                            ->label(__('accounts::filament/resources/journal.infolist.tabs.journal-entries.field-set.bank-account.entries.account-number')),
                                                    ]),
                                            ]),
                                        Tab::make(__('accounts::filament/resources/journal.infolist.tabs.incoming-payments.title'))
                                            ->visible(fn ($record) => in_array($record->type, [JournalType::BANK->value, JournalType::CASH->value, JournalType::CREDIT_CARD->value]))
                                            ->schema([
                                                TextEntry::make('relation_notes')
                                                    ->placeholder('-')
                                                    ->label(__('accounts::filament/resources/journal.infolist.tabs.incoming-payments.entries.relation-notes'))
                                                    ->markdown(),
                                            ]),
                                        Tab::make(__('accounts::filament/resources/journal.infolist.tabs.outgoing-payments.title'))
                                            ->visible(fn ($record) => in_array($record->type, [JournalType::BANK->value, JournalType::CASH->value, JournalType::CREDIT_CARD->value]))
                                            ->schema([
                                                TextEntry::make('relation_notes')
                                                    ->placeholder('-')
                                                    ->label(__('accounts::filament/resources/journal.infolist.tabs.outgoing-payments.entries.relation-notes'))
                                                    ->markdown(),
                                            ]),
                                        Tab::make(__('accounts::filament/resources/journal.infolist.tabs.advanced-settings.title'))
                                            ->schema([
                                                Fieldset::make(__('accounts::filament/resources/journal.infolist.tabs.advanced-settings.title'))
                                                    ->schema([
                                                        TextEntry::make('allowedAccounts.name')
                                                            ->placeholder('-')
                                                            ->listWithLineBreaks()
                                                            ->label(__('accounts::filament/resources/journal.infolist.tabs.advanced-settings.entries.allowed-accounts')),
                                                        IconEntry::make('auto_check_on_post')
                                                            ->boolean()
                                                            ->placeholder('-')
                                                            ->label(__('accounts::filament/resources/journal.infolist.tabs.advanced-settings.entries.auto-check-on-post')),
                                                    ]),
                                                Fieldset::make(__('accounts::filament/resources/journal.infolist.tabs.advanced-settings.payment-communication.title'))
                                                    ->visible(fn ($record) => $record->type === JournalType::SALE->value)
                                                    ->schema([
                                                        TextEntry::make('invoice_reference_type')
                                                            ->placeholder('-')
                                                            ->label(__('accounts::filament/resources/journal.infolist.tabs.advanced-settings.payment-communication.entries.communication-type')),
                                                        TextEntry::make('invoice_reference_model')
                                                            ->placeholder('-')
                                                            ->label(__('accounts::filament/resources/journal.infolist.tabs.advanced-settings.payment-communication.entries.communication-standard')),
                                                    ]),
                                            ]),
                                    ]),
                            ])->columnSpan(2),
                        Group::make()
                            ->schema([
                                Section::make(__('accounts::filament/resources/journal.infolist.general.title'))
                                    ->schema([
                                        TextEntry::make('name')
                                            ->placeholder('-')
                                            ->label(__('accounts::filament/resources/journal.infolist.general.entries.name'))
                                            ->icon('heroicon-o-document-text'),
                                        TextEntry::make('type')
                                            ->placeholder('-')
                                            ->label(__('accounts::filament/resources/journal.infolist.general.entries.type'))
                                            ->icon('heroicon-o-tag'),
                                        TextEntry::make('company.name')
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
            'index'  => ListJournals::route('/'),
            'create' => CreateJournal::route('/create'),
            'view'   => ViewJournal::route('/{record}'),
            'edit'   => EditJournal::route('/{record}/edit'),
        ];
    }
}
