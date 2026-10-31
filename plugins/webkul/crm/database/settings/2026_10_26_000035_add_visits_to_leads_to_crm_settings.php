<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('crm.visits_to_leads', false);
    }

    public function down(): void
    {
        $this->migrator->deleteIfExists('crm.visits_to_leads');
    }
};
