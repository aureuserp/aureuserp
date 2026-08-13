<?php

namespace Webkul\Support\Services;

use Filament\Facades\Filament;
use Webkul\Support\Models\QuickNavigationFavorite;

class QuickNavigationFavorites
{
    /**
     * @return array<int, array{url: string, label: string}>
     */
    public static function get(): array
    {
        $userId = Filament::auth()->id();

        if ($userId === null) {
            return [];
        }

        return QuickNavigationFavorite::query()
            ->where('user_id', $userId)
            ->orderBy('sort')
            ->get(['url', 'label'])
            ->map(fn (QuickNavigationFavorite $favorite): array => [
                'url'   => $favorite->url,
                'label' => $favorite->label,
            ])
            ->all();
    }

    /**
     * @return array<int, string>
     */
    public static function urls(): array
    {
        return array_column(static::get(), 'url');
    }

    public static function toggle(string $url, string $label): void
    {
        $userId = Filament::auth()->id();

        if ($userId === null) {
            return;
        }

        $existing = QuickNavigationFavorite::query()
            ->where('user_id', $userId)
            ->where('url', $url)
            ->first();

        if ($existing !== null) {
            $existing->delete();

            return;
        }

        QuickNavigationFavorite::query()->create([
            'user_id' => $userId,
            'label'   => $label,
            'url'     => $url,
            'sort'    => (int) QuickNavigationFavorite::query()->where('user_id', $userId)->max('sort') + 1,
        ]);
    }
}
