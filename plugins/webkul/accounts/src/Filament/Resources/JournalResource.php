<?php

namespace Webkul\Account\Filament\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Select;
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
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Webkul\Account\Enums\AccountType;
use Webkul\Account\Enums\CommunicationStandard;
use Webkul\Account\Enums\CommunicationType;
use Webkul\Account\Enums\JournalType;
use Webkul\Account\Enums\PaymentType;
use Webkul\Account\Filament\Resources\JournalResource\Pages\CreateJournal;
use Webkul\Account\Filament\Resources\JournalResource\Pages\EditJournal;
use Webkul\Account\Filament\Resources\JournalResource\Pages\ListJournals;
use Webkul\Account\Filament\Resources\JournalResource\Pages\ViewJournal;
use Webkul\Account\Models\Journal;
use Webkul\Support\Filament\Forms\Components\Repeater;
use Webkul\Support\Filament\Forms\Components\Repeater\TableColumn;
use Webkul\Support\Filament\Infolists\Components\RepeatableEntry;
use Webkul\Support\Filament\Infolists\Components\Repeater\TableColumn as InfolistTableColumn;
use Webkul\Support\Models\Company;

class JournalResource extends Resource
{
    protected static ?string $model = Journal::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-book-open';

    protected static bool $shouldRegisterNavigation = false;

    protected static bool $isGloballySearchable = false;

