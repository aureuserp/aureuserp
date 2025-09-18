<?php

namespace Webkul\Inventory\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Inventory\Models\Rule;
use Webkul\Security\Models\User;

class RulePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_rule');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Rule $rule): bool
    {
        return $user->can('view_rule');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_rule');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Rule $rule): bool
    {
        return $user->can('update_rule');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Rule $rule): bool
    {
        return $user->can('delete_rule');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_rule');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, Rule $rule): bool
    {
        return $user->can('force_delete_rule');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_rule');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, Rule $rule): bool
    {
        return $user->can('restore_rule');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_rule');
    }
}
