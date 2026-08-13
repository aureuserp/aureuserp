<?php

namespace Webkul\Chatter\Support;

use Filament\Forms\Components\RichEditor\MentionProvider;
use Filament\Forms\Components\RichEditor\RichContentRenderer;
use Throwable;
use Webkul\Security\Filament\Resources\UserResource;
use Webkul\Security\Models\User;

class ChatterMentions
{
    public static function provider(): MentionProvider
    {
        return MentionProvider::make('@')
            ->getSearchResultsUsing(fn (string $search): array => User::query()
                ->where('is_active', true)
                ->whereLike('name', "%{$search}%")
                ->orderBy('name')
                ->limit(10)
                ->pluck('name', 'id')
                ->all())
            ->getLabelsUsing(fn (array $ids): array => User::query()
                ->whereIn('id', $ids)
                ->pluck('name', 'id')
                ->all())
            ->extraAttributes(['class' => 'fi-chatter-mention font-medium'])
            ->url(fn (string $id): ?string => static::userUrl($id));
    }

    public static function render(?string $body): string
    {
        if (blank($body)) {
            return '';
        }

        try {
            $html = RichContentRenderer::make($body)
                ->mentions([static::provider()])
                ->toHtml();

            return static::styleMentions($html);
        } catch (Throwable $e) {
            report($e);

            return (string) str($body)->sanitizeHtml();
        }
    }

    protected static function styleMentions(string $html): string
    {
        return preg_replace_callback(
            '/<(a|span)\b([^>]*\bdata-type=["\']mention["\'][^>]*)>/i',
            function (array $match): string {
                if (str_contains($match[2], 'style=')) {
                    return $match[0];
                }

                return '<'.$match[1].$match[2].' style="color:var(--primary-500);font-weight:500">';
            },
            $html,
        ) ?? $html;
    }

    protected static function userUrl(string $id): ?string
    {
        try {
            return UserResource::getUrl('view', ['record' => $id]);
        } catch (Throwable) {
            return null;
        }
    }

    public static function extractUserIds(?string $body): array
    {
        if (blank($body)) {
            return [];
        }

        if (! preg_match_all('/<(?:span|a)[^>]*data-type=["\']mention["\'][^>]*>/i', $body, $tags)) {
            return [];
        }

        $ids = [];

        foreach ($tags[0] as $tag) {
            if (preg_match('/data-id=["\'](\d+)["\']/', $tag, $match)) {
                $ids[] = (int) $match[1];
            }
        }

        return array_values(array_unique($ids));
    }
}
