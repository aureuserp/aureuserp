<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('inventories_operation.enable_returning_more_than_received', false);
        $this->migrator->add('inventories_operation.enable_returning_returns', false);
    }

    public function down(): void
    {
        $this->migrator->deleteIfExists('inventories_operation.enable_returning_more_than_received');
        $this->migrator->deleteIfExists('inventories_operation.enable_returning_returns');
    }
};
