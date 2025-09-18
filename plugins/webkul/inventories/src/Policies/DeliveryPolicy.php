<?php

namespace Webkul\Inventory\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Inventory\Models\Delivery;
use Webkul\Security\Models\User;
use Webkul\Security\Traits\HasScopedPermissions;

class DeliveryPolicy
{
    use HandlesAuthorization, HasScopedPermissions;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_delivery');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Delivery $delivery): bool
    {
        return $user->can('view_delivery');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_delivery');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Delivery $delivery): bool
    {
        if (! $user->can('update_delivery')) {
            return false;
        }

        return $this->hasAccess($user, $delivery);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Delivery $delivery): bool
    {
        if (! $user->can('delete_delivery')) {
            return false;
        }
        // dd($this->hasAccess($user, $delivery));

        return $this->hasAccess($user, $delivery);
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_delivery');
    }
}
