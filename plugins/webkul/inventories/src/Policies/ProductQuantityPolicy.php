<?php

namespace Webkul\Inventory\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Inventory\Models\ProductQuantity;
use Webkul\Security\Models\User;

class ProductQuantityPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_quantities::quantity');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ProductQuantity $productQuantity): bool
    {
        return $user->can('view_quantities::quantity');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_quantities::quantity');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ProductQuantity $productQuantity): bool
    {
        return $user->can('update_quantities::quantity');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ProductQuantity $productQuantity): bool
    {
        return $user->can('delete_quantities::quantity');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_quantities::quantity');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, ProductQuantity $productQuantity): bool
    {
        return $user->can('force_delete_quantities::quantity');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_quantities::quantity');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, ProductQuantity $productQuantity): bool
    {
        return $user->can('restore_quantities::quantity');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_quantities::quantity');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, ProductQuantity $productQuantity): bool
    {
        return $user->can('replicate_quantities::quantity');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_quantities::quantity');
    }
}
