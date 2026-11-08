<?php

namespace Webkul\Asset\Settings;

use Spatie\LaravelSettings\Settings;

class AssetSettings extends Settings
{
    public string $tag_prefix = 'AST-';

    public bool $auto_generate_tag = true;

    public int $tag_padding = 5;

    public string $default_condition = 'new';

    public bool $require_serial_by_default = false;

    public ?int $default_warehouse_id = null;

    public bool $enable_site_assets = true;

    public bool $require_site_on_custody = false;

    public ?int $default_custody_duration_days = null;

    public bool $require_expected_return_date = false;

    public bool $prevent_duplicate_product_custody = false;

    public bool $require_attachments_on_custody = false;

    public bool $enable_consumables_batch_issue = true;

    public bool $auto_fill_consumable_employees_by_site = true;

    public int $default_consumable_issue_quantity = 1;

    public bool $require_site_on_consumable_issue = false;

    public bool $require_attachments_on_consumable_issue = false;

    public bool $require_attachments_on_damage = true;

    public bool $require_attachments_on_termination = false;

    public bool $restrict_termination_to_managers = false;

    public ?float $termination_approval_cost_threshold = null;

    public static function group(): string
    {
        return 'assets';
    }
}
