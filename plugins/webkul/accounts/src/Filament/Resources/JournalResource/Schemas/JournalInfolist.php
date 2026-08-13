<?php

namespace Webkul\Account\Filament\Resources\JournalResource\Schemas;

use Filament\Infolists\Components\ColorEntry;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Webkul\Account\Enums\JournalType;
use Webkul\Support\Filament\Infolists\Components\RepeatableEntry;
use Webkul\Support\Filament\Infolists\Components\Repeater\TableColumn as InfolistTableColumn;

class JournalInfolist
{
    public static function configure(Schema $schema): Schema
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
}
