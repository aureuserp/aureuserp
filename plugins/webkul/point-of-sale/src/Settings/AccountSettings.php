<?php

namespace Webkul\PointOfSale\Settings;

use Spatie\LaravelSettings\Settings;

class AccountSettings extends Settings
{
    public ?int $receivable_account_id;

    public ?int $stock_output_account_id;

    public ?int $balancing_account_id;

    public bool $enable_cogs;

    public bool $allow_balancing_line;

    public static function cacheKey(): string
    {
        return static::class.':'.(current_company_id() ?? 0);
    }

    public static function group(): string
    {
        return 'point_of_sale_account';
    }
}
