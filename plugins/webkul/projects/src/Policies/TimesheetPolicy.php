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
        return $user->can('view_any_timesheet');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_timesheet');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Timesheet $timesheet): bool
    {
        if (! $user->can('update_timesheet')) {
            return false;
        }

        return $this->hasAccess($user, $timesheet, 'users');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Timesheet $timesheet): bool
    {
        if (! $user->can('delete_timesheet')) {
            return false;
        }

        return $this->hasAccess($user, $timesheet, 'users');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_timesheet');
    }
}
