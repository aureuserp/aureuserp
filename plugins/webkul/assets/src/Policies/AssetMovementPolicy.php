<?php

namespace Webkul\Asset\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Asset\Models\AssetMovement;
use Webkul\Security\Models\User;

class AssetMovementPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_asset_asset::movement')
            || $user->can('view_any_asset_asset')
            || $user->can('view_any_asset_movement');
    }

    public function view(User $user, AssetMovement $movement): bool
    {
        return $user->can('view_asset_asset::movement')
            || $user->can('view_asset_asset')
            || $user->can('view_asset_movement');
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, AssetMovement $movement): bool
    {
        return false;
    }

    public function delete(User $user, AssetMovement $movement): bool
    {
        return false;
    }

    public function restore(User $user, AssetMovement $movement): bool
    {
        return false;
    }

    public function forceDelete(User $user, AssetMovement $movement): bool
    {
        return false;
    }
}
