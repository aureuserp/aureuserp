<?php

namespace Webkul\Crm\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Crm\Models\Lead;
use Webkul\Security\Models\User;

class LeadRecordPolicy
{
    use HandlesAuthorization;

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
        return $user->can('update_crm_lead');
    }

    public function delete(User $user, Lead $lead): bool
    {
        return $user->can('delete_crm_lead');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_crm_lead');
    }

    public function forceDelete(User $user, Lead $lead): bool
    {
        return $user->can('force_delete_crm_lead');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_crm_lead');
    }

    public function restore(User $user, Lead $lead): bool
    {
        return $user->can('restore_crm_lead');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_crm_lead');
    }
}
