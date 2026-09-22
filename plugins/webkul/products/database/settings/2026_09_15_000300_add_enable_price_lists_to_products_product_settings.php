<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        if ($this->migrator->exists('products_product.enable_price_lists')) {
            return;
        }

        $this->migrator->add('products_product.enable_price_lists', false);
    }

    public function down(): void
    {
        $this->migrator->deleteIfExists('products_product.enable_price_lists');
    }
};
