<?php

namespace Webkul\Invoice\Settings;

use Spatie\LaravelSettings\Settings;

class ProductSettings extends Settings
{
    public bool $enable_uom;

    public static function group(): string
    {
        return 'invoices_products';
    }
}
