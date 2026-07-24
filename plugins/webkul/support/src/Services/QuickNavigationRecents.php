<?php

namespace Webkul\Support\Services;

use Filament\Facades\Filament;
use Illuminate\Support\Facades\Session;

class QuickNavigationRecents
{
    protected const LIMIT = 6;

    /**
     * @return array<int, array{url: string, title: string}>
     */
    public static function get(): array
    {
        return array_values(array_filter(
            Session::get(static::key(), []),
            fn ($item): bool => is_array($item) && isset($item['url'], $item['title']),
        ));
    }

    public static function push(string $url, string $title): void
    {
        $items = array_values(array_filter(
            static::get(),
            fn (array $existing): bool => $existing['url'] !== $url,
        ));

        array_unshift($items, ['url' => $url, 'title' => $title]);

        Session::put(static::key(), array_slice($items, 0, static::LIMIT));
    }

    protected static function key(): string
    {
        return 'quick-navigation.recent.'.(Filament::auth()->id() ?? 'guest');
    }
}
