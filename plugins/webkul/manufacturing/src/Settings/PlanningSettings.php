<?php

namespace Webkul\Manufacturing\Settings;

use Spatie\LaravelSettings\Settings;

class PlanningSettings extends Settings
{
    public int $manufacturing_lead;

    public static function group(): string
    {
        return 'manufacturing_planning';
    }
}
