<?php

namespace Webkul\Partner\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Partner\Models\Title;
use Webkul\Security\Models\User;


class TitlePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_title');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Title $title): bool
    {
        return $user->can('view_title');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_title');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Title $title): bool
    {
        return $user->can('update_title');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Title $title): bool
    {
        return $user->can('delete_title');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_title');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, Title $title): bool
    {
        return $user->can('force_delete_title');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_title');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, Title $title): bool
    {
        return $user->can('restore_title');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_title');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, Title $title): bool
    {
        return $user->can('replicate_title');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_title');
    }
}
