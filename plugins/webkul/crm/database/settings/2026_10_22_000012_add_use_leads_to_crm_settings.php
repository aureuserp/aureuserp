<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('crm.use_leads', true);
    }

    public function down(): void
    {
        $this->migrator->deleteIfExists('crm.use_leads');
    }
};
