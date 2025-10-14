<?php

namespace Webkul\Invoice\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Invoice\Models\TaxGroup;
use Webkul\Security\Models\User;

class TaxGroupPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_tax::group');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, TaxGroup $taxGroup): bool
    {
        return $user->can('view_tax::group');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_tax::group');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, TaxGroup $taxGroup): bool
    {
        return $user->can('update_tax::group');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, TaxGroup $taxGroup): bool
    {
        return $user->can('delete_tax::group');
    }
}
