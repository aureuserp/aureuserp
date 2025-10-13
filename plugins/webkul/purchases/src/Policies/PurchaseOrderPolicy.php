<?php

namespace Webkul\Purchase\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Purchase\Models\PurchaseOrder;
use Webkul\Security\Models\User;
use Webkul\Security\Traits\HasScopedPermissions;

class PurchaseOrderPolicy
{
    use HandlesAuthorization, HasScopedPermissions;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_purchase::order');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, PurchaseOrder $purchaseOrder): bool
    {
        return $user->can('view_purchase::order');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_purchase::order');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, PurchaseOrder $purchaseOrder): bool
    {
        if (! $user->can('update_purchase::order')) {
            return false;
        }

        return $this->hasAccess($user, $purchaseOrder);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, PurchaseOrder $purchaseOrder): bool
    {
        if (! $user->can('delete_purchase::order')) {
            return false;
        }

        return $this->hasAccess($user, $purchaseOrder);
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_purchase::order');
    }
}
