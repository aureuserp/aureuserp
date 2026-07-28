<?php

namespace Webkul\Maintenance\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Maintenance\Models\Stage;
use Webkul\Security\Models\User;

class StagePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_maintenance_stage');
    }

    public function view(User $user, Stage $stage): bool
    {
        return $user->can('view_maintenance_stage');
    }

    public function create(User $user): bool
    {
        return $user->can('create_maintenance_stage');
    }

    public function update(User $user, Stage $stage): bool
    {
        return $user->can('update_maintenance_stage');
    }

    public function delete(User $user, Stage $stage): bool
    {
        return $user->can('delete_maintenance_stage');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_maintenance_stage');
    }

    public function reorder(User $user): bool
    {
        return $user->can('reorder_maintenance_stage');
    }
}
