<?php

namespace Webkul\Inventory\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Inventory\Models\StorageCategory;
use Webkul\Security\Models\User;

class StorageCategoryPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_storage::category');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, StorageCategory $storageCategory): bool
    {
        return $user->can('view_storage::category');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_storage::category');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, StorageCategory $storageCategory): bool
    {
        return $user->can('update_storage::category');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, StorageCategory $storageCategory): bool
    {
        return $user->can('delete_storage::category');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_storage::category');
    }
}
