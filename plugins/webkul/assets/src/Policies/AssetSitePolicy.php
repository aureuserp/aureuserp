<?php

namespace Webkul\Asset\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Asset\Models\AssetSite;
use Webkul\Asset\Policies\Concerns\ChecksAssetScopedAccess;
use Webkul\Security\Models\User;

class AssetSitePolicy
{
    use ChecksAssetScopedAccess, HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_asset_asset::site');
    }

    public function view(User $user, AssetSite $site): bool
    {
        return $user->can('view_asset_asset::site');
    }

    public function create(User $user): bool
    {
        return $user->can('create_asset_asset::site');
    }

    public function update(User $user, AssetSite $site): bool
    {
        if (! $user->can('update_asset_asset::site')) {
            return false;
        }

        return $this->hasSiteScopeAccess($user, $site);
    }

    public function delete(User $user, AssetSite $site): bool
    {
        if (! $user->can('delete_asset_asset::site')) {
            return false;
        }

        return $this->hasSiteScopeAccess($user, $site);
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_asset_asset::site');
    }

    public function forceDelete(User $user, AssetSite $site): bool
    {
        if (! $user->can('force_delete_asset_asset::site')) {
            return false;
        }

        return $this->hasSiteScopeAccess($user, $site);
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_asset_asset::site');
    }

    public function restore(User $user, AssetSite $site): bool
    {
        if (! $user->can('restore_asset_asset::site')) {
            return false;
        }

        return $this->hasSiteScopeAccess($user, $site);
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_asset_asset::site');
    }

    protected function hasSiteScopeAccess(User $user, AssetSite $site): bool
    {
        return $this->hasScopedUserAccess($user, [
            $site->creator_id,
            $site->defaultCustodian?->user_id,
        ]);
    }
}
