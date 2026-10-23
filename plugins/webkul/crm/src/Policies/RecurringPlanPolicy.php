<?php

namespace Webkul\Crm\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Crm\Models\RecurringPlan;
use Webkul\Security\Models\User;

class RecurringPlanPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_crm_recurring::plan');
    }

    public function view(User $user, RecurringPlan $recurringPlan): bool
    {
        return $user->can('view_crm_recurring::plan');
    }

    public function create(User $user): bool
    {
        return $user->can('create_crm_recurring::plan');
    }

    public function update(User $user, RecurringPlan $recurringPlan): bool
    {
        return $user->can('update_crm_recurring::plan');
    }

    public function delete(User $user, RecurringPlan $recurringPlan): bool
    {
        return $user->can('delete_crm_recurring::plan');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_crm_recurring::plan');
    }

    public function forceDelete(User $user, RecurringPlan $recurringPlan): bool
    {
        return $user->can('force_delete_crm_recurring::plan');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_crm_recurring::plan');
    }

    public function restore(User $user, RecurringPlan $recurringPlan): bool
    {
        return $user->can('restore_crm_recurring::plan');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_crm_recurring::plan');
    }

    public function reorder(User $user): bool
    {
        return $user->can('reorder_crm_recurring::plan');
    }
}
