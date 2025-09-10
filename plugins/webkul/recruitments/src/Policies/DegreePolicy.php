<?php

namespace Webkul\Recruitment\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Recruitment\Models\Degree;
use Webkul\Security\Models\User;

class DegreePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_degrees::degree');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Degree $degree): bool
    {
        return $user->can('view_degrees::degree');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_degrees::degree');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Degree $degree): bool
    {
        return $user->can('update_degrees::degree');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Degree $degree): bool
    {
        return $user->can('delete_degrees::degree');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_degrees::degree');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, Degree $degree): bool
    {
        return $user->can('force_delete_degrees::degree');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_degrees::degree');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, Degree $degree): bool
    {
        return $user->can('restore_degrees::degree');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_degrees::degree');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, Degree $degree): bool
    {
        return $user->can('replicate_degrees::degree');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_degrees::degree');
    }
}
