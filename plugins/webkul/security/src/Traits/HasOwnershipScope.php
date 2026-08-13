<?php

namespace Webkul\Security\Traits;

use Illuminate\Database\Eloquent\Builder;
use Webkul\Security\Models\Scopes\OwnershipScope;
use Webkul\Security\Support\OwnerSource;

trait HasOwnershipScope
{
    public static function bootHasOwnershipScope(): void
    {
        if (static::ownershipScopeIsGlobal()) {
            static::addGlobalScope(new OwnershipScope);
        }
    }

    protected static function ownershipScopeIsGlobal(): bool
    {
        return true;
    }

    public function scopeOwnership(Builder $query): Builder
    {
        (new OwnershipScope)->apply($query, $this);

        return $query;
    }

    public function ownershipSources(): array
    {
        return [
            OwnerSource::column('creator_id'),
            OwnerSource::column('user_id'),
        ];
    }
}
