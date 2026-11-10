<?php

namespace Webkul\Inventory\Settings;

use Spatie\LaravelSettings\Settings;

class OperationSettings extends Settings
{
    public bool $enable_packages;

    public bool $enable_warnings;

    public bool $enable_reception_report;

    public int $annual_inventory_day;

    public int $annual_inventory_month;

    public bool $enable_returning_more_than_received;

    public bool $enable_returning_returns;

    public static function group(): string
    {
        return 'inventories_operation';
    }
}
