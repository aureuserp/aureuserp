<?php

namespace Webkul\Crm\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Crm\Models\Lead;
use Webkul\Security\Models\User;
use Webkul\Security\Traits\HasScopedPermissions;

class LeadRecordPolicy
{
    use HandlesAuthorization, HasScopedPermissions;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_crm_lead');
    }

    public function view(User $user, Lead $lead): bool
    {
        return $user->can('view_crm_lead');
    }

    public function create(User $user): bool
    {
        return $user->can('create_crm_lead');
    }

    public function update(User $user, Lead $lead): bool
    {
        if (! $user->can('update_crm_lead')) {
            return false;
        }

        return $this->hasAccess($user, $lead);
    }

    public function delete(User $user, Lead $lead): bool
    {
        if (! $user->can('delete_crm_lead')) {
            return false;
        }

        return $this->hasAccess($user, $lead);
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_crm_lead');
    }

    public function forceDelete(User $user, Lead $lead): bool
    {
        if (! $user->can('force_delete_crm_lead')) {
            return false;
        }

        return $this->hasAccess($user, $lead);
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_crm_lead');
    }

    public function restore(User $user, Lead $lead): bool
    {
        if (! $user->can('restore_crm_lead')) {
            return false;
        }

        return $this->hasAccess($user, $lead);
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_crm_lead');
    }
}
