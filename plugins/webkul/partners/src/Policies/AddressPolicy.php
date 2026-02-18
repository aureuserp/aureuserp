<?php

namespace Webkul\Partner\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Partner\Models\Address;
use Webkul\Security\Models\User;

class AddressPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_partner_address');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Address $address): bool
    {
        return $user->can('view_partner_address');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_partner_address');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Address $address): bool
    {
        return $user->can('update_partner_address');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Address $address): bool
    {
        return $user->can('delete_partner_address');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_partner_address');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, Address $address): bool
    {
        return $user->can('force_delete_partner_address');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_partner_address');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, Address $address): bool
    {
        return $user->can('restore_partner_address');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_partner_address');
    }
}
