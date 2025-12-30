<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('products_product.enable_variants', true);
        $this->migrator->add('products_product.enable_uom', false);
        $this->migrator->add('products_product.enable_packagings', false);
    }

    public function down(): void
    {
        $this->migrator->deleteIfExists('products_product.enable_variants');
        $this->migrator->deleteIfExists('products_product.enable_uom');
        $this->migrator->deleteIfExists('products_product.enable_packagings');
    }
};
