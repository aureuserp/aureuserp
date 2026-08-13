<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('manufacturing_planning.manufacturing_lead', 0);
    }

    public function down(): void
    {
        $this->migrator->deleteIfExists('manufacturing_planning.manufacturing_lead');
    }
};
