<?php

namespace Webkul\Manufacturing\Settings;

use Spatie\LaravelSettings\Settings;

class OperationSettings extends Settings
{
    public bool $enable_work_orders;

    public bool $enable_work_order_dependencies;

    public bool $enable_byproducts;

    public static function group(): string
    {
        return 'manufacturing_operation';
    }
}
