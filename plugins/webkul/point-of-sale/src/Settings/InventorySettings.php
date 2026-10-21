<?php

namespace Webkul\PointOfSale\Settings;

use Spatie\LaravelSettings\Settings;
use Webkul\PointOfSale\Enums\StockUpdateMode;

class InventorySettings extends Settings
{
    public StockUpdateMode $stock_update_mode;

    public static function cacheKey(): string
    {
        return static::class.':'.(current_company_id() ?? 0);
    }

    public static function group(): string
    {
        return 'point_of_sale_inventory';
    }
}
