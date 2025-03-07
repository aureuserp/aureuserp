<?php

namespace Webkul\Invoice\Policies;

use Webkul\Security\Models\User;
use Webkul\Invoice\Models\Move;
use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Security\Traits\HasScopedPermissions;

class MovePolicy
{
    use HandlesAuthorization, HasScopedPermissions;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_refund');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Move $move): bool
    {
        return $user->can('view_refund');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_refund');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Move $move): bool
    {
        if (! $user->can('update_refund')) {
            return false;
        }

        return $this->hasAccess($user, $move, 'invoiceUser');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Move $move): bool
    {
        if (! $user->can('delete_refund')) {
            return false;
        }

        return $this->hasAccess($user, $move, 'invoiceUser');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_refund');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, Move $move): bool
    {
        if (! $user->can('force_delete_refund')) {
            return false;
        }

        return $this->hasAccess($user, $move, 'invoiceUser');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_refund');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, Move $move): bool
    {
        if (! $user->can('restore_refund')) {
            return false;
        }

        return $this->hasAccess($user, $move, 'invoiceUser');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_refund');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, Move $move): bool
    {
        if (! $user->can('replicate_refund')) {
            return false;
        }

        return $this->hasAccess($user, $move, 'invoiceUser');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_refund');
    }
}
