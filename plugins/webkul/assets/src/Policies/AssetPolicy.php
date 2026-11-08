<?php

namespace Webkul\Asset\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Asset\Models\Asset;
use Webkul\Asset\Policies\Concerns\ChecksAssetScopedAccess;
use Webkul\Asset\Settings\AssetSettings;
use Webkul\Security\Enums\PermissionType;
use Webkul\Security\Models\User;

class AssetPolicy
{
    use ChecksAssetScopedAccess, HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_asset_asset');
    }

    public function view(User $user, Asset $asset): bool
    {
        if (! $user->can('view_asset_asset')) {
            return false;
        }

        return $this->hasAssetScopeAccess($user, $asset);
    }

    public function create(User $user): bool
    {
        return $user->can('create_asset_asset');
    }

    public function update(User $user, Asset $asset): bool
    {
        if (! $user->can('update_asset_asset')) {
            return false;
        }

        return $this->hasAssetScopeAccess($user, $asset);
    }

    public function delete(User $user, Asset $asset): bool
    {
        if (! $user->can('delete_asset_asset')) {
            return false;
        }

        return $this->hasAssetScopeAccess($user, $asset);
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_asset_asset');
    }

    public function forceDelete(User $user, Asset $asset): bool
    {
        if (! $user->can('force_delete_asset_asset')) {
            return false;
        }

        return $this->hasAssetScopeAccess($user, $asset);
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_asset_asset');
    }

    public function restore(User $user, Asset $asset): bool
    {
        if (! $user->can('restore_asset_asset')) {
            return false;
        }

        return $this->hasAssetScopeAccess($user, $asset);
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_asset_asset');
    }

    public function issue(User $user, Asset $asset): bool
    {
        return $this->update($user, $asset);
    }

    public function transfer(User $user, Asset $asset): bool
    {
        if ($asset->isConsumable()) {
            return false;
        }

        return $this->update($user, $asset);
    }

    public function return(User $user, Asset $asset): bool
    {
        if ($asset->isConsumable()) {
            return false;
        }

        return $this->update($user, $asset);
    }

    public function terminate(User $user, Asset $asset): bool
    {
        if (! $this->delete($user, $asset)) {
            return false;
        }

        $settings = app(AssetSettings::class);

        if (! empty($settings->restrict_termination_to_managers)) {
            $threshold = $settings->termination_approval_cost_threshold ?? null;

            $requiresManager = blank($threshold)
                || (float) $threshold <= 0.0
                || (float) $asset->cost >= (float) $threshold;

            if ($requiresManager) {
                return in_array($user->resource_permission, [
                    PermissionType::GLOBAL,
                    PermissionType::GROUP,
                ], true);
            }
        }

        return true;
    }

    protected function hasAssetScopeAccess(User $user, Asset $asset): bool
    {
        return $this->hasScopedUserAccess($user, [
            $asset->creator_id,
            $asset->currentEmployee?->user_id,
            $asset->warehouse?->custodian?->user_id,
            $asset->currentSite?->defaultCustodian?->user_id,
        ]);
    }
}
