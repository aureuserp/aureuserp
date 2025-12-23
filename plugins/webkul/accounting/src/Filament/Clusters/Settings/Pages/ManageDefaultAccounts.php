<?php

namespace Webkul\Accounting\Filament\Clusters\Settings\Pages;

use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Group;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;
use Webkul\Account\Settings\DefaultAccountSettings;
use Webkul\Account\Models\Tax;
use Webkul\Account\Models\Account;
use Filament\Support\Enums\FontWeight;
use Webkul\Account\Models\Journal;
use Webkul\Account\Models\FiscalPosition;
use Webkul\Support\Models\Country;
use Filament\Schemas\Components\Text;
use Filament\Forms\Components\Radio;
use Webkul\Account\Enums\TaxIncludeOverride;
use Webkul\Support\Filament\Clusters\Settings;
use Webkul\Account\Enums\TypeTaxUse;
use Filament\Schemas\Components\Fieldset;

class ManageDefaultAccounts extends SettingsPage
{
    use HasPageShield;

    protected static ?string $slug = 'accounting/manage-default-accounts';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-circle';

    protected static string|\UnitEnum|null $navigationGroup = 'Accounting';

    protected static ?int $navigationSort = 11;

    protected static string $settings = DefaultAccountSettings::class;

    protected static ?string $cluster = Settings::class;

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
                            ->options(Account::all()->pluck('name', 'id'))
                            ->inlineLabel()
                            ->searchable(),
                        Select::make('expense_currency_exchange_account_id')
                            ->label(__('accounting::filament/clusters/settings/pages/manage-default-accounts.form.exchange-difference-entries.fields.loss.label'))
                            ->options(Account::all()->pluck('name', 'id'))
                            ->inlineLabel()
                            ->searchable(),
                    ])
                    ->columns(1),
               
                Fieldset::make(__('accounting::filament/clusters/settings/pages/manage-default-accounts.form.bank-transfer-and-payments.label'))
                    ->schema([
                        Select::make('account_journal_suspense_account_id')
                            ->label(__('accounting::filament/clusters/settings/pages/manage-default-accounts.form.bank-transfer-and-payments.fields.bank-suspense-account.label'))
                            ->options(Account::all()->pluck('name', 'id'))
                            ->inlineLabel()
                            ->searchable(),
                        Select::make('transfer_account_id')
                            ->label(__('accounting::filament/clusters/settings/pages/manage-default-accounts.form.bank-transfer-and-payments.fields.transfer-account.label'))
                            ->options(Account::all()->pluck('name', 'id'))
                            ->inlineLabel()
                            ->searchable(),
                    ])
                    ->columns(1),
                    
                Fieldset::make(__('accounting::filament/clusters/settings/pages/manage-default-accounts.form.product-accounts.label'))
                    ->schema([
                        Select::make('income_account_id')
                            ->label(__('accounting::filament/clusters/settings/pages/manage-default-accounts.form.product-accounts.fields.income-account.label'))
                            ->options(Account::all()->pluck('name', 'id'))
                            ->inlineLabel()
                            ->searchable(),
                        Select::make('expense_account_id')
                            ->label(__('accounting::filament/clusters/settings/pages/manage-default-accounts.form.product-accounts.fields.expense-account.label'))
                            ->options(Account::all()->pluck('name', 'id'))
                            ->inlineLabel()
                            ->searchable(),
                    ])
                    ->columns(1),
            ]);
    }
}
