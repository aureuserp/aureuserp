<?php

namespace Webkul\Accounting\Filament\Clusters\Settings\Pages;

use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms\Components\Select;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Schema;
use Webkul\Account\Enums\AccountType;
use Webkul\Account\Models\Account;
use Webkul\Account\Models\Journal;
use Webkul\Account\Settings\DefaultAccountSettings;
use Webkul\Support\Filament\Clusters\Settings;

class ManageDefaultAccounts extends SettingsPage
{
    use HasPageShield;

    protected static ?string $slug = 'accounting/manage-default-accounts';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-circle';

    protected static string|\UnitEnum|null $navigationGroup = 'Accounting';

    protected static ?int $navigationSort = 11;

    protected static string $settings = DefaultAccountSettings::class;

    protected static ?string $cluster = Settings::class;

    protected static function getPagePermission(): ?string
    {
        return 'page_accounting_manage_default_accounts';
    }

    public function getBreadcrumbs(): array
    {
        return [
            __('accounting::filament/clusters/settings/pages/manage-default-accounts.title'),
        ];
    }

    public function getTitle(): string
    {
        return __('accounting::filament/clusters/settings/pages/manage-default-accounts.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('accounting::filament/clusters/settings/pages/manage-default-accounts.title');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Fieldset::make(__('accounting::filament/clusters/settings/pages/manage-default-accounts.form.exchange-difference-entries.label'))
                    ->schema([
                        Select::make('currency_exchange_journal_id')
                            ->label(__('accounting::filament/clusters/settings/pages/manage-default-accounts.form.exchange-difference-entries.fields.journal.label'))
                            ->options(Journal::all()->pluck('name', 'id'))
                            ->inlineLabel()
                            ->searchable(),
                        Select::make('income_currency_exchange_account_id')
                            ->label(__('accounting::filament/clusters/settings/pages/manage-default-accounts.form.exchange-difference-entries.fields.gain.label'))
                            ->options(Account::whereIn('account_type', [AccountType::INCOME, AccountType::INCOME_OTHER])->where('deprecated', false)->get()->pluck('name', 'id'))
                            ->inlineLabel()
                            ->searchable(),
                        Select::make('expense_currency_exchange_account_id')
                            ->label(__('accounting::filament/clusters/settings/pages/manage-default-accounts.form.exchange-difference-entries.fields.loss.label'))
                            ->options(Account::whereIn('account_type', [AccountType::EXPENSE, AccountType::EXPENSE_DEPRECIATION, AccountType::EXPENSE_DIRECT_COST])->where('deprecated', false)->get()->pluck('name', 'id'))
                            ->inlineLabel()
                            ->searchable(),
                    ])
                    ->columns(1),

                Fieldset::make(__('accounting::filament/clusters/settings/pages/manage-default-accounts.form.bank-transfer-and-payments.label'))
                    ->schema([
                        Select::make('account_journal_suspense_account_id')
                            ->label(__('accounting::filament/clusters/settings/pages/manage-default-accounts.form.bank-transfer-and-payments.fields.bank-suspense-account.label'))
                            ->options(Account::whereIn('account_type', [AccountType::ASSET_CURRENT, AccountType::LIABILITY_CURRENT])->where('deprecated', false)->get()->pluck('name', 'id'))
                            ->inlineLabel()
                            ->searchable(),
                        Select::make('transfer_account_id')
                            ->label(__('accounting::filament/clusters/settings/pages/manage-default-accounts.form.bank-transfer-and-payments.fields.transfer-account.label'))
                            ->options(Account::where('account_type', AccountType::ASSET_CURRENT)->where('deprecated', false)->where('reconcile', true)->get()->pluck('name', 'id'))
                            ->inlineLabel()
                            ->searchable(),
                    ])
                    ->columns(1),

                Fieldset::make(__('accounting::filament/clusters/settings/pages/manage-default-accounts.form.product-accounts.label'))
                    ->schema([
                        Select::make('income_account_id')
                            ->label(__('accounting::filament/clusters/settings/pages/manage-default-accounts.form.product-accounts.fields.income-account.label'))
                            ->options(Account::whereNotIn('account_type', [AccountType::ASSET_RECEIVABLE, AccountType::LIABILITY_PAYABLE, AccountType::ASSET_CASH, AccountType::LIABILITY_CREDIT_CARD, AccountType::OFF_BALANCE])->where('deprecated', false)->get()->pluck('name', 'id'))
                            ->inlineLabel()
                            ->searchable(),
                        Select::make('expense_account_id')
                            ->label(__('accounting::filament/clusters/settings/pages/manage-default-accounts.form.product-accounts.fields.expense-account.label'))
                            ->options(Account::whereNotIn('account_type', [AccountType::ASSET_RECEIVABLE, AccountType::LIABILITY_PAYABLE, AccountType::ASSET_CASH, AccountType::LIABILITY_CREDIT_CARD, AccountType::OFF_BALANCE])->where('deprecated', false)->get()->pluck('name', 'id'))
                            ->inlineLabel()
                            ->searchable(),
                    ])
                    ->columns(1),
            ]);
    }
}
