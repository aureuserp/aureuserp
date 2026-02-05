<?php

namespace Webkul\Inventory\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Inventory\Models\Packaging;
use Webkul\Security\Models\User;

class PackagingPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_inventory_packaging');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Packaging $packaging): bool
    {
        return $user->can('view_inventory_packaging');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_inventory_packaging');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Packaging $packaging): bool
    {
        return $user->can('update_inventory_packaging');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Packaging $packaging): bool
    {
        return $user->can('delete_inventory_packaging');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_inventory_packaging');
    }
}
