<?php

namespace Webkul\Invoice\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Invoice\Models\Refund;
use Webkul\Security\Models\User;

class RefundPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_refunds::refund');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Refund $refund): bool
    {
        return $user->can('view_refunds::refund');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_refunds::refund');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Refund $refund): bool
    {
        return $user->can('update_refunds::refund');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Refund $refund): bool
    {
        return $user->can('delete_refunds::refund');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_refunds::refund');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, Refund $refund): bool
    {
        return $user->can('force_delete_refunds::refund');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_refunds::refund');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, Refund $refund): bool
    {
        return $user->can('restore_refunds::refund');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_refunds::refund');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, Refund $refund): bool
    {
        return $user->can('replicate_refunds::refund');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_refunds::refund');
    }
}
