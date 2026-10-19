<?php

namespace Webkul\Crm\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Crm\Models\Activity;
use Webkul\Security\Models\User;

class ActivityPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_crm_activity');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Activity $activity): bool
    {
        return $user->can('view_crm_activity');
    }

    /**
     * Determine whether the user can update (reschedule or mark as done) the model.
     */
    public function update(User $user, Activity $activity): bool
    {
        return $user->can('update_crm_activity');
    }

    /**
     * Determine whether the user can cancel the model.
     */
    public function delete(User $user, Activity $activity): bool
    {
        return $user->can('delete_crm_activity');
    }

    /**
     * Determine whether the user can bulk cancel.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_crm_activity');
    }
}
