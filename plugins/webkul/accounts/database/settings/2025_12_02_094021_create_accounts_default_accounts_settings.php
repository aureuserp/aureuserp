<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('accounts_accounts.currency_exchange_journal_id', 4);
        $this->migrator->add('accounts_accounts.income_currency_exchange_account_id', 28);
        $this->migrator->add('accounts_accounts.expense_currency_exchange_account_id', 38);
        $this->migrator->add('accounts_accounts.account_discount_expense_allocation_id', null);
        $this->migrator->add('accounts_accounts.account_discount_income_allocation_id', null);
        $this->migrator->add('accounts_accounts.account_journal_suspense_account_id', 47);
        $this->migrator->add('accounts_accounts.account_journal_payment_debit_account_id', 49);
        $this->migrator->add('accounts_accounts.account_journal_payment_credit_account_id', 50);
        $this->migrator->add('accounts_accounts.income_account_id', 27);
        $this->migrator->add('accounts_accounts.expense_account_id', 33);
        $this->migrator->add('accounts_accounts.transfer_account_id', 48);
    }

    public function down(): void
    {
        $this->migrator->deleteIfExists('accounts_accounts.currency_exchange_journal_id');
        $this->migrator->deleteIfExists('accounts_accounts.income_currency_exchange_account_id');
        $this->migrator->deleteIfExists('accounts_accounts.expense_currency_exchange_account_id');
        $this->migrator->deleteIfExists('accounts_accounts.account_discount_expense_allocation_id');
        $this->migrator->deleteIfExists('accounts_accounts.account_discount_income_allocation_id');
        $this->migrator->deleteIfExists('accounts_accounts.account_journal_suspense_account_id');
        $this->migrator->deleteIfExists('accounts_accounts.account_journal_payment_debit_account_id');
        $this->migrator->deleteIfExists('accounts_accounts.account_journal_payment_credit_account_id');
        $this->migrator->deleteIfExists('accounts_accounts.income_account_id');
        $this->migrator->deleteIfExists('accounts_accounts.expense_account_id');
        $this->migrator->deleteIfExists('accounts_accounts.transfer_account_id');
    }
};
