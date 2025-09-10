<?php

namespace Webkul\Inventory\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Inventory\Models\Warehouse;
use Webkul\Security\Models\User;

class WarehousePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_warehouses::warehouse');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Warehouse $warehouse): bool
    {
        return $user->can('view_warehouses::warehouse');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_warehouses::warehouse');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Warehouse $warehouse): bool
    {
        return $user->can('update_warehouses::warehouse');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Warehouse $warehouse): bool
    {
        return $user->can('delete_warehouses::warehouse');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_warehouses::warehouse');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, Warehouse $warehouse): bool
    {
        return $user->can('force_delete_warehouses::warehouse');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_warehouses::warehouse');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, Warehouse $warehouse): bool
    {
        return $user->can('restore_warehouses::warehouse');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_warehouses::warehouse');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, Warehouse $warehouse): bool
    {
        return $user->can('replicate_warehouses::warehouse');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_warehouses::warehouse');
    }
}
