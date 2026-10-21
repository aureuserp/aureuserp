<?php

namespace Webkul\Crm\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Crm\Models\Activity;
use Webkul\Security\Models\User;

class ActivityPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_crm_activity');
    }

    public function view(User $user, Activity $activity): bool
    {
        return $user->can('view_crm_activity');
    }

    public function update(User $user, Activity $activity): bool
    {
        return $user->can('update_crm_activity');
    }

    public function delete(User $user, Activity $activity): bool
    {
        return $user->can('delete_crm_activity');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_crm_activity');
    }
}
