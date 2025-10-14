<?php

namespace Webkul\TimeOff\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Security\Models\User;
use Webkul\TimeOff\Models\LeaveType;

class LeaveTypePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_leave::type');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, LeaveType $leaveType): bool
    {
        return $user->can('view_leave::type');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_leave::type');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, LeaveType $leaveType): bool
    {
        return $user->can('update_leave::type');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, LeaveType $leaveType): bool
    {
        return $user->can('delete_leave::type');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_leave::type');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, LeaveType $leaveType): bool
    {
        return $user->can('force_delete_leave::type');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_leave::type');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, LeaveType $leaveType): bool
    {
        return $user->can('restore_leave::type');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_leave::type');
    }
}
