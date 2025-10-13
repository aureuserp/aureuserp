<?php

namespace Webkul\Sale\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Sale\Models\ActivityType;
use Webkul\Security\Models\User;

class ActivityTypePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_sale_activity::type');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ActivityType $activityType): bool
    {
        return $user->can('view_sale_activity::type');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_sale_activity::type');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ActivityType $activityType): bool
    {
        return $user->can('update_sale_activity::type');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ActivityType $activityType): bool
    {
        return $user->can('delete_sale_activity::type');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_sale_activity::type');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, ActivityType $activityType): bool
    {
        return $user->can('force_delete_sale_activity::type');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_sale_activity::type');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, ActivityType $activityType): bool
    {
        return $user->can('restore_sale_activity::type');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_sale_activity::type');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, ActivityType $activityType): bool
    {
        return $user->can('replicate_sale_activity::type');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_sale_activity::type');
    }
}