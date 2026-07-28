<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('manufacturing_operation.enable_work_orders', false);
        $this->migrator->add('manufacturing_operation.enable_work_order_dependencies', false);
        $this->migrator->add('manufacturing_operation.enable_byproducts', false);
    }

    public function down(): void
    {
        $this->migrator->deleteIfExists('manufacturing_operation.enable_work_orders');
        $this->migrator->deleteIfExists('manufacturing_operation.enable_work_order_dependencies');
        $this->migrator->deleteIfExists('manufacturing_operation.enable_byproducts');
    }
};
