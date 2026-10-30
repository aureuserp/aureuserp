<?php

namespace Webkul\Crm\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Contact\Models\Partner;
use Webkul\Security\Models\User;

class CustomerPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_crm_customer');
    }

    public function view(User $user, Partner $partner): bool
    {
        return $user->can('view_crm_customer');
    }

    public function create(User $user): bool
    {
        return $user->can('create_crm_customer');
    }

    public function update(User $user, Partner $partner): bool
    {
        return $user->can('update_crm_customer');
    }

    public function delete(User $user, Partner $partner): bool
    {
        return $user->can('delete_crm_customer');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_crm_customer');
    }

    public function forceDelete(User $user, Partner $partner): bool
    {
        return $user->can('force_delete_crm_customer');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_crm_customer');
    }

    public function restore(User $user, Partner $partner): bool
    {
        return $user->can('restore_crm_customer');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_crm_customer');
    }
}
