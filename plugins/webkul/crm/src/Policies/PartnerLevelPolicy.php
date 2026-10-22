<?php

namespace Webkul\Crm\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Crm\Models\PartnerLevel;
use Webkul\Security\Models\User;

class PartnerLevelPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_crm_partner::level');
    }

    public function view(User $user, PartnerLevel $partnerLevel): bool
    {
        return $user->can('view_crm_partner::level');
    }

    public function create(User $user): bool
    {
        return $user->can('create_crm_partner::level');
    }

    public function update(User $user, PartnerLevel $partnerLevel): bool
    {
        return $user->can('update_crm_partner::level');
    }

    public function delete(User $user, PartnerLevel $partnerLevel): bool
    {
        return $user->can('delete_crm_partner::level');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_crm_partner::level');
    }

    public function forceDelete(User $user, PartnerLevel $partnerLevel): bool
    {
        return $user->can('force_delete_crm_partner::level');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_crm_partner::level');
    }

    public function restore(User $user, PartnerLevel $partnerLevel): bool
    {
        return $user->can('restore_crm_partner::level');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_crm_partner::level');
    }

    public function reorder(User $user): bool
    {
        return $user->can('reorder_crm_partner::level');
    }
}
