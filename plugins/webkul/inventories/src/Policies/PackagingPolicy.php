<?php

namespace Webkul\Inventory\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Inventory\Models\Packaging;
use Webkul\Security\Models\User;

class PackagingPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_packagings::packaging');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Packaging $packaging): bool
    {
        return $user->can('view_packagings::packaging');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_packagings::packaging');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Packaging $packaging): bool
    {
        return $user->can('update_packagings::packaging');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Packaging $packaging): bool
    {
        return $user->can('delete_packagings::packaging');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_packagings::packaging');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, Packaging $packaging): bool
    {
        return $user->can('force_delete_packagings::packaging');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_packagings::packaging');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, Packaging $packaging): bool
    {
        return $user->can('restore_packagings::packaging');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_packagings::packaging');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, Packaging $packaging): bool
    {
        return $user->can('replicate_packagings::packaging');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_packagings::packaging');
    }
}
