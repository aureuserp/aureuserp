<?php

namespace Webkul\Project\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Project\Models\Timesheet;
use Webkul\Security\Models\User;
use Webkul\Security\Traits\HasScopedPermissions;

class TimesheetPolicy
{
    use HandlesAuthorization, HasScopedPermissions;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_timesheets::timesheet');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Timesheet $timesheet): bool
    {
        return $user->can('view_timesheets::timesheet');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_timesheets::timesheet');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Timesheet $timesheet): bool
    {
        if (! $user->can('update_timesheets::timesheet')) {
            return false;
        }

        return $this->hasAccess($user, $timesheet, 'users');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Timesheet $timesheet): bool
    {
        if (! $user->can('delete_timesheets::timesheet')) {
            return false;
        }

        return $this->hasAccess($user, $timesheet, 'users');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_timesheets::timesheet');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, Timesheet $timesheet): bool
    {
        if (! $user->can('force_delete_timesheets::timesheet')) {
            return false;
        }

        return $this->hasAccess($user, $timesheet, 'users');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_timesheets::timesheet');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, Timesheet $timesheet): bool
    {
        if (! $user->can('restore_timesheets::timesheet')) {
            return false;
        }

        return $this->hasAccess($user, $timesheet, 'users');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_timesheets::timesheet');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, Timesheet $timesheet): bool
    {
        if (! $user->can('replicate_timesheets::timesheet')) {
            return false;
        }

        return $this->hasAccess($user, $timesheet, 'users');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_timesheets::timesheet');
    }
}
