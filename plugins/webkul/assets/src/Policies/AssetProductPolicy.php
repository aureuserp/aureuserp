<?php

namespace Webkul\Asset\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Asset\Models\AssetProduct;
use Webkul\Asset\Policies\Concerns\ChecksAssetScopedAccess;
use Webkul\Security\Models\User;

class AssetProductPolicy
{
    use ChecksAssetScopedAccess, HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_asset_product') || $user->can('view_any_asset_asset');
    }

    public function view(User $user, AssetProduct $product): bool
    {
        return $user->can('view_asset_product') || $user->can('view_asset_asset');
    }

    public function create(User $user): bool
    {
        return $user->can('create_asset_product');
    }

    public function update(User $user, AssetProduct $product): bool
    {
        if (! $user->can('update_asset_product')) {
            return false;
        }

        return $this->hasProductScopeAccess($user, $product);
    }

    public function delete(User $user, AssetProduct $product): bool
    {
        if (! $user->can('delete_asset_product')) {
            return false;
        }

        return $this->hasProductScopeAccess($user, $product);
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_asset_product');
    }

    public function forceDelete(User $user, AssetProduct $product): bool
    {
        if (! $user->can('force_delete_asset_product')) {
            return false;
        }

        return $this->hasProductScopeAccess($user, $product);
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_asset_product');
    }

    public function restore(User $user, AssetProduct $product): bool
    {
        if (! $user->can('restore_asset_product')) {
            return false;
        }

        return $this->hasProductScopeAccess($user, $product);
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_asset_product');
    }

    protected function hasProductScopeAccess(User $user, AssetProduct $product): bool
    {
        return $this->hasScopedUserAccess($user, [
            $product->creator_id,
        ]);
    }
}
