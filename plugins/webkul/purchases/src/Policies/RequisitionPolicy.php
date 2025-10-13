<?php

namespace Webkul\Purchase\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Purchase\Models\Requisition;
use Webkul\Security\Models\User;
use Webkul\Security\Traits\HasScopedPermissions;

class RequisitionPolicy
{
    use HandlesAuthorization, HasScopedPermissions;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_purchase::agreement');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Requisition $requisition): bool
    {
        return $user->can('view_purchase::agreement');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_purchase::agreement');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Requisition $requisition): bool
    {
        if (! $user->can('update_purchase::agreement')) {
            return false;
        }

        return $this->hasAccess($user, $requisition);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Requisition $requisition): bool
    {
        if (! $user->can('delete_purchase::agreement')) {
            return false;
        }

        return $this->hasAccess($user, $requisition);
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_purchase::agreement');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, Requisition $requisition): bool
    {
        if (! $user->can('force_delete_purchase::agreement')) {
            return false;
        }

        return $this->hasAccess($user, $requisition);
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_purchase::agreement');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, Requisition $requisition): bool
    {
        if (! $user->can('restore_purchase::agreement')) {
            return false;
        }

        return $this->hasAccess($user, $requisition);
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_purchase::agreement');
    }
}
