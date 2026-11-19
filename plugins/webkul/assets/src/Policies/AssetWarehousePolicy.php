<?php

namespace Webkul\Asset\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Asset\Models\AssetWarehouse;
use Webkul\Asset\Policies\Concerns\ChecksAssetScopedAccess;
use Webkul\Security\Models\User;

class AssetWarehousePolicy
{
    use ChecksAssetScopedAccess, HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_asset_asset::warehouse');
    }

    public function view(User $user, AssetWarehouse $warehouse): bool
    {
        return $user->can('view_asset_asset::warehouse');
    }

    public function create(User $user): bool
    {
        return $user->can('create_asset_asset::warehouse');
    }

    public function update(User $user, AssetWarehouse $warehouse): bool
    {
        if (! $user->can('update_asset_asset::warehouse')) {
            return false;
        }

        return $this->hasWarehouseScopeAccess($user, $warehouse);
    }

    public function delete(User $user, AssetWarehouse $warehouse): bool
    {
        if (! $user->can('delete_asset_asset::warehouse')) {
            return false;
        }

        return $this->hasWarehouseScopeAccess($user, $warehouse);
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_asset_asset::warehouse');
    }

    public function forceDelete(User $user, AssetWarehouse $warehouse): bool
    {
        if (! $user->can('force_delete_asset_asset::warehouse')) {
            return false;
        }

        return $this->hasWarehouseScopeAccess($user, $warehouse);
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_asset_asset::warehouse');
    }

    public function restore(User $user, AssetWarehouse $warehouse): bool
    {
        if (! $user->can('restore_asset_asset::warehouse')) {
            return false;
        }

        return $this->hasWarehouseScopeAccess($user, $warehouse);
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_asset_asset::warehouse');
    }

    protected function hasWarehouseScopeAccess(User $user, AssetWarehouse $warehouse): bool
    {
        return $this->hasScopedUserAccess($user, [
            $warehouse->creator_id,
            $warehouse->custodian?->user_id,
        ]);
    }
}
