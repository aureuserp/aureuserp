<?php

namespace Webkul\FullCalendar\Concerns;

use Illuminate\Support\Arr;
use Webkul\FullCalendar\FullCalendarPlugin;

trait CanBeConfigured
{
    public function config(): array
    {
        return [];
    }

    public function getConfig(): array
    {
        return static::mergeConfig(
            FullCalendarPlugin::get()->getConfig(),
            $this->config()
        );
    }

    protected static function mergeConfig(array $base, array $override): array
    {
        foreach ($override as $key => $value) {
            if (
                is_array($value)
                && Arr::exists($base, $key) && is_array($base[$key])
            ) {
                $base[$key] = static::mergeConfig($base[$key], $value);
            } else {
                $base[$key] = $value;
            }
        }

        return $base;
    }
}
