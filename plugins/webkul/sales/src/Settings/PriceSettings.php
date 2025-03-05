<?php

namespace Webkul\Sale\Settings;

use Spatie\LaravelSettings\Settings;

class PriceSettings extends Settings
{
    public bool $enable_discount;

    public bool $enable_margin;

    public static function group(): string
    {
        return 'sales_price';
    }
}
