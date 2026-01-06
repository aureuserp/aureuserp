<?php

namespace Webkul\Sale\Settings;

use Webkul\Product\Settings\ProductSettings as BaseProductSettings;

class ProductSettings extends BaseProductSettings
{
    public bool $enable_deliver_content_by_email;

    public static function group(): string
    {
        return 'sales_product';
    }
}
