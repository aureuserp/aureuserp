<?php

namespace Webkul\Recruitment\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Recruitment\Models\JobPosition;
use Webkul\Security\Models\User;

class JobPositionPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_recruitment_job::position');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, JobPosition $jobPosition): bool
    {
        return $user->can('view_recruitment_job::position');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_recruitment_job::position');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, JobPosition $jobPosition): bool
    {
        return $user->can('update_recruitment_job::position');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, JobPosition $jobPosition): bool
    {
        return $user->can('delete_recruitment_job::position');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_recruitment_job::position');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, JobPosition $jobPosition): bool
    {
        return $user->can('force_delete_recruitment_job::position');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_recruitment_job::position');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, JobPosition $jobPosition): bool
    {
        return $user->can('restore_recruitment_job::position');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_recruitment_job::position');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_recruitment_job::position');
    }
}
