<?php

namespace Webkul\Security\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Security\Models\User;
use Webkul\Security\Traits\HasScopedPermissions;

class UserPolicy
{
    use HandlesAuthorization, HasScopedPermissions;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_user');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $record): bool
    {
        if (! $user->can('view_user')) {
            return false;
        }

        return $this->hasAccess($user, $record, 'createdBy');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_user');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $record): bool
    {
        if (! $user->can('update_user')) {
            return false;
        }

        return $this->hasAccess($user, $record, 'createdBy');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $record): bool
    {
        if (! $user->can('delete_user')) {
            return false;
        }

        return $this->hasAccess($user, $record, 'createdBy');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_user');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, User $record): bool
    {
        if (! $user->can('force_delete_user')) {
            return false;
        }

        return $this->hasAccess($user, $record, 'createdBy');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_user');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, User $record): bool
    {
        if (! $user->can('restore_user')) {
            return false;
        }

        return $this->hasAccess($user, $record, 'createdBy');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_user');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, User $record): bool
    {
        if (! $user->can('replicate_user')) {
            return false;
        }

        return $this->hasAccess($user, $record, 'createdBy');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_user');
    }
}
