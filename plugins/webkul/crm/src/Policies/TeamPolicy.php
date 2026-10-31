<?php

namespace Webkul\Crm\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Sale\Models\Team;
use Webkul\Security\Models\User;

class TeamPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_crm_team');
    }

    public function view(User $user, Team $team): bool
    {
        return $user->can('view_crm_team');
    }

    public function create(User $user): bool
    {
        return $user->can('create_crm_team');
    }

    public function update(User $user, Team $team): bool
    {
        return $user->can('update_crm_team');
    }

    public function delete(User $user, Team $team): bool
    {
        return $user->can('delete_crm_team');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_crm_team');
    }

    public function forceDelete(User $user, Team $team): bool
    {
        return $user->can('force_delete_crm_team');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_crm_team');
    }

    public function restore(User $user, Team $team): bool
    {
        return $user->can('restore_crm_team');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_crm_team');
    }

    public function reorder(User $user): bool
    {
        return $user->can('reorder_crm_team');
    }
}
