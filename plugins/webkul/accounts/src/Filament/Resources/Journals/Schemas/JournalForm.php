<?php

namespace Webkul\Account\Filament\Resources\Journals\Schemas;

use Filament\Schemas\Schema;

class JournalForm
{
    public static function configure(Schema $schema): Schema
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
            ->columns('full');
    }
}
