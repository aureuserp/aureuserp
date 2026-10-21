<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;
use Webkul\PointOfSale\Enums\StockUpdateMode;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('point_of_sale_inventory.stock_update_mode', StockUpdateMode::REAL_TIME->value);
    }

    public function down(): void
    {
        $this->migrator->deleteIfExists('point_of_sale_inventory.stock_update_mode');
    }
};
