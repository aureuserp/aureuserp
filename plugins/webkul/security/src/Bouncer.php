<?php

namespace Webkul\Security;

use Webkul\Security\Enums\PermissionType;
use Webkul\Security\Models\User;

class Bouncer
{
    /**
     * Cached authorized user IDs.
     */
    protected static ?array $authorizedUserIdsCache = null;

    /**
     * Return user IDs authorized for the current user.
     */
    public function getAuthorizedUserIds(): ?array
    {
        if (static::$authorizedUserIdsCache !== null) {
            return static::$authorizedUserIdsCache;
        }

        $user = filament()->auth()->user();

        if ($user->resource_permission == PermissionType::GLOBAL) {
            static::$authorizedUserIdsCache = null;
        } elseif ($user->resource_permission == PermissionType::GROUP) {
            static::$authorizedUserIdsCache = $this->getCurrentAccessibleUserIds($user);
        } else {
            static::$authorizedUserIdsCache = [$user->id];
        }

        return static::$authorizedUserIdsCache;
    }

    /**
     * Get user IDs of the current user's groups.
     */
    protected function getCurrentAccessibleUserIds(User $user): array
    {
        return User::query()
            ->select('users.id')
            ->leftJoin('user_team', 'users.id', '=', 'user_team.user_id')
            ->leftJoin('teams', 'user_team.team_id', '=', 'teams.id')
            ->whereIn('teams.id', $user->teams()->pluck('id'))
            ->pluck('users.id')
            ->toArray();
    }
}
