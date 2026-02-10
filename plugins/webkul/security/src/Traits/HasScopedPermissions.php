<?php

// 1. Enhanced HasScopedPermissions Trait

namespace Webkul\Security\Traits;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Webkul\Security\Enums\PermissionType;
use Webkul\Security\Models\User;

trait HasScopedPermissions
{
    /**
     * Check if the user has global access to any resource.
     */
    protected function hasGlobalAccess(User $user): bool
    {
        return $user->resource_permission === PermissionType::GLOBAL;
    }

    /**
     * Check if the user has group access to resources of users in the same group.
     */
    protected function hasGroupAccess(User $user, Model $model, string $ownerAttribute = 'user'): bool
    {
        if ($user->resource_permission !== PermissionType::GROUP) {
            return false;
        }

        $owner = $model->{$ownerAttribute};

        if (! $owner) {
            return false;
        }

        if ($owner instanceof Collection) {
            if ($owner->pluck('id')->contains($user->id)) {
                return true;
            }

            $ownerTeamIds = $owner->pluck('teams')->flatten()->pluck('id');
        } else {
            if ($owner->id === $user->id) {
                return true;
            }

            $ownerTeamIds = $owner->teams->pluck('id');
        }

        $userTeamIds = $user->teams->pluck('id');

        return $ownerTeamIds->intersect($userTeamIds)->isNotEmpty();
    }

    /**
     * Check if the user has individual access to their own resources only.
     */
    protected function hasIndividualAccess(User $user, Model $model, string $ownerAttribute = 'user'): bool
    {
        if ($user->resource_permission !== PermissionType::INDIVIDUAL) {
            return false;
        }

        $owner = $model->{$ownerAttribute};

        if (! $owner) {
            return false;
        }

        return $owner instanceof Collection
            ? $owner->pluck('id')->contains($user->id)
            : $owner->id === $user->id;
    }

    /**
     * Main access method that combines all permissions.
     */
    protected function hasAccess(User $user, Model $model, string $ownerAttribute = 'user'): bool
    {
        return $this->hasGlobalAccess($user)
            || $this->hasGroupAccess($user, $model, $ownerAttribute)
            || $this->hasIndividualAccess($user, $model, $ownerAttribute);
    }
}
