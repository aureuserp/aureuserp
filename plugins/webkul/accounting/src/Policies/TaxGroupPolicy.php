<?php

namespace Webkul\Accounting\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Accounting\Models\TaxGroup;
use Webkul\Security\Models\User;

class TaxGroupPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_accounting_tax::group');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, TaxGroup $taxGroup): bool
    {
        return $user->can('view_accounting_tax::group');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_accounting_tax::group');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, TaxGroup $taxGroup): bool
    {
        return $user->can('update_accounting_tax::group');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, TaxGroup $taxGroup): bool
    {
        return $user->can('delete_accounting_tax::group');
    }
}
