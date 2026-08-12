<?php

namespace Webkul\Support\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Calendar;

class CalendarPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_support_calendar');
    }

    public function view(User $user, Calendar $calendar): bool
    {
        return $user->can('view_support_calendar');
    }

    public function create(User $user): bool
    {
        return $user->can('create_support_calendar');
    }

    public function update(User $user, Calendar $calendar): bool
    {
        return $user->can('update_support_calendar');
    }

    public function delete(User $user, Calendar $calendar): bool
    {
        return $user->can('delete_support_calendar');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_support_calendar');
    }

    public function forceDelete(User $user, Calendar $calendar): bool
    {
        return $user->can('force_delete_support_calendar');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_support_calendar');
    }

    public function restore(User $user, Calendar $calendar): bool
    {
        return $user->can('restore_support_calendar');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_support_calendar');
    }
}
