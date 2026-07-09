<?php

namespace Webkul\Support;

class SettingsRegistry
{
    protected array $resolved = [];

    public function get(string $settings): object
    {
        $settings = ltrim($settings, '\\');

        return $this->resolved[$settings] ??= app($settings);
    }
}
