<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('invoices_products.enable_uom', false);
    }

    public function down(): void
    {
        $this->migrator->deleteIfExists('invoices_products.enable_uom');
    }
};
