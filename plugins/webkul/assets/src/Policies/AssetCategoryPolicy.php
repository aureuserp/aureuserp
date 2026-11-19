<?php

namespace Webkul\Asset\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Asset\Models\AssetCategory;
use Webkul\Asset\Policies\Concerns\ChecksAssetScopedAccess;
use Webkul\Security\Models\User;

class AssetCategoryPolicy
{
    use ChecksAssetScopedAccess, HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_asset_asset::category') || $user->can('view_any_asset_product');
    }

    public function view(User $user, AssetCategory $category): bool
    {
        return $user->can('view_asset_asset::category') || $user->can('view_asset_product');
    }

    public function create(User $user): bool
    {
        return $user->can('create_asset_asset::category') || $user->can('create_asset_product');
    }

    public function update(User $user, AssetCategory $category): bool
    {
        if (! $user->can('update_asset_asset::category') && ! $user->can('update_asset_product')) {
            return false;
        }

        return $this->hasCategoryScopeAccess($user, $category);
    }

    public function delete(User $user, AssetCategory $category): bool
    {
        if (! $user->can('delete_asset_asset::category') && ! $user->can('delete_asset_product')) {
            return false;
        }

        return $this->hasCategoryScopeAccess($user, $category);
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_asset_asset::category') || $user->can('delete_any_asset_product');
    }

    protected function hasCategoryScopeAccess(User $user, AssetCategory $category): bool
    {
        return $this->hasScopedUserAccess($user, [
            $category->creator_id,
        ]);
    }
}
