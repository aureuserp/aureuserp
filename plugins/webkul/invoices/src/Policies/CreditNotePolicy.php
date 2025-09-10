<?php

namespace Webkul\Invoice\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Invoice\Models\CreditNote;
use Webkul\Security\Models\User;

class CreditNotePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_credit::notes::credit::notes');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, CreditNote $creditNote): bool
    {
        return $user->can('view_credit::notes::credit::notes');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_credit::notes::credit::notes');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, CreditNote $creditNote): bool
    {
        return $user->can('update_credit::notes::credit::notes');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, CreditNote $creditNote): bool
    {
        return $user->can('delete_credit::notes::credit::notes');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_credit::notes::credit::notes');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, CreditNote $creditNote): bool
    {
        return $user->can('force_delete_credit::notes::credit::notes');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_credit::notes::credit::notes');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, CreditNote $creditNote): bool
    {
        return $user->can('restore_credit::notes::credit::notes');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_credit::notes::credit::notes');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, CreditNote $creditNote): bool
    {
        return $user->can('replicate_credit::notes::credit::notes');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_credit::notes::credit::notes');
    }
}
