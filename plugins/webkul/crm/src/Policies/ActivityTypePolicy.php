<?php

namespace Webkul\Crm\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Crm\Models\ActivityType;
use Webkul\Security\Models\User;

class ActivityTypePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_crm_activity::type');
    }

    public function view(User $user, ActivityType $activityType): bool
    {
        return $user->can('view_crm_activity::type');
    }

    public function create(User $user): bool
    {
        return $user->can('create_crm_activity::type');
    }

    public function update(User $user, ActivityType $activityType): bool
    {
        return $user->can('update_crm_activity::type');
    }

    public function delete(User $user, ActivityType $activityType): bool
    {
        return $user->can('delete_crm_activity::type');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_crm_activity::type');
    }

    public function forceDelete(User $user, ActivityType $activityType): bool
    {
        return $user->can('force_delete_crm_activity::type');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_crm_activity::type');
    }

    public function restore(User $user, ActivityType $activityType): bool
    {
        return $user->can('restore_crm_activity::type');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_crm_activity::type');
    }

    public function reorder(User $user): bool
    {
        return $user->can('reorder_crm_activity::type');
    }
}
