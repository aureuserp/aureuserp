<?php

namespace Webkul\Field\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Field\Models\Field;
use Webkul\Security\Models\User;

class FieldPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_fields::field');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Field $field): bool
    {
        return $user->can('view_fields::field');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_fields::field');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Field $field): bool
    {
        return $user->can('update_fields::field');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Field $field): bool
    {
        return $user->can('delete_fields::field');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_fields::field');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, Field $field): bool
    {
        return $user->can('force_delete_fields::field');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_fields::field');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, Field $field): bool
    {
        return $user->can('restore_fields::field');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_fields::field');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, Field $field): bool
    {
        return $user->can('replicate_fields::field');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_fields::field');
    }
}
