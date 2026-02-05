<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('accounts_taxes.account_sale_tax_id', 1);
        $this->migrator->add('accounts_taxes.account_purchase_tax_id', 2);
        $this->migrator->add('accounts_taxes.account_price_include', 'tax_excluded');
        $this->migrator->add('accounts_taxes.tax_calculation_rounding_method', 'round_per_line');
        $this->migrator->add('accounts_taxes.account_fiscal_country_id', 233);
    }

    public function down(): void
    {
        $this->migrator->deleteIfExists('accounts_taxes.account_sale_tax_id');
        $this->migrator->deleteIfExists('accounts_taxes.account_purchase_tax_id');
        $this->migrator->deleteIfExists('accounts_taxes.account_price_include');
        $this->migrator->deleteIfExists('accounts_taxes.tax_calculation_rounding_method');
        $this->migrator->deleteIfExists('accounts_taxes.account_fiscal_country_id');
    }
};
