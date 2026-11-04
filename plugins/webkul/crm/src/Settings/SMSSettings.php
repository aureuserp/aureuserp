<?php

namespace Webkul\Crm\Settings;

use Spatie\LaravelSettings\Settings;

class SMSSettings extends Settings
{
    public bool $is_active;

    public ?string $auth_key;

    public ?string $sender_id;

    public string $route;

    public static function group(): string
    {
        return 'crm_sms';
    }

    public static function encrypted(): array
    {
        return ['auth_key'];
    }
}
