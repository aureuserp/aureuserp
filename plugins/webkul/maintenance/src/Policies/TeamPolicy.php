<?php

namespace Webkul\Maintenance\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Maintenance\Models\Team;
use Webkul\Security\Models\User;

class TeamPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_maintenance_team');
    }

    public function view(User $user, Team $team): bool
    {
        return $user->can('view_maintenance_team');
    }

    public function create(User $user): bool
    {
        return $user->can('create_maintenance_team');
    }

    public function update(User $user, Team $team): bool
    {
        return $user->can('update_maintenance_team');
    }

    public function delete(User $user, Team $team): bool
    {
        return $user->can('delete_maintenance_team');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_maintenance_team');
    }

    public function forceDelete(User $user, Team $team): bool
    {
        return $user->can('force_delete_maintenance_team');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_maintenance_team');
    }

    public function restore(User $user, Team $team): bool
    {
        return $user->can('restore_maintenance_team');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_maintenance_team');
    }
}
