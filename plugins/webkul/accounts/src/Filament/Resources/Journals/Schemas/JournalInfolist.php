<?php

namespace Webkul\Account\Filament\Resources\Journals\Schemas;

use Filament\Infolists\Components\ColorEntry;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Webkul\Account\Enums\JournalType;

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
}
