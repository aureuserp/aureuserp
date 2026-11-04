<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('crm.use_recurring_revenues', true);
    }

    public function down(): void
    {
        $this->migrator->deleteIfExists('crm.use_recurring_revenues');
    }
};
