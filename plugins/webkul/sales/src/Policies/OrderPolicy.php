<?php

namespace Webkul\Sale\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Sale\Models\Order;
use Webkul\Security\Models\User;
use Webkul\Security\Traits\HasScopedPermissions;

class OrderPolicy
{
    use HandlesAuthorization, HasScopedPermissions;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_quotation');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Order $order): bool
    {
        return $user->can('view_quotation');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_quotation');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Order $order): bool
    {
        if (! $user->can('update_quotation')) {
            return false;
        }

        return $this->hasAccess($user, $order);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Order $order): bool
    {
        if (! $user->can('delete_quotation')) {
            return false;
        }

        return $this->hasAccess($user, $order);
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_quotation');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, Order $order): bool
    {
        if (! $user->can('force_delete_quotation')) {
            return false;
        }

        return $this->hasAccess($user, $order);
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_quotation');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, Order $order): bool
    {
        if (! $user->can('restore_quotation')) {
            return false;
        }

        return $this->hasAccess($user, $order);
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_quotation');
    }
}
