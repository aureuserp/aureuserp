<?php

namespace Webkul\TimeOff\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Security\Models\User;
use Webkul\Security\Traits\HasScopedPermissions;
use Webkul\TimeOff\Models\Leave;

class LeavePolicy
{
    use HandlesAuthorization, HasScopedPermissions;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_time::off');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Leave $leave): bool
    {
        return $user->can('view_time::off');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_time::off');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Leave $leave): bool
    {
        if (! $user->can('update_time::off')) {
            return false;
        }

        return $this->hasAccess($user, $leave, 'employee');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Leave $leave): bool
    {
        if (! $user->can('delete_time::off')) {
            return false;
        }

        return $this->hasAccess($user, $leave, 'employee');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_time::off');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, Leave $leave): bool
    {
        if (! $user->can('force_delete_time::off')) {
            return false;
        }

        return $this->hasAccess($user, $leave, 'employee');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_time::off');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, Leave $leave): bool
    {
        if (! $user->can('restore_time::off')) {
            return false;
        }

        return $this->hasAccess($user, $leave, 'employee');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_time::off');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, Leave $leave): bool
    {
        if (! $user->can('replicate_time::off')) {
            return false;
        }

        return $this->hasAccess($user, $leave, 'employee');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_time::off');
    }
}
