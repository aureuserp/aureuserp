<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('sales_quotation_and_orders.default_quotation_validity', 30);
        $this->migrator->add('sales_quotation_and_orders.enable_lock_confirm_sales', false);
    }

    public function down(): void
    {
        $this->migrator->deleteIfExists('sales_quotation_and_orders.default_quotation_validity');
        $this->migrator->deleteIfExists('sales_quotation_and_orders.enable_lock_confirm_sales');
    }
};
