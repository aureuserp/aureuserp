<?php

namespace Webkul\Support\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Sequence;

class SequencePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can("view_any_sequence");
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Sequence $sequence): bool
    {
        return $user->can("view_sequence");
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can("create_sequence");
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Sequence $sequence): bool
    {
        return $user->can("update_sequence");
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Sequence $sequence): bool
    {
        return $user->can("delete_sequence");
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can("delete_any_sequence");
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, Sequence $sequence): bool
    {
        return $user->can("force_delete_sequence");
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can("force_delete_any_sequence");
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, Sequence $sequence): bool
    {
        return $user->can("restore_sequence");
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can("restore_any_sequence");
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can("reorder_sequence");
    }
}
