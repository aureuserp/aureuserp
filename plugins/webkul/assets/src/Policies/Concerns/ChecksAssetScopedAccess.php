<?php

namespace Webkul\Asset\Policies\Concerns;

use Webkul\Security\Enums\PermissionType;
use Webkul\Security\Models\User;
use Webkul\Security\Traits\HasScopedPermissions;

trait ChecksAssetScopedAccess
{
    use HasScopedPermissions;

    /**
     * @var array<string, array<int, int>>
     */
    protected static array $authorizedUserIdsCache = [];

    /**
     * @param  array<int, int|null>  $candidateUserIds
     */
    protected function hasScopedUserAccess(User $user, array $candidateUserIds): bool
    {
        if (! $user->resource_permission || $this->hasGlobalAccess($user)) {
            return true;
        }

        $candidates = array_values(array_filter($candidateUserIds, fn (?int $id): bool => $id !== null));

        if ($candidates === []) {
            return false;
        }

        return array_intersect($candidates, $this->getAuthorizedUserIds($user)) !== [];
    }

    /**
     * @return array<int, int>
     */
    protected function getAuthorizedUserIds(User $user): array
    {
        if ($user->resource_permission === PermissionType::INDIVIDUAL) {
            return [(int) $user->id];
        }

        if ($user->resource_permission === PermissionType::GROUP) {
            $cacheKey = $user->id.':'.$user->resource_permission->value;

            if (isset(static::$authorizedUserIdsCache[$cacheKey])) {
                return static::$authorizedUserIdsCache[$cacheKey];
            }

            $teamIds = $user->teams()->pluck('id');

            if ($teamIds->isEmpty()) {
                return static::$authorizedUserIdsCache[$cacheKey] = [(int) $user->id];
            }

            return static::$authorizedUserIdsCache[$cacheKey] = User::query()
                ->select('users.id')
                ->leftJoin('user_team', 'users.id', '=', 'user_team.user_id')
                ->whereIn('user_team.team_id', $teamIds)
                ->pluck('users.id')
                ->map(fn ($id): int => (int) $id)
                ->push((int) $user->id)
                ->unique()
                ->values()
                ->all();
        }

        return [(int) $user->id];
    }
}
