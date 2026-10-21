<?php

namespace Webkul\Crm\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Crm\Models\Lead;
use Webkul\Security\Models\User;

class LeadPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_crm_opportunity');
    }

    public function view(User $user, Lead $lead): bool
    {
        return $user->can('view_crm_opportunity');
    }

    public function create(User $user): bool
    {
        return $user->can('create_crm_opportunity');
    }

    public function update(User $user, Lead $lead): bool
    {
        return $user->can('update_crm_opportunity');
    }

    public function delete(User $user, Lead $lead): bool
    {
        return $user->can('delete_crm_opportunity');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_crm_opportunity');
    }

    public function forceDelete(User $user, Lead $lead): bool
    {
        return $user->can('force_delete_crm_opportunity');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_crm_opportunity');
    }

    public function restore(User $user, Lead $lead): bool
    {
        return $user->can('restore_crm_opportunity');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_crm_opportunity');
    }
}
