<?php

namespace Webkul\Support\Filament\Concerns;

trait HasTranslationFallback
{
    protected function applyTranslationFallback(array $data, array $attributes): array
    {
        $fallbackLocale = config('app.fallback_locale', 'en');

        if ($this->activeLocale === $fallbackLocale) {
            return $data;
        }

        $fallbackData = $this->otherLocaleData[$fallbackLocale] ?? [];

        foreach ($attributes as $attribute) {
            if (blank($data[$attribute] ?? null) && filled($fallbackData[$attribute] ?? null)) {
                $data[$attribute] = $fallbackData[$attribute];
            }
        }

        return $data;
    }
}
