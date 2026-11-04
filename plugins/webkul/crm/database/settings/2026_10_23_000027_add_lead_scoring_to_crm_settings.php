<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('crm.scoring_variables', ['phone', 'email']);
        $this->migrator->add('crm.scoring_start_date', null);
    }

    public function down(): void
    {
        $this->migrator->deleteIfExists('crm.scoring_variables');
        $this->migrator->deleteIfExists('crm.scoring_start_date');
    }
};
