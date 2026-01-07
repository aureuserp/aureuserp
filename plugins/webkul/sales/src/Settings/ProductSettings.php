<?php

namespace Webkul\Sale\Settings;

use Spatie\LaravelSettings\Settings;

class ProductSettings extends Settings
{
    public bool $enable_deliver_content_by_email;

    public static function group(): string
    {
        return 'sales_product';
    }
}
