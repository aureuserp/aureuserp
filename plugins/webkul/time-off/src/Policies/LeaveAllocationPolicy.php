<?php

namespace Webkul\TimeOff\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Security\Models\User;
use Webkul\TimeOff\Models\LeaveAllocation;

class LeaveAllocationPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_my::allocation');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, LeaveAllocation $leaveAllocation): bool
    {
        return $user->can('view_my::allocation');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_my::allocation');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, LeaveAllocation $leaveAllocation): bool
    {
        return $user->can('update_my::allocation');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, LeaveAllocation $leaveAllocation): bool
    {
        return $user->can('delete_my::allocation');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_my::allocation');
    }
}
