<?php

namespace Webkul\Inventory\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Inventory\Models\Lot;
use Webkul\Security\Models\User;

class LotPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_lots::lot');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Lot $lot): bool
    {
        return $user->can('view_lots::lot');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_lots::lot');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Lot $lot): bool
    {
        return $user->can('update_lots::lot');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Lot $lot): bool
    {
        return $user->can('delete_lots::lot');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_lots::lot');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, Lot $lot): bool
    {
        return $user->can('force_delete_lots::lot');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_lots::lot');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, Lot $lot): bool
    {
        return $user->can('restore_lots::lot');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_lots::lot');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, Lot $lot): bool
    {
        return $user->can('replicate_lots::lot');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_lots::lot');
    }
}
