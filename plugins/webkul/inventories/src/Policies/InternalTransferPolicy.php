<?php

namespace Webkul\Inventory\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Inventory\Models\InternalTransfer;
use Webkul\Security\Models\User;
use Webkul\Security\Traits\HasScopedPermissions;

class InternalTransferPolicy
{
    use HandlesAuthorization, HasScopedPermissions;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_internals::internal');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, InternalTransfer $internalTransfer): bool
    {
        return $user->can('view_internals::internal');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_internals::internal');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, InternalTransfer $internalTransfer): bool
    {
        if (! $user->can('update_internals::internal')) {
            return false;
        }

        return $this->hasAccess($user, $internalTransfer);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, InternalTransfer $internalTransfer): bool
    {
        if (! $user->can('delete_internals::internal')) {
            return false;
        }

        return $this->hasAccess($user, $internalTransfer);
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_internals::internal');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, InternalTransfer $internalTransfer): bool
    {
        if (! $user->can('force_delete_internals::internal')) {
            return false;
        }

        return $this->hasAccess($user, $internalTransfer);
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_internals::internal');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, InternalTransfer $internalTransfer): bool
    {
        if (! $user->can('restore_internals::internal')) {
            return false;
        }

        return $this->hasAccess($user, $internalTransfer);
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_internals::internal');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, InternalTransfer $internalTransfer): bool
    {
        if (! $user->can('replicate_internals::internal')) {
            return false;
        }

        return $this->hasAccess($user, $internalTransfer);
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_internals::internal');
    }
}
