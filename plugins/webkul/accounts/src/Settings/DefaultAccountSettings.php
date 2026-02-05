<?php

namespace Webkul\Account\Settings;

use Spatie\LaravelSettings\Settings;

class DefaultAccountSettings extends Settings
{
    public int $currency_exchange_journal_id;

    public int $income_currency_exchange_account_id;

    public int $expense_currency_exchange_account_id;

    public ?int $account_discount_expense_allocation_id;

    public ?int $account_discount_income_allocation_id;

    public int $account_journal_suspense_account_id;

    public int $transfer_account_id;

    public int $account_journal_payment_debit_account_id;

    public int $account_journal_payment_credit_account_id;

    public int $income_account_id;

    public int $expense_account_id;

    public static function group(): string
    {
        return 'accounts_accounts';
    }
}
