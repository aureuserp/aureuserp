<?php

namespace Webkul\Crm\Settings;

use Spatie\LaravelSettings\Settings;

class OutgoingMailSettings extends Settings
{
    public bool $is_active;

    public ?string $host;

    public int $port;

    public string $encryption;

    public ?string $username;

    public ?string $password;

    public ?string $from_address;

    public ?string $from_name;

    public static function group(): string
    {
        return 'crm_outgoing_mail';
    }

    public static function encrypted(): array
    {
        return ['password'];
    }
}
