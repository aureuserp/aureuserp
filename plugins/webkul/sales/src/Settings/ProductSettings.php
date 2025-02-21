<?php

namespace Webkul\Sale\Settings;

use Spatie\LaravelSettings\Settings;

class ProductSettings extends Settings
{
    public bool $unit_of_measurement;

    public static function group(): string
    {
        return 'sales_product';
    }
}
