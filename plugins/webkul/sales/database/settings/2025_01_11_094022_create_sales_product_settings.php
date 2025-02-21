<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('sales_product.unit_of_measurement', true);
    }

    public function down(): void
    {
        $this->migrator->deleteIfExists('sales_product.unit_of_measurement');
    }
};
