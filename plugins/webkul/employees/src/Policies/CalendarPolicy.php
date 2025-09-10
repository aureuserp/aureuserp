<?php

namespace Webkul\Employee\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Employee\Models\Calendar;
use Webkul\Security\Models\User;

class CalendarPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_calendar::calendar');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Calendar $calendar): bool
    {
        return $user->can('view_calendar::calendar');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_calendar::calendar');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Calendar $calendar): bool
    {
        return $user->can('update_calendar::calendar');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Calendar $calendar): bool
    {
        return $user->can('delete_calendar::calendar');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_calendar::calendar');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, Calendar $calendar): bool
    {
        return $user->can('force_delete_calendar::calendar');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_calendar::calendar');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, Calendar $calendar): bool
    {
        return $user->can('restore_calendar::calendar');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_calendar::calendar');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, Calendar $calendar): bool
    {
        return $user->can('replicate_calendar::calendar');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_calendar::calendar');
    }
}
