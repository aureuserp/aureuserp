<?php

namespace Webkul\Product\Settings;

use Spatie\LaravelSettings\Settings;

class ProductSettings extends Settings
{
    public bool $enable_variants;

    public bool $enable_uom;

    public bool $enable_packagings;

    public bool $enable_price_lists;

    public static function group(): string
    {
        return 'products_product';
    }
}
