<?php

namespace Webkul\Inventory\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Inventory\Models\Dropship;
use Webkul\Security\Models\User;
use Webkul\Security\Traits\HasScopedPermissions;

class DropshipPolicy
{
    use HandlesAuthorization, HasScopedPermissions;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_dropships::dropship');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Dropship $dropship): bool
    {
        return $user->can('view_dropships::dropship');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_dropships::dropship');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Dropship $dropship): bool
    {
        if (! $user->can('update_dropships::dropship')) {
            return false;
        }

        return $this->hasAccess($user, $dropship);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Dropship $dropship): bool
    {
        if (! $user->can('delete_dropships::dropship')) {
            return false;
        }

        return $this->hasAccess($user, $dropship);
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_dropships::dropship');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, Dropship $dropship): bool
    {
        if (! $user->can('force_delete_dropships::dropship')) {
            return false;
        }

        return $this->hasAccess($user, $dropship);
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_dropships::dropship');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, Dropship $dropship): bool
    {
        if (! $user->can('restore_dropships::dropship')) {
            return false;
        }

        return $this->hasAccess($user, $dropship);
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_dropships::dropship');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, Dropship $dropship): bool
    {
        if (! $user->can('replicate_dropships::dropship')) {
            return false;
        }

        return $this->hasAccess($user, $dropship);
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_dropships::dropship');
    }
}
