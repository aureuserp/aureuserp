<?php

namespace Webkul\Support\Settings;

use Spatie\LaravelSettings\Settings;

class BrandSettings extends Settings
{
    public ?string $primary_color;

    public ?string $gray_color;

    public ?string $danger_color;

    public ?string $info_color;

    public ?string $success_color;

    public ?string $warning_color;

    public ?string $light_logo;

    public ?string $dark_logo;

    public ?string $favicon;

    public ?string $logo_height;

    public static function group(): string
    {
        return 'branding';
    }
}
