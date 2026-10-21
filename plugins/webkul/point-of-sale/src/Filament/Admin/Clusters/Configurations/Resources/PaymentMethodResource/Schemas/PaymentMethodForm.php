<?php

namespace Webkul\PointOfSale\Filament\Admin\Clusters\Configurations\Resources\PaymentMethodResource\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Account\Enums\JournalType;
use Webkul\Account\Models\Journal;
use Webkul\PointOfSale\Enums\PaymentTerminalType;

class PaymentMethodForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('point-of-sale::filament/admin/clusters/configurations/resources/payment-method.form.sections.general.title'))
                    ->schema([
                        TextInput::make('name')
                            ->label(__('point-of-sale::filament/admin/clusters/configurations/resources/payment-method.form.sections.general.fields.name'))
                            ->required()
                            ->maxLength(255)
                            ->autofocus(),

                        Select::make('company_id')
                            ->label(__('point-of-sale::filament/admin/clusters/configurations/resources/payment-method.form.sections.general.fields.company'))
                            ->relationship('company', 'name')
                            ->default(fn (): ?int => current_company_id())
                            ->disabled()
                            ->dehydrated()
                            ->native(false),

                        Select::make('terminal_type')
                            ->label(__('point-of-sale::filament/admin/clusters/configurations/resources/payment-method.form.sections.general.fields.terminal-type'))
                            ->options(PaymentTerminalType::class)
                            ->native(false)
                            ->required(),

                        Toggle::make('is_split_transaction')
                            ->live()
                            ->label(__('point-of-sale::filament/admin/clusters/configurations/resources/payment-method.form.sections.general.fields.is-split-transaction'))
                            ->helperText(__('point-of-sale::filament/admin/clusters/configurations/resources/payment-method.form.sections.general.fields.is-split-transaction-helper-text')),
                    ])
                    ->columns(2),

                Section::make(__('point-of-sale::filament/admin/clusters/configurations/resources/payment-method.form.sections.accounting.title'))
                    ->schema([
                        Select::make('journal_id')
                            ->label(__('point-of-sale::filament/admin/clusters/configurations/resources/payment-method.form.sections.accounting.fields.journal'))
                            ->relationship(
                                'journal',
                                'name',
                                modifyQueryUsing: fn (Builder $query, Get $get) => $query
                                    ->whereIn('type', [JournalType::CASH, JournalType::BANK, JournalType::CREDIT_CARD])
                                    ->where(owned_by_company($get('company_id'))),
                            )
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->required(fn (Get $get): bool => ! $get('is_split_transaction'))
                            ->placeholder(__('point-of-sale::filament/admin/clusters/configurations/resources/payment-method.form.sections.accounting.fields.journal-placeholder'))
                            ->live(),

                        Select::make('receivable_account_id')
                            ->label(__('point-of-sale::filament/admin/clusters/configurations/resources/payment-method.form.sections.accounting.fields.receivable-account'))
                            ->relationship('receivableAccount', 'name')
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->placeholder(__('point-of-sale::filament/admin/clusters/configurations/resources/payment-method.form.sections.accounting.fields.account-placeholder'))
                            ->hidden(fn (Get $get): bool => (bool) $get('is_split_transaction')),

                        Select::make('outstanding_account_id')
                            ->label(__('point-of-sale::filament/admin/clusters/configurations/resources/payment-method.form.sections.accounting.fields.outstanding-account'))
                            ->relationship('outstandingAccount', 'name')
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->placeholder(__('point-of-sale::filament/admin/clusters/configurations/resources/payment-method.form.sections.accounting.fields.account-placeholder'))
                            ->visible(fn (Get $get): bool => static::isBankJournal($get('journal_id')))
                            ->required(fn (Get $get): bool => static::isBankJournal($get('journal_id'))),
                    ])
                    ->columns(2),
            ]);
    }

    protected static function isBankJournal(mixed $journalId): bool
    {
        if (blank($journalId)) {
            return false;
        }

        return Journal::query()->whereKey($journalId)->value('type') === JournalType::BANK;
    }
}
