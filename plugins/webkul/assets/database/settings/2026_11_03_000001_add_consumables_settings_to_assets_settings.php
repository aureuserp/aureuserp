<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $defaults = [
            'assets.enable_consumables_batch_issue'          => true,
            'assets.auto_fill_consumable_employees_by_site'  => true,
            'assets.default_consumable_issue_quantity'       => 1,
            'assets.require_site_on_consumable_issue'        => false,
            'assets.require_attachments_on_consumable_issue' => false,
        ];

        foreach ($defaults as $key => $value) {
            if (! $this->migrator->exists($key)) {
                $this->migrator->add($key, $value);
            }
        }
    }

    public function down(): void
    {
        $this->migrator->deleteIfExists('assets.enable_consumables_batch_issue');
        $this->migrator->deleteIfExists('assets.auto_fill_consumable_employees_by_site');
        $this->migrator->deleteIfExists('assets.default_consumable_issue_quantity');
        $this->migrator->deleteIfExists('assets.require_site_on_consumable_issue');
        $this->migrator->deleteIfExists('assets.require_attachments_on_consumable_issue');
    }
};
