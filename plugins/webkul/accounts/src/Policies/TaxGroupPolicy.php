<?php

namespace Webkul\Account\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Account\Models\TaxGroup;
use Webkul\Security\Models\User;

class TaxGroupPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_account_tax::group');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, TaxGroup $taxGroup): bool
    {
        return $user->can('view_account_tax::group');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_account_tax::group');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, TaxGroup $taxGroup): bool
    {
        return $user->can('update_account_tax::group');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, TaxGroup $taxGroup): bool
    {
        return $user->can('delete_account_tax::group');
    }
}
