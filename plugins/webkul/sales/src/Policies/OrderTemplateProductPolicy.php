<?php

namespace Webkul\Sale\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Sale\Models\OrderTemplateProduct;
use Webkul\Security\Models\User;

class OrderTemplateProductPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_order::template::product');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, OrderTemplateProduct $orderTemplateProduct): bool
    {
        return $user->can('view_order::template::product');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_order::template::product');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, OrderTemplateProduct $orderTemplateProduct): bool
    {
        return $user->can('update_order::template::product');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, OrderTemplateProduct $orderTemplateProduct): bool
    {
        return $user->can('delete_order::template::product');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_order::template::product');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, OrderTemplateProduct $orderTemplateProduct): bool
    {
        return $user->can('force_delete_order::template::product');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_order::template::product');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, OrderTemplateProduct $orderTemplateProduct): bool
    {
        return $user->can('restore_order::template::product');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_order::template::product');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_order::template::product');
    }
}
