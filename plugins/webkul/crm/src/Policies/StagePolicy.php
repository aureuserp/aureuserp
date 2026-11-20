<?php

namespace Webkul\Crm\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Crm\Models\Stage;
use Webkul\Security\Models\User;

class StagePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_crm_stage');
    }

    public function view(User $user, Stage $stage): bool
    {
        return $user->can('view_crm_stage');
    }

    public function create(User $user): bool
    {
        return $user->can('create_crm_stage');
    }

    public function update(User $user, Stage $stage): bool
    {
        return $user->can('update_crm_stage');
    }

    public function delete(User $user, Stage $stage): bool
    {
        return $user->can('delete_crm_stage');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_crm_stage');
    }

    public function reorder(User $user): bool
    {
        return $user->can('reorder_crm_stage');
    }
}
