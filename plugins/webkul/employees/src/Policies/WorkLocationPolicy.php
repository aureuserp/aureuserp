<?php

namespace Webkul\Employee\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Employee\Models\WorkLocation;
use Webkul\Security\Models\User;

class WorkLocationPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_work::locations::work::location');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, WorkLocation $workLocation): bool
    {
        return $user->can('view_work::locations::work::location');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_work::locations::work::location');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, WorkLocation $workLocation): bool
    {
        return $user->can('update_work::locations::work::location');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, WorkLocation $workLocation): bool
    {
        return $user->can('delete_work::locations::work::location');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_work::locations::work::location');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, WorkLocation $workLocation): bool
    {
        return $user->can('force_delete_work::locations::work::location');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_work::locations::work::location');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, WorkLocation $workLocation): bool
    {
        return $user->can('restore_work::locations::work::location');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_work::locations::work::location');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, WorkLocation $workLocation): bool
    {
        return $user->can('replicate_work::locations::work::location');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_work::locations::work::location');
    }
}
