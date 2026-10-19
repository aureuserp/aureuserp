<?php

namespace Webkul\Crm\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Crm\Models\Lead;
use Webkul\Security\Models\User;

/**
 * Authorizes the CRM → Leads screen (`type = 'lead'` records) through HasCrmAuthorization.
 * Opportunities use LeadPolicy, which Laravel discovers for the Lead model.
 */
class LeadRecordPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_crm_lead');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Lead $lead): bool
    {
        return $user->can('view_crm_lead');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_crm_lead');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Lead $lead): bool
    {
        return $user->can('update_crm_lead');
    }

    /**
     * Determine whether the user can delete (mark as lost) the model.
     */
    public function delete(User $user, Lead $lead): bool
    {
        return $user->can('delete_crm_lead');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_crm_lead');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, Lead $lead): bool
    {
        return $user->can('force_delete_crm_lead');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_crm_lead');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, Lead $lead): bool
    {
        return $user->can('restore_crm_lead');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_crm_lead');
    }
}
