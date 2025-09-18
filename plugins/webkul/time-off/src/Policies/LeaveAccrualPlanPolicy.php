<?php

namespace Webkul\TimeOff\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Security\Models\User;
use Webkul\TimeOff\Models\LeaveAccrualPlan;

class LeaveAccrualPlanPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_accrual::plan');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, LeaveAccrualPlan $leaveAccrualPlan): bool
    {
        return $user->can('view_accrual::plan');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_accrual::plan');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, LeaveAccrualPlan $leaveAccrualPlan): bool
    {
        return $user->can('update_accrual::plan');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, LeaveAccrualPlan $leaveAccrualPlan): bool
    {
        return $user->can('delete_accrual::plan');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_accrual::plan');
    }
}
