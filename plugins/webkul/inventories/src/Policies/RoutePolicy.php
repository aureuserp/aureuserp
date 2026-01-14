<?php

namespace Webkul\Inventory\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Inventory\Models\Route;
use Webkul\Security\Models\User;

class RoutePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_inventory_route');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Route $route): bool
    {
        return $user->can('view_inventory_route');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_inventory_route');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Route $route): bool
    {
        return $user->can('update_inventory_route');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Route $route): bool
    {
        return $user->can('delete_inventory_route');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_inventory_route');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, Route $route): bool
    {
        return $user->can('force_delete_inventory_route');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_inventory_route');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, Route $route): bool
    {
        return $user->can('restore_inventory_route');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_inventory_route');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_inventory_route');
    }
}
