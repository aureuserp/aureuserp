<?php

namespace Webkul\Inventory\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Inventory\Models\OrderPoint;
use Webkul\Security\Models\User;

class OrderPointPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_replenishments::replenishment');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, OrderPoint $orderPoint): bool
    {
        return $user->can('view_replenishments::replenishment');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_replenishments::replenishment');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, OrderPoint $orderPoint): bool
    {
        return $user->can('update_replenishments::replenishment');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, OrderPoint $orderPoint): bool
    {
        return $user->can('delete_replenishments::replenishment');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_replenishments::replenishment');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, OrderPoint $orderPoint): bool
    {
        return $user->can('force_delete_replenishments::replenishment');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_replenishments::replenishment');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, OrderPoint $orderPoint): bool
    {
        return $user->can('restore_replenishments::replenishment');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_replenishments::replenishment');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, OrderPoint $orderPoint): bool
    {
        return $user->can('replicate_replenishments::replenishment');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_replenishments::replenishment');
    }
}
