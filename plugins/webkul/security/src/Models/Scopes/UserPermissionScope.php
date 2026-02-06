<?php

namespace Webkul\Security\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;
use Webkul\Security\Enums\PermissionType;

class UserPermissionScope implements Scope
{
    protected $ownerRelation;

    /**
     * Create a new scope instance.
     */
    public function __construct(string $ownerRelation)
    {
        $this->ownerRelation = $ownerRelation;
    }

    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        $user = Auth::user();

        if ($user->resource_permission === PermissionType::GLOBAL) {
            return;
        }

        if ($user->resource_permission === PermissionType::INDIVIDUAL) {
            $builder->whereHas($this->ownerRelation, function ($q) use ($user) {
                $q->where('users.id', $user->id);
            });

            $builder->orWhereHas('followers', function ($q) use ($user) {
                $q->where('chatter_followers.partner_id', $user->partner_id);
            });
        }

        if ($user->resource_permission === PermissionType::GROUP) {
            $teamIds = $user->teams()->pluck('id');

            $builder->whereHas("$this->ownerRelation.teams", function ($q) use ($teamIds) {
                $q->whereIn('teams.id', $teamIds);
            });
        }
    }
}
