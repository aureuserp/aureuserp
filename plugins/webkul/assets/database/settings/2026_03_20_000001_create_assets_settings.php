<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('assets.tag_prefix', 'AST-');
        $this->migrator->add('assets.auto_generate_tag', true);
        $this->migrator->add('assets.tag_padding', 5);
        $this->migrator->add('assets.default_condition', 'new');
        $this->migrator->add('assets.require_serial_by_default', false);
        $this->migrator->add('assets.default_warehouse_id', null);
        $this->migrator->add('assets.enable_site_assets', true);
        $this->migrator->add('assets.require_site_on_custody', false);
        $this->migrator->add('assets.default_custody_duration_days', null);
        $this->migrator->add('assets.require_expected_return_date', false);
        $this->migrator->add('assets.prevent_duplicate_product_custody', false);
        $this->migrator->add('assets.require_attachments_on_custody', false);
        $this->migrator->add('assets.require_attachments_on_damage', true);
        $this->migrator->add('assets.require_attachments_on_termination', false);
        $this->migrator->add('assets.restrict_termination_to_managers', false);
        $this->migrator->add('assets.termination_approval_cost_threshold', null);

        $consumableDefaults = [
            'assets.enable_consumables_batch_issue'          => true,
            'assets.auto_fill_consumable_employees_by_site'  => true,
            'assets.default_consumable_issue_quantity'       => 1,
            'assets.require_site_on_consumable_issue'        => false,
            'assets.require_attachments_on_consumable_issue' => false,
        ];

        foreach ($consumableDefaults as $key => $value) {
            if (! $this->migrator->exists($key)) {
                $this->migrator->add($key, $value);
            }
        }
    }

    public function down(): void
    {
        $this->migrator->deleteIfExists('assets.tag_prefix');
        $this->migrator->deleteIfExists('assets.auto_generate_tag');
        $this->migrator->deleteIfExists('assets.tag_padding');
        $this->migrator->deleteIfExists('assets.default_condition');
        $this->migrator->deleteIfExists('assets.require_serial_by_default');
        $this->migrator->deleteIfExists('assets.default_warehouse_id');
        $this->migrator->deleteIfExists('assets.enable_site_assets');
        $this->migrator->deleteIfExists('assets.require_site_on_custody');
        $this->migrator->deleteIfExists('assets.default_custody_duration_days');
        $this->migrator->deleteIfExists('assets.require_expected_return_date');
        $this->migrator->deleteIfExists('assets.prevent_duplicate_product_custody');
        $this->migrator->deleteIfExists('assets.require_attachments_on_custody');
        $this->migrator->deleteIfExists('assets.require_attachments_on_damage');
        $this->migrator->deleteIfExists('assets.require_attachments_on_termination');
        $this->migrator->deleteIfExists('assets.restrict_termination_to_managers');
        $this->migrator->deleteIfExists('assets.termination_approval_cost_threshold');
        $this->migrator->deleteIfExists('assets.enable_consumables_batch_issue');
        $this->migrator->deleteIfExists('assets.auto_fill_consumable_employees_by_site');
        $this->migrator->deleteIfExists('assets.default_consumable_issue_quantity');
        $this->migrator->deleteIfExists('assets.require_site_on_consumable_issue');
        $this->migrator->deleteIfExists('assets.require_attachments_on_consumable_issue');
    }
};
