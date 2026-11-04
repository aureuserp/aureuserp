<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('crm.multi_teams', false);
        $this->migrator->add('crm.use_auto_assignment', false);
        $this->migrator->add('crm.auto_assignment_action', 'manual');
        $this->migrator->add('crm.auto_assignment_interval_number', 1);
        $this->migrator->add('crm.auto_assignment_interval_type', 'days');
        $this->migrator->add('crm.auto_assignment_run_at', null);
    }

    public function down(): void
    {
        foreach (['multi_teams', 'use_auto_assignment', 'auto_assignment_action', 'auto_assignment_interval_number', 'auto_assignment_interval_type', 'auto_assignment_run_at'] as $key) {
            $this->migrator->deleteIfExists('crm.'.$key);
        }
    }
};
