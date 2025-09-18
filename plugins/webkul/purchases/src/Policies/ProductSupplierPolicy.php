<?php

namespace Webkul\Purchase\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Purchase\Models\ProductSupplier;
use Webkul\Security\Models\User;

class ProductSupplierPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_vendor::price');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ProductSupplier $productSupplier): bool
    {
        return $user->can('view_vendor::price');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_vendor::price');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ProductSupplier $productSupplier): bool
    {
        return $user->can('update_vendor::price');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ProductSupplier $productSupplier): bool
    {
        return $user->can('delete_vendor::price');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_vendor::price');
    }
}
