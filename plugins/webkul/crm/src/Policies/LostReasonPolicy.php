<?php

namespace Webkul\Crm\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Crm\Models\LostReason;
use Webkul\Security\Models\User;

class LostReasonPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_crm_lost::reason');
    }

    public function view(User $user, LostReason $lostReason): bool
    {
        return $user->can('view_crm_lost::reason');
    }

    public function create(User $user): bool
    {
        return $user->can('create_crm_lost::reason');
    }

    public function update(User $user, LostReason $lostReason): bool
    {
        return $user->can('update_crm_lost::reason');
    }

    public function delete(User $user, LostReason $lostReason): bool
    {
        return $user->can('delete_crm_lost::reason');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_crm_lost::reason');
    }

    public function forceDelete(User $user, LostReason $lostReason): bool
    {
        return $user->can('force_delete_crm_lost::reason');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_crm_lost::reason');
    }

    public function restore(User $user, LostReason $lostReason): bool
    {
        return $user->can('restore_crm_lost::reason');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_crm_lost::reason');
    }
}
