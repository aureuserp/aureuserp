<?php

namespace Webkul\TimeOff\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Security\Models\User;
use Webkul\TimeOff\Models\CalendarLeave;

class CalendarLeavePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_public::holiday');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, CalendarLeave $calendarLeave): bool
    {
        return $user->can('view_public::holiday');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_public::holiday');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, CalendarLeave $calendarLeave): bool
    {
        return $user->can('update_public::holiday');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, CalendarLeave $calendarLeave): bool
    {
        return $user->can('delete_public::holiday');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_public::holiday');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, CalendarLeave $calendarLeave): bool
    {
        return $user->can('force_delete_public::holiday');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_public::holiday');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, CalendarLeave $calendarLeave): bool
    {
        return $user->can('restore_public::holiday');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_public::holiday');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, CalendarLeave $calendarLeave): bool
    {
        return $user->can('replicate_public::holiday');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_public::holiday');
    }
}
