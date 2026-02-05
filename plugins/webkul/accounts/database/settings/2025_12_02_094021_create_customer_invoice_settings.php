<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('accounts_customer_invoice.group_cash_rounding', false);
        $this->migrator->add('accounts_customer_invoice.incoterm_id', null);
    }

    public function down(): void
    {
        $this->migrator->deleteIfExists('accounts_customer_invoice.group_cash_rounding');
        $this->migrator->deleteIfExists('accounts_customer_invoice.incoterm_id');
    }
};