    protected static ?string $recordTitleAttribute = 'name';

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
                                                                        return ! in_array($get('type'), [JournalType::SALE, JournalType::PURCHASE]);
                                                                    })
                                                                    ->label(__('accounts::filament/resources/journal.form.tabs.journal-entries.field-set.accounting-information.fields.dedicated-credit-note-sequence')),
                                                                Toggle::make('payment_order')
                                                                    ->hidden(function (Get $get) {
                                                                        return ! in_array($get('type'), [JournalType::BANK, JournalType::CASH, JournalType::CREDIT_CARD]);
                                                                    })
                                                                    ->label(__('accounts::filament/resources/journal.form.tabs.journal-entries.field-set.accounting-information.fields.dedicated-payment-sequence')),
                                                                TextInput::make('code')
                                                                    ->label(__('accounts::filament/resources/journal.form.tabs.journal-entries.field-set.accounting-information.fields.sort-code'))
                                                                    ->placeholder(__('accounts::filament/resources/journal.form.tabs.journal-entries.field-set.accounting-information.fields.sort-code-placeholder')),
                                                                Select::make('currency_id')
                                                                    ->label(__('accounts::filament/resources/journal.form.tabs.journal-entries.field-set.accounting-information.fields.currency'))
                                                                    ->relationship(
                                                                        'currency',
                                                                        'name',
                                                                        modifyQueryUsing: fn (Builder $query) => $query->where('active', 1),
                                                                    )
                                                                    ->preload()
                                                                    ->searchable()
                                                                    ->live()
                                                                    ->afterStateUpdated(function (Set $set, Get $get) {
                                                                        $journalType = $get('type');

                                                                        if (! in_array($journalType, [JournalType::BANK, JournalType::CASH, JournalType::CREDIT_CARD])) {
                                                                            return;
                                                                        }

                                                                        $set('inboundPaymentMethodLines', Journal::getDefaultInboundPaymentMethodLines());
                                                                        $set('outboundPaymentMethodLines', Journal::getDefaultOutboundPaymentMethodLines());
                                                                    }),
                                                                ColorPicker::make('color')
                                                                    ->label(__('accounts::filament/resources/journal.form.tabs.journal-entries.field-set.accounting-information.fields.color'))
                                                                    ->hexColor(),
                                                                Select::make('default_account_id')
                                                                    ->label(__('accounts::filament/resources/journal.form.tabs.journal-entries.field-set.accounting-information.fields.default-account'))
                                                                    ->relationship('defaultAccount', 'name')
                                                                    ->preload()
                                                                    ->searchable()
                                                                    ->required(),

                                                                Select::make('profit_account_id')
                                                                    ->label(__('accounts::filament/resources/journal.form.tabs.journal-entries.field-set.accounting-information.fields.profit-account'))
                                                                    ->relationship(
                                                                        'profitAccount',
                                                                        'name',
                                                                        modifyQueryUsing: fn ($query) => $query->where('deprecated', false)->whereIn('account_type', [AccountType::INCOME, AccountType::INCOME_OTHER])
                                                                    )
                                                                    ->preload()
                                                                    ->searchable()
                                                                    ->visible(fn (Get $get) => in_array($get('type'), [
                                                                        JournalType::CASH,
                                                                        JournalType::SALE,
                                                                        JournalType::BANK,
                                                                    ])),

                                                                Select::make('loss_account_id')
                                                                    ->label(__('accounts::filament/resources/journal.form.tabs.journal-entries.field-set.accounting-information.fields.loss-account'))
                                                                    ->relationship(
                                                                        'lossAccount',
                                                                        'name',
                                                                        modifyQueryUsing: fn ($query) => $query->where('deprecated', false)->where('account_type', AccountType::EXPENSE)
                                                                    )
                                                                    ->preload()
                                                                    ->searchable()
                                                                    ->visible(fn (Get $get) => in_array($get('type'), [
                                                                        JournalType::CASH,
                                                                        JournalType::BANK,
                                                                        JournalType::PURCHASE,
                                                                    ])),

                                                                Select::make('suspense_account_id')
                                                                    ->label(__('accounts::filament/resources/journal.form.tabs.journal-entries.field-set.accounting-information.fields.suspense-account'))
                                                                    ->relationship('suspenseAccount', 'name')
                                                                    ->preload()
                                                                    ->searchable()
                                                                    ->visible(fn (Get $get) => in_array($get('type'), [
                                                                        JournalType::BANK,
                                                                        JournalType::CASH,
                                                                        JournalType::CREDIT_CARD,
                                                                    ])),

                                                            ])->columnSpanFull(),
                                                    ])->columns(2),
                                                Fieldset::make(__('accounts::filament/resources/journal.form.tabs.journal-entries.field-set.bank-account-number.title'))
                                                    ->visible(function (Get $get) {
                                                        return $get('type') === JournalType::BANK;
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
                                            ->visible(fn (Get $get) => in_array($get('type'), [
                                                JournalType::BANK,
                                                JournalType::CASH,
                                                JournalType::CREDIT_CARD,
                                            ]))
                                            ->schema([
                                                Repeater::make('inboundPaymentMethodLines')
                                                    ->hiddenLabel()
                                                    ->relationship('inboundPaymentMethodLines')
                                                    ->compact()
                                                    ->reactive()
                                                    ->addActionLabel(__('accounts::filament/resources/journal.form.tabs.incoming-payments.add-action-label'))
                                                    ->table([
                                                        TableColumn::make('payment_method_id')
                                                            ->label(__('accounts::filament/resources/journal.form.tabs.incoming-payments.fields.payment-method'))
                                                            ->width(200),

                                                        TableColumn::make('name')
                                                            ->label(__('accounts::filament/resources/journal.form.tabs.incoming-payments.fields.display-name'))
                                                            ->width(200),

                                                        TableColumn::make('payment_account_id')
                                                            ->label(__('accounts::filament/resources/journal.form.tabs.incoming-payments.fields.account-number'))
                                                            ->width(200),
                                                    ])
                                                    ->schema([
                                                        Select::make('payment_method_id')
                                                            ->label(__('accounts::filament/resources/journal.form.tabs.incoming-payments.fields.payment-method'))
                                                            ->relationship(
                                                                name: 'paymentMethod',
                                                                titleAttribute: 'name',
                                                                modifyQueryUsing: fn ($query) => $query->where('payment_type', PaymentType::RECEIVE)
                                                            )
                                                            ->searchable()
                                                            ->preload()
                                                            ->required(),

                                                        TextInput::make('name')
                                                            ->label(__('accounts::filament/resources/journal.form.tabs.incoming-payments.fields.display-name'))
                                                            ->maxLength(255)
                                                            ->required(),

                                                        Select::make('payment_account_id')
                                                            ->label(__('accounts::filament/resources/journal.form.tabs.incoming-payments.fields.account-number'))
                                                            ->relationship('paymentAccount', 'name')
                                                            ->searchable()
                                                            ->preload(),
                                                    ])
                                                    ->columns(2),
                                            ]),

                                        Tab::make(__('accounts::filament/resources/journal.form.tabs.outgoing-payments.title'))
                                            ->visible(fn (Get $get) => in_array($get('type'), [
                                                JournalType::BANK,
                                                JournalType::CASH,
                                                JournalType::CREDIT_CARD,
                                            ]))
                                            ->schema([
                                                Repeater::make('outboundPaymentMethodLines')
                                                    ->hiddenLabel()
                                                    ->relationship('outboundPaymentMethodLines')
                                                    ->compact()
                                                    ->reactive()
                                                    ->addActionLabel(__('accounts::filament/resources/journal.form.tabs.outgoing-payments.add-action-label'))
                                                    ->table([
                                                        TableColumn::make('payment_method_id')
                                                            ->label(__('accounts::filament/resources/journal.form.tabs.outgoing-payments.fields.payment-method'))
                                                            ->width(200),

                                                        TableColumn::make('name')
                                                            ->label(__('accounts::filament/resources/journal.form.tabs.outgoing-payments.fields.display-name'))
                                                            ->width(200),

                                                        TableColumn::make('payment_account_id')
                                                            ->label(__('accounts::filament/resources/journal.form.tabs.outgoing-payments.fields.account-number'))
                                                            ->width(200),
                                                    ])
                                                    ->schema([
                                                        Select::make('payment_method_id')
                                                            ->label(__('accounts::filament/resources/journal.form.tabs.outgoing-payments.fields.payment-method'))
                                                            ->relationship(
                                                                name: 'paymentMethod',
                                                                titleAttribute: 'name',
                                                                modifyQueryUsing: fn ($query) => $query->where('payment_type', PaymentType::SEND)
                                                            )
                                                            ->searchable()
                                                            ->preload()
                                                            ->required(),

                                                        TextInput::make('name')
                                                            ->label(__('accounts::filament/resources/journal.form.tabs.outgoing-payments.fields.display-name'))
                                                            ->maxLength(255)
                                                            ->required(),

                                                        Select::make('payment_account_id')
                                                            ->label(__('accounts::filament/resources/journal.form.tabs.outgoing-payments.fields.account-number'))
                                                            ->relationship('paymentAccount', 'name')
                                                            ->searchable()
                                                            ->preload(),
                                                    ])
                                                    ->columns(2),
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
                                                    ->visible(fn (Get $get) => $get('type') === JournalType::SALE)
                                                    ->schema([
                                                        Select::make('invoice_reference_type')
                                                            ->options(CommunicationType::class)
                                                            ->default(CommunicationType::INVOICE)
                                                            ->label(__('accounts::filament/resources/journal.form.tabs.advanced-settings.fields.communication-type')),
                                                        Select::make('invoice_reference_model')
                                                            ->options(CommunicationStandard::class)
                                                            ->default(CommunicationStandard::AUREUS)
                                                            ->label(__('accounts::filament/resources/journal.form.tabs.advanced-settings.fields.communication-standard')),
                                                    ]),
                                            ]),
                                    ]),
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
                                                    ->options(JournalType::class)
                                                    ->required()
                                                    ->live()
                                                    ->afterStateUpdated(function ($state, Set $set) {
                                                        if (in_array($state, [JournalType::BANK, JournalType::CASH, JournalType::CREDIT_CARD])) {
                                                            $set('inboundPaymentMethodLines', Journal::getDefaultInboundPaymentMethodLines());
                                                            $set('outboundPaymentMethodLines', Journal::getDefaultOutboundPaymentMethodLines());
                                                        } else {
                                                            $set('inboundPaymentMethodLines', []);
                                                            $set('outboundPaymentMethodLines', []);
                                                        }
                                                    }),
                                                Select::make('company_id')
                                                    ->label(__('accounts::filament/resources/journal.form.general.fields.company'))
                                                    ->disabled()
                                                    ->dehydrated()
                                                    ->options(fn () => Company::pluck('name', 'id'))
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
                TextColumn::make('creator.name')
                    ->searchable()
                    ->sortable()
                    ->label(__('accounts::filament/resources/journal.table.columns.created-by')),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make()
                    ->action(function (Journal $record, DeleteAction $action) {
                        try {
                            $record->delete();

                            $action->success();
                        } catch (QueryException $e) {
                            $action->failure();
                        }
                    })
                    ->failureNotification(
                        Notification::make()
                            ->danger()
                            ->title(__('accounts::filament/resources/journal.table.actions.delete.notification.error.title'))
                            ->body(__('accounts::filament/resources/journal.table.actions.delete.notification.error.body'))
                    )
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('accounts::filament/resources/journal.table.actions.delete.notification.success.title'))
                            ->body(__('accounts::filament/resources/journal.table.actions.delete.notification.success.body'))
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->action(function (Collection $records, DeleteBulkAction $action) {
                            try {
                                $records->each(fn (Model $record) => $record->delete());

                                $action->success();
                            } catch (QueryException $e) {
                                $action->failure();
                            }
                        })
                        ->failureNotification(
                            Notification::make()
                                ->danger()
                                ->title(__('accounts::filament/resources/journal.table.bulk-actions.delete.notification.error.title'))
                                ->body(__('accounts::filament/resources/journal.table.bulk-actions.delete.notification.error.body'))
                        )
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('accounts::filament/resources/journal.table.bulk-actions.delete.notification.success.title'))
                                ->body(__('accounts::filament/resources/journal.table.bulk-actions.delete.notification.success.body'))
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
                                                            ->visible(fn ($record) => in_array($record->type, [JournalType::SALE, JournalType::PURCHASE]))
                                                            ->placeholder('-')
                                                            ->label(__('accounts::filament/resources/journal.infolist.tabs.journal-entries.field-set.accounting-information.entries.dedicated-credit-note-sequence')),
                                                        IconEntry::make('payment_order')
                                                            ->boolean()
                                                            ->placeholder('-')
                                                            ->visible(fn ($record) => in_array($record->type, [JournalType::BANK, JournalType::CASH, JournalType::CREDIT_CARD]))
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
                                                        // Inside accounting-information Fieldset in infolist
                                                        TextEntry::make('defaultAccount.name')
                                                            ->placeholder('-')
                                                            ->label(__('accounts::filament/resources/journal.infolist.tabs.journal-entries.field-set.accounting-information.entries.default-account')),

                                                        TextEntry::make('profitAccount.name')
                                                            ->placeholder('-')
                                                            ->label(__('accounts::filament/resources/journal.infolist.tabs.journal-entries.field-set.accounting-information.entries.profit-account'))
                                                            ->visible(fn (Get $get) => in_array($get('type'), [
                                                                JournalType::CASH,
                                                                JournalType::SALE,
                                                                JournalType::BANK,
                                                            ])),

                                                        TextEntry::make('lossAccount.name')
                                                            ->placeholder('-')
                                                            ->label(__('accounts::filament/resources/journal.infolist.tabs.journal-entries.field-set.accounting-information.entries.loss-account'))
                                                            ->visible(fn (Get $get) => in_array($get('type'), [
                                                                JournalType::CASH,
                                                                JournalType::BANK,
                                                                JournalType::PURCHASE,
                                                            ])),

                                                        TextEntry::make('suspenseAccount.name')
                                                            ->placeholder('-')
                                                            ->label(__('accounts::filament/resources/journal.infolist.tabs.journal-entries.field-set.accounting-information.entries.suspense-account'))
                                                            ->visible(fn ($record) => in_array($record->type, [
                                                                JournalType::BANK,
                                                                JournalType::CASH,
                                                                JournalType::CREDIT_CARD,
                                                            ])),

                                                    ])->columnSpanFull(),
                                                Section::make(__('accounts::filament/resources/journal.infolist.tabs.journal-entries.field-set.bank-account-number.title'))
                                                    ->visible(fn ($record) => $record->type === JournalType::BANK)
                                                    ->schema([
                                                        TextEntry::make('bankAccount.account_number')
                                                            ->placeholder('-')
                                                            ->label(__('accounts::filament/resources/journal.infolist.tabs.journal-entries.field-set.bank-account-number.entries.account-number')),
                                                    ]),
                                            ]),

                                        Tab::make(__('accounts::filament/resources/journal.form.tabs.incoming-payments.title'))
                                            ->visible(fn (Get $get) => in_array($get('type'), [
                                                JournalType::BANK,
                                                JournalType::CASH,
                                                JournalType::CREDIT_CARD,
                                            ]))
                                            ->schema([
                                                RepeatableEntry::make('inboundPaymentMethodLines')
                                                    ->hiddenLabel()
                                                    ->table([
                                                        InfolistTableColumn::make('paymentMethod.name')
                                                            ->alignCenter()
                                                            ->label(__('accounts::filament/resources/journal.infolist.tabs.incoming-payments.entries.payment-method')),

                                                        InfolistTableColumn::make('name')
                                                            ->alignCenter()
                                                            ->label(__('accounts::filament/resources/journal.infolist.tabs.incoming-payments.entries.display-name')),
                                                        InfolistTableColumn::make('paymentAccount.name')
                                                            ->alignCenter()
                                                            ->label(__('accounts::filament/resources/journal.infolist.tabs.incoming-payments.entries.account-number')),
                                                    ])
                                                    ->schema([
                                                        TextEntry::make('paymentMethod.name')
                                                            ->label(__('accounts::filament/resources/journal.infolist.tabs.incoming-payments.entries.payment-method')),
                                                        TextEntry::make('name')
                                                            ->label(__('accounts::filament/resources/journal.infolist.tabs.incoming-payments.entries.display-name'))
                                                            ->placeholder('-'),

                                                        TextEntry::make('paymentAccount.name')
                                                            ->label(__('accounts::filament/resources/journal.infolist.tabs.incoming-payments.entries.account-number')),
                                                    ]),
                                            ]),

                                        Tab::make(__('accounts::filament/resources/journal.form.tabs.outgoing-payments.title'))
                                            ->visible(fn (Get $get) => in_array($get('type'), [
                                                JournalType::BANK,
                                                JournalType::CASH,
                                                JournalType::CREDIT_CARD,
                                            ]))
                                            ->schema([
                                                RepeatableEntry::make('inboundPaymentMethodLines')
                                                    ->hiddenLabel()
                                                    ->table([
                                                        InfolistTableColumn::make('paymentMethod.name')
                                                            ->alignCenter()
                                                            ->label(__('accounts::filament/resources/journal.infolist.tabs.outgoing-payments.entries.payment-method')),

                                                        InfolistTableColumn::make('name')
                                                            ->alignCenter()
                                                            ->label(__('accounts::filament/resources/journal.infolist.tabs.outgoing-payments.entries.display-name')),
                                                        InfolistTableColumn::make('paymentAccount.name')
                                                            ->alignCenter()
                                                            ->label(__('accounts::filament/resources/journal.infolist.tabs.outgoing-payments.entries.account-number')),
                                                    ])
                                                    ->schema([
                                                        TextEntry::make('paymentMethod.name')
                                                            ->label(__('accounts::filament/resources/journal.infolist.tabs.outgoing-payments.entries.payment-method')),
                                                        TextEntry::make('name')
                                                            ->label(__('accounts::filament/resources/journal.infolist.tabs.outgoing-payments.entries.display-name'))
                                                            ->placeholder('-'),

                                                        TextEntry::make('paymentAccount.name')
                                                            ->label(__('accounts::filament/resources/journal.infolist.tabs.outgoing-payments.entries.account-number')),
                                                    ]),
                                            ]),

                                        Tab::make(__('accounts::filament/resources/journal.infolist.tabs.advanced-settings.title'))
                                            ->schema([
                                                Fieldset::make(__('accounts::filament/resources/journal.infolist.tabs.advanced-settings.title'))
                                                    ->schema([
                                                        TextEntry::make('allowedAccounts.name')
                                                            ->placeholder('-')
                                                            ->listWithLineBreaks()
                                                            ->label(__('accounts::filament/resources/journal.infolist.tabs.advanced-settings.allowed-accounts.entries.allowed-accounts')),
                                                        IconEntry::make('auto_check_on_post')
                                                            ->boolean()
                                                            ->placeholder('-')
                                                            ->label(__('accounts::filament/resources/journal.infolist.tabs.advanced-settings.allowed-accounts.entries.auto-check-on-post')),
                                                    ]),

                                                Fieldset::make(__('accounts::filament/resources/journal.infolist.tabs.advanced-settings.payment-communication.title'))
                                                    ->visible(fn ($record) => $record->type === JournalType::SALE)
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
                    ])->columnSpanFull(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
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
