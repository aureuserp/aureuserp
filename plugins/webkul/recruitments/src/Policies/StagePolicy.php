<?php

namespace Webkul\Recruitment\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Recruitment\Models\Stage;
use Webkul\Security\Models\User;

class StagePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_stages::stage');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Stage $stage): bool
    {
        return $user->can('view_stages::stage');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_stages::stage');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Stage $stage): bool
    {
        return $user->can('update_stages::stage');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Stage $stage): bool
    {
        return $user->can('delete_stages::stage');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_stages::stage');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, Stage $stage): bool
    {
        return $user->can('force_delete_stages::stage');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_stages::stage');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, Stage $stage): bool
    {
        return $user->can('restore_stages::stage');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_stages::stage');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, Stage $stage): bool
    {
        return $user->can('replicate_stages::stage');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_stages::stage');
    }
}
