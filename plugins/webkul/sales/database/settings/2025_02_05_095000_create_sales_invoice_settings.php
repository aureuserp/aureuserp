<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('sales_invoicing.invoice_policy', 'delivery');
    }

    public function down(): void
    {
        $this->migrator->deleteIfExists('sales_invoicing.invoice_policy');
    }
};
