<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('products_product.enable_deliver_content_by_email', false);
    }

    public function down(): void
    {
        $this->migrator->deleteIfExists('products_product.enable_deliver_content_by_email');
    }
};
