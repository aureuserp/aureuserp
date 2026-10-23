<?php

namespace Webkul\Crm\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Security\Models\Company;
use Webkul\Security\Models\User;

class CompanyPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_crm_company');
    }

    public function view(User $user, Company $company): bool
    {
        return $user->can('view_crm_company');
    }

    public function create(User $user): bool
    {
        return $user->can('create_crm_company');
    }

    public function update(User $user, Company $company): bool
    {
        return $user->can('update_crm_company');
    }

    public function delete(User $user, Company $company): bool
    {
        return $user->can('delete_crm_company');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_crm_company');
    }

    public function forceDelete(User $user, Company $company): bool
    {
        return $user->can('force_delete_crm_company');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_crm_company');
    }

    public function restore(User $user, Company $company): bool
    {
        return $user->can('restore_crm_company');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_crm_company');
    }

    public function reorder(User $user): bool
    {
        return $user->can('reorder_crm_company');
    }
}
