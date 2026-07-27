<?php

namespace Webkul\Support\Models\Concerns;

trait HasContributedAttributes
{
    protected static array $contributedFillable = [];

    protected static array $contributedCasts = [];

    public static function contributeFillable(array $attributes): void
    {
        static::$contributedFillable = array_values(array_unique([
            ...static::$contributedFillable,
            ...$attributes,
        ]));
    }

    public static function contributeCasts(array $casts): void
    {
        static::$contributedCasts = array_merge(static::$contributedCasts, $casts);
    }

    public function initializeHasContributedAttributes(): void
    {
        if (! empty(static::$contributedFillable)) {
            $this->mergeFillable(static::$contributedFillable);
        }

        if (! empty(static::$contributedCasts)) {
            $this->mergeCasts(static::$contributedCasts);
        }
    }
}
