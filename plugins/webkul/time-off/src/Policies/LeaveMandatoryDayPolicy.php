<?php

namespace Webkul\TimeOff\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Security\Models\User;
use Webkul\TimeOff\Models\LeaveMandatoryDay;

class LeaveMandatoryDayPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_mandatory::day');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, LeaveMandatoryDay $leaveMandatoryDay): bool
    {
        return $user->can('view_mandatory::day');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_mandatory::day');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, LeaveMandatoryDay $leaveMandatoryDay): bool
    {
        return $user->can('update_mandatory::day');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, LeaveMandatoryDay $leaveMandatoryDay): bool
    {
        return $user->can('delete_mandatory::day');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_mandatory::day');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, LeaveMandatoryDay $leaveMandatoryDay): bool
    {
        return $user->can('force_delete_mandatory::day');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_mandatory::day');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, LeaveMandatoryDay $leaveMandatoryDay): bool
    {
        return $user->can('restore_mandatory::day');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_mandatory::day');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, LeaveMandatoryDay $leaveMandatoryDay): bool
    {
        return $user->can('replicate_mandatory::day');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_mandatory::day');
    }
}
