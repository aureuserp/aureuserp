<?php

namespace Webkul\Security\Settings;

use Spatie\LaravelSettings\Settings;

class CurrencySettings extends Settings
{
    public ?int $default_currency_id;

    public static function group(): string
    {
        return 'currency';
    }
}
