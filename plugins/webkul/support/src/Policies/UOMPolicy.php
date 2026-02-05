<?php

namespace Webkul\Support\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Security\Models\User;
use Webkul\Support\Models\UOM;

class UOMPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_support_u::o::m::category');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, UOM $uom): bool
    {
        return $user->can('view_support_u::o::m::category');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_support_u::o::m::category');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, UOM $uom): bool
    {
        return $user->can('update_support_u::o::m::category');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, UOM $uom): bool
    {
        return $user->can('delete_support_u::o::m::category');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_support_u::o::m::category');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, UOM $uom): bool
    {
        return $user->can('force_delete_support_u::o::m::category');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_support_u::o::m::category');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, UOM $uom): bool
    {
        return $user->can('restore_support_u::o::m::category');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_support_u::o::m::category');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_support_u::o::m::category');
    }
}
