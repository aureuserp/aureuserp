<?php

namespace Webkul\Employee\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Employee\Models\ActivityPlan;
use Webkul\Security\Models\User;

class ActivityPlanPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_employee_activity::plan');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ActivityPlan $activityPlan): bool
    {
        return $user->can('view_employee_activity::plan');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_employee_activity::plan');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ActivityPlan $activityPlan): bool
    {
        return $user->can('update_employee_activity::plan');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ActivityPlan $activityPlan): bool
    {
        return $user->can('delete_employee_activity::plan');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_employee_activity::plan');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, ActivityPlan $activityPlan): bool
    {
        return $user->can('force_delete_employee_activity::plan');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_employee_activity::plan');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, ActivityPlan $activityPlan): bool
    {
        return $user->can('restore_employee_activity::plan');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_employee_activity::plan');
    }
}
