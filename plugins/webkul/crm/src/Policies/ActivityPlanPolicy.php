<?php

namespace Webkul\Crm\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Crm\Models\ActivityPlan;
use Webkul\Security\Models\User;

class ActivityPlanPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_crm_activity::plan');
    }

    public function view(User $user, ActivityPlan $activityPlan): bool
    {
        return $user->can('view_crm_activity::plan');
    }

    public function create(User $user): bool
    {
        return $user->can('create_crm_activity::plan');
    }

    public function update(User $user, ActivityPlan $activityPlan): bool
    {
        return $user->can('update_crm_activity::plan');
    }

    public function delete(User $user, ActivityPlan $activityPlan): bool
    {
        return $user->can('delete_crm_activity::plan');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_crm_activity::plan');
    }

    public function forceDelete(User $user, ActivityPlan $activityPlan): bool
    {
        return $user->can('force_delete_crm_activity::plan');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_crm_activity::plan');
    }

    public function restore(User $user, ActivityPlan $activityPlan): bool
    {
        return $user->can('restore_crm_activity::plan');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_crm_activity::plan');
    }
}
