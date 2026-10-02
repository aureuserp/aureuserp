<?php

namespace Webkul\Attendance\Writers;

use InvalidArgumentException;

class WriterRegistry
{
    /**
     * Canonical writer slug shape: lowercase alphanumerics, with '-' or '_'
     * only between alphanumerics (no leading/trailing separators).
     */
    public const SLUG_PATTERN = '[a-z0-9]+(?:[-_][a-z0-9]+)*';

    /**
     * Registered attendance writers: slug => label resolver.
     *
     * The resolver receives the numeric reference id (or null for a bare
     * writer key such as 'remote') and returns the current human name, or
     * null when it cannot be resolved (record deleted, writer disabled).
     * A writer without any resolver is valid too: rows then fall back to
     * the stored source_label snapshot.
     *
     * @var array<string, ?callable(?int): ?string>
     */
    protected static array $writers = [];

    public static function register(string $slug, ?callable $labelResolver = null): void
    {
        $slug = strtolower($slug);

        if (! preg_match('/^'.self::SLUG_PATTERN.'$/', $slug)) {
            throw new InvalidArgumentException("Invalid attendance writer slug [{$slug}].");
        }

        static::$writers[$slug] = $labelResolver;
    }

    public static function flush(): void
    {
        static::$writers = [];
    }

    public static function isRegistered(string $slug): bool
    {
        return array_key_exists(strtolower($slug), static::$writers);
    }

    public static function resolveLabel(string $slug, ?int $referenceId = null): ?string
    {
        $resolver = static::$writers[strtolower($slug)] ?? null;

        if ($resolver === null) {
            return null;
        }

        return $resolver($referenceId);
    }

    /**
     * @return list<string>
     */
    public static function slugs(): array
    {
        return array_keys(static::$writers);
    }
}
