<?php

namespace Webkul\Crm\Models;

use Illuminate\Database\Eloquent\Builder;
use Webkul\Support\Models\ActivityType as BaseActivityType;

class ActivityType extends BaseActivityType
{
    public const PLUGINS = ['support', 'crm'];

    public function scopeAvailableInCRM(Builder $query): Builder
    {
        return $query->whereIn('plugin', self::PLUGINS);
    }
}
