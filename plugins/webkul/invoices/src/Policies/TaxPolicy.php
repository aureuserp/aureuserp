<?php

namespace Webkul\Invoice\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Invoice\Models\Tax;
use Webkul\Security\Models\User;

class TaxPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_taxes::tax');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Tax $tax): bool
    {
        return $user->can('view_taxes::tax');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_taxes::tax');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Tax $tax): bool
    {
        return $user->can('update_taxes::tax');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Tax $tax): bool
    {
        return $user->can('delete_taxes::tax');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_taxes::tax');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, Tax $tax): bool
    {
        return $user->can('force_delete_taxes::tax');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_taxes::tax');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, Tax $tax): bool
    {
        return $user->can('restore_taxes::tax');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_taxes::tax');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, Tax $tax): bool
    {
        return $user->can('replicate_taxes::tax');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_taxes::tax');
    }
}
